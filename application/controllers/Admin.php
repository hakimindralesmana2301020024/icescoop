<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CI_Controller {
    public function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        $this->load->helper('url');
        // Protect admin area: only allow logged-in users with role 'admin'
        $user = $this->session->userdata();
        if (empty($user) || empty($user['logged_in']) || (!isset($user['role']) || $user['role'] !== 'admin')) {
            // redirect to login and return to requested admin page after login
            $return = current_url();
            redirect(base_url('index.php/login?return=' . urlencode($return)));
            exit;
        }
    }

    /**
     * List contact messages submitted from site contact form
     */
    public function messages()
    {
        $this->load->model('Contact_model');
        $messages = $this->Contact_model->get_messages(200, 0);

        $data = ['messages' => $messages];
        $this->load->view('templates/admin_header');
        $this->load->view('admin/messages_list', $data);
        $this->load->view('templates/admin_footer');
    }

    /**
     * Mark message as read (simple GET action)
     */
    public function mark_message_read($id = 0)
    {
        $this->load->model('Contact_model');
        if ($id) $this->Contact_model->mark_read((int)$id);
        redirect(base_url('index.php/admin/messages'));
    }

    /**
     * Delete a message
     */
    public function delete_message($id = 0)
    {
        $this->load->model('Contact_model');
        if ($id) $this->Contact_model->delete((int)$id);
        redirect(base_url('index.php/admin/messages'));
    }

    /**
     * Return JSON counts for unread messages (used by sidebar polling if desired)
     */
    public function messages_count()
    {
        $this->load->model('Contact_model');
        $cnt = $this->Contact_model->get_unread_count();
        header('Content-Type: application/json');
        echo json_encode(['unread' => (int)$cnt]);
    }

    /**
     * Hard-coded admin dashboard preview
     */
    public function index()
    {
        // Load weekly trend data and render dashboard
        $this->load->model('Trend_model');
        $this->load->model('Icecream_model');

        // Weekly trend
        $weekly = $this->Trend_model->get_weekly('orders');
        $weekly_labels = array_column($weekly, 'date');
        $weekly_values = array_column($weekly, 'value');
        // fallback: if empty, create last 7 days labels with zeros
        if (empty($weekly_labels)) {
            $weekly_labels = [];
            $weekly_values = [];
            for ($i = 6; $i >= 0; $i--) {
                $d = date('Y-m-d', strtotime("-{$i} days"));
                $weekly_labels[] = $d;
                $weekly_values[] = 0;
            }
        }
        // human-friendly weekly labels (e.g. '25 Nov')
        $weekly_labels_display = array_map(function($d){ return date('d M', strtotime($d)); }, $weekly_labels);

        // If Trend_model returned only zeros (or empty), try to fallback to real orders table
        $sum_weekly_values = is_array($weekly_values) ? array_sum($weekly_values) : 0;
        if ($sum_weekly_values === 0) {
            try {
                if ($this->db->table_exists('orders')) {
                    $q = $this->db->select("DATE(created_at) AS day, COUNT(*) as cnt")->from('orders')
                        ->where('DATE(created_at) >=', date('Y-m-d', strtotime('-6 days')))
                        ->group_by('day')->order_by('day', 'ASC')->get();
                    $rows = $q->result_array();
                    $map = [];
                    foreach ($rows as $r) {
                        $map[$r['day']] = (int)$r['cnt'];
                    }
                    $weekly = [];
                    $weekly_labels = [];
                    $weekly_values = [];
                    for ($i = 6; $i >= 0; $i--) {
                        $d = date('Y-m-d', strtotime("-{$i} days"));
                        $weekly[] = ['date' => $d, 'value' => isset($map[$d]) ? $map[$d] : 0];
                        $weekly_labels[] = $d;
                        $weekly_values[] = isset($map[$d]) ? $map[$d] : 0;
                    }
                    $weekly_labels_display = array_map(function($d){ return date('d M', strtotime($d)); }, $weekly_labels);
                }
            } catch (Exception $e) {
                // ignore and keep existing zeros
            }
        }

        // If we used fallback weekly data, consider saving to `weekly_trends` so Trend_model can read next time
        try {
            if ($this->db->table_exists('weekly_trends') && is_array($weekly) && count($weekly)) {
                foreach ($weekly as $w) {
                    $date = isset($w['date']) ? $w['date'] : null;
                    $value = isset($w['value']) ? (int)$w['value'] : 0;
                    if (!$date) continue;
                    // check if an entry for this metric/date already exists
                    $exists = $this->db->select('COUNT(*) as cnt')->from('weekly_trends')->where('metric','orders')->where('DATE(created_at)', $date)->get()->row_array();
                    if (empty($exists) || empty($exists['cnt'])) {
                        // insert a new trend row for this date
                        $this->db->insert('weekly_trends', ['metric' => 'orders', 'value' => $value, 'created_at' => $date]);
                    }
                }
            }
        } catch (Exception $e) {
            // ignore DB write errors
        }

        // Monthly trend (last 12 months)
        $monthly = $this->Trend_model->get_monthly('orders');
        $monthly_labels = array_column($monthly, 'month');
        $monthly_values = array_column($monthly, 'value');
        // fallback: last 12 months labels with zeros
        if (empty($monthly_labels)) {
            $monthly_labels = [];
            $monthly_values = [];
            for ($i = 11; $i >= 0; $i--) {
                $m = date('Y-m', strtotime("-{$i} months"));
                $monthly_labels[] = $m;
                $monthly_values[] = 0;
            }
        }
        // human-friendly monthly labels (e.g. 'Nov 2025')
        $monthly_labels_display = array_map(function($m){ return date('M Y', strtotime($m . '-01')); }, $monthly_labels);

        // If monthly values are all zero, try to build from orders table grouped by month
        $sum_monthly_values = is_array($monthly_values) ? array_sum($monthly_values) : 0;
        if ($sum_monthly_values === 0) {
            try {
                if ($this->db->table_exists('orders')) {
                    $q = $this->db->select("DATE_FORMAT(created_at, '%Y-%m') AS mon, COUNT(*) as cnt")->from('orders')
                        ->where("DATE_FORMAT(created_at, '%Y-%m') >=", date('Y-m', strtotime('-11 months')))
                        ->group_by('mon')->order_by('mon','ASC')->get();
                    $rows = $q->result_array();
                    $map = [];
                    foreach ($rows as $r) {
                        $map[$r['mon']] = (int)$r['cnt'];
                    }
                    $monthly = [];
                    $monthly_labels = [];
                    $monthly_values = [];
                    for ($i = 11; $i >= 0; $i--) {
                        $d = date('Y-m', strtotime("-{$i} months"));
                        $monthly[] = ['month' => $d, 'value' => isset($map[$d]) ? $map[$d] : 0];
                        $monthly_labels[] = $d;
                        $monthly_values[] = isset($map[$d]) ? $map[$d] : 0;
                    }
                    $monthly_labels_display = array_map(function($m){ return date('M Y', strtotime($m . '-01')); }, $monthly_labels);
                }
            } catch (Exception $e) {
                // ignore
            }
        }

        // --- Daily revenue (last 30 days) - use weekly_trends 'revenue' metric if available ---
        $daily = $this->Trend_model->get_daily('revenue', 30);
        $daily_labels = array_column($daily, 'date');
        $daily_values = array_column($daily, 'value');
        // human-friendly daily labels (e.g. '04 Dec')
        $daily_labels_display = array_map(function($d){ return date('d M', strtotime($d)); }, $daily_labels);
        $daily_total = is_array($daily_values) ? array_sum($daily_values) : 0;

        // If no revenue rows in weekly_trends, fallback to calculating from orders table
        $sum_daily_values = is_array($daily_values) ? array_sum($daily_values) : 0;
        if ($sum_daily_values === 0) {
            try {
                $daily = $this->Trend_model->get_daily_from_orders(30);
                $daily_labels = array_column($daily, 'date');
                $daily_values = array_column($daily, 'value');
                $daily_labels_display = array_map(function($d){ return date('d M', strtotime($d)); }, $daily_labels);
                $daily_total = is_array($daily_values) ? array_sum($daily_values) : 0;
            } catch (Exception $e) {}
        }

        // compute daily average and delta (last vs previous day)
        $daily_avg = '-'; $daily_delta = '-'; $daily_delta_pct = null;
        if (is_array($daily_values) && count($daily_values)) {
            $days_count = count($daily_values);
            $daily_avg = (int)round(array_sum($daily_values) / max(1,$days_count));
            $last = (int)$daily_values[$days_count-1];
            $prev = $days_count>1 ? (int)$daily_values[$days_count-2] : 0;
            $daily_delta = $last - $prev;
            if ($prev !== 0) $daily_delta_pct = round((($last - $prev)/max(1,$prev)) * 100, 1);
        }

        // totals: compute from the arrays so stats match charts
        $total_week = is_array($weekly_values) ? array_sum($weekly_values) : 0;
        $total_month = is_array($monthly_values) ? array_sum($monthly_values) : 0;
        $total_featured = isset($featured_values) && is_array($featured_values) ? array_sum($featured_values) : 0;

        // Prefer showing recent week's total as 'Total Orders' if available, else fall back to stored total
        $db_total = $this->Trend_model->get_total('orders');
        $total_orders = $total_week > 0 ? $total_week : ($db_total ?: 0);

        // Daily revenue: read last 7 days for metric 'revenue' and take today's value
        $weekly_revenue = $this->Trend_model->get_weekly('revenue');
        $daily_revenue_value = 0;
        if (is_array($weekly_revenue) && count($weekly_revenue)) {
            // find today's date entry
            $today = date('Y-m-d');
            foreach ($weekly_revenue as $r) {
                if (isset($r['date']) && $r['date'] === $today) {
                    $daily_revenue_value = (int)$r['value'];
                    break;
                }
            }
            // if not found, maybe last element is the latest
            if ($daily_revenue_value === 0) {
                $last = end($weekly_revenue);
                if (isset($last['value'])) $daily_revenue_value = (int)$last['value'];
            }
        }
        $revenue = $daily_revenue_value > 0 ? ('Rp ' . number_format($daily_revenue_value, 0, ',', '.')) : '-';

        // other stats - try real queries if DB tables exist, else keep placeholders
        $active_users = '-';
        $pending_orders = '-';

        // If orders table exists, prefer real counts from DB
        try {
            if ($this->db->table_exists('orders')) {
                $tot = $this->db->select('COUNT(*) as cnt')->from('orders')->get()->row_array();
                $total_orders = isset($tot['cnt']) ? (int)$tot['cnt'] : (int)$total_orders;

                // pending statuses commonly used across admin views
                $pending_statuses = ['pending_payment', 'paid_pending_confirmation', 'pending_confirmation'];
                $pendQ = $this->db->select('COUNT(*) as cnt')->from('orders')->where_in('status', $pending_statuses)->get()->row_array();
                $pending_orders = isset($pendQ['cnt']) ? (int)$pendQ['cnt'] : 0;
            }
        } catch (Exception $e) {
            // ignore DB errors and keep placeholders
        }

        // Active users: if `users` table exists, count users created in the last 30 days as a lightweight 'active' proxy
        try {
            if ($this->db->table_exists('users')) {
                $since = date('Y-m-d H:i:s', strtotime('-30 days'));
                $this->db->where('created_at >=', $since);
                $active_users = (int)$this->db->count_all_results('users');
            }
        } catch (Exception $e) {
            // leave as placeholder
        }

        // Unread messages count (Contact_model provides helper)
        $unread_messages = 0;
        try {
            $this->load->model('Contact_model');
            $unread_messages = (int)$this->Contact_model->get_unread_count();
        } catch (Exception $e) {
            $unread_messages = 0;
        }

        // featured ice cream products (uses Icecream_model which currently returns featured list)
        $featured_products = $this->Icecream_model->get_featured();
        // featured data for pie chart (title + sales value)
        $featured_for_pie = $this->Icecream_model->get_featured_with_sales(6);
        $featured_labels = array_column($featured_for_pie, 'title');
        $featured_values = array_column($featured_for_pie, 'value');
        if (empty($featured_labels)) {
            $featured_labels = ['No data'];
            $featured_values = [0];
        }

        $data = [
            'weekly_labels' => $weekly_labels,
            'weekly_values' => $weekly_values,
            'weekly_labels_display' => $weekly_labels_display,
            'monthly_labels' => $monthly_labels,
            'monthly_values' => $monthly_values,
            'monthly_labels_display' => $monthly_labels_display,
            'total_orders' => $total_orders,
            'weekly_total' => $total_week,
            'monthly_total' => $total_month,
            'featured_total' => $total_featured,
            'revenue' => $revenue,
            'active_users' => $active_users,
            'pending_orders' => $pending_orders,
            'featured_products' => $featured_products,
            'featured_labels' => $featured_labels,
            'featured_values' => $featured_values
        ];

        // include daily revenue arrays for dashboard (30 days)
        $data['daily_labels'] = isset($daily_labels) ? $daily_labels : [];
        $data['daily_values'] = isset($daily_values) ? $daily_values : [];
        $data['daily_labels_display'] = isset($daily_labels_display) ? $daily_labels_display : [];
        $data['daily_total'] = isset($daily_total) ? $daily_total : 0;

        // include unread messages count for admin dashboard/header
        $data['unread_messages'] = isset($unread_messages) ? (int)$unread_messages : 0;

        // expose raw weekly_revenue for debug (temporary)
        $data['weekly_revenue_raw'] = isset($weekly_revenue) ? $weekly_revenue : [];

        $this->load->view('templates/admin_header');
        $this->load->view('admin/dashboard', $data);
        $this->load->view('templates/admin_footer');
    }

    /**
     * Edit About page content (GET shows form, POST saves)
     */
    public function about()
    {
        $this->load->model('Page_model');

        // create images dir if missing
        $img_dir = FCPATH . 'assets/images/about/';
        if (!is_dir($img_dir)) {
            @mkdir($img_dir, 0755, true);
        }

        if ($this->input->method() === 'post') {
            // read posted fields
            $payload = [];
            // fetch existing to allow safe deletion of previous images
            $existing = $this->Page_model->get_by_slug('about') ?: [];
            $payload['hero_title'] = $this->input->post('hero_title', true);
            $payload['journey_title'] = $this->input->post('journey_title', true);
            $payload['journey_lead1'] = $this->input->post('journey_lead1', true);
            $payload['journey_lead2'] = $this->input->post('journey_lead2', true);
            $payload['mission_title'] = $this->input->post('mission_title', true);
            $payload['mission_lead'] = $this->input->post('mission_lead', true);

            // Team members (up to 6 simple rows)
            $team = [];
            $names = $this->input->post('team_name');
            $roles = $this->input->post('team_role');
            for ($i=0;$i<6;$i++) {
                if (!empty($names[$i]) || !empty($roles[$i])) {
                    $team[$i] = [
                        'name' => isset($names[$i]) ? $this->input->post('team_name['.$i.']', true) : '',
                        'role' => isset($roles[$i]) ? $this->input->post('team_role['.$i.']', true) : '',
                        'image' => ''
                    ];
                }
            }

            // handle uploads: journey_image, mission_image, team_image_0..5
            $uploaded = [];
            if (!empty($_FILES['journey_image']) && $_FILES['journey_image']['error'] === UPLOAD_ERR_OK) {
                $tmp = $_FILES['journey_image']['tmp_name'];
                $name = 'journey_' . time() . '_' . basename($_FILES['journey_image']['name']);
                $dest = $img_dir . $name;
                if (@move_uploaded_file($tmp, $dest)) {
                    // remove previous image file if present and inside about folder
                    if (!empty($existing['journey_image'])) {
                        $prev = FCPATH . ltrim($existing['journey_image'], '/');
                        $about_base = realpath(FCPATH . 'assets/images/about/');
                        if ($about_base && strpos(realpath($prev), $about_base) === 0 && is_file($prev)) {
                            @unlink($prev);
                        }
                    }
                    $payload['journey_image'] = 'assets/images/about/' . $name;
                }
            }

            if (!empty($_FILES['mission_image']) && $_FILES['mission_image']['error'] === UPLOAD_ERR_OK) {
                $tmp = $_FILES['mission_image']['tmp_name'];
                $name = 'mission_' . time() . '_' . basename($_FILES['mission_image']['name']);
                $dest = $img_dir . $name;
                if (@move_uploaded_file($tmp, $dest)) {
                    if (!empty($existing['mission_image'])) {
                        $prev = FCPATH . ltrim($existing['mission_image'], '/');
                        $about_base = realpath(FCPATH . 'assets/images/about/');
                        if ($about_base && strpos(realpath($prev), $about_base) === 0 && is_file($prev)) {
                            @unlink($prev);
                        }
                    }
                    $payload['mission_image'] = 'assets/images/about/' . $name;
                }
            }

            // team images
            for ($i=0;$i<6;$i++) {
                $key = 'team_image_' . $i;
                if (!empty($_FILES[$key]) && $_FILES[$key]['error'] === UPLOAD_ERR_OK) {
                    $tmp = $_FILES[$key]['tmp_name'];
                    $name = 'team_' . $i . '_' . time() . '_' . basename($_FILES[$key]['name']);
                    $dest = $img_dir . $name;
                    if (@move_uploaded_file($tmp, $dest)) {
                        // delete previous team image if present
                        $existing_team = isset($existing['team']) && is_array($existing['team']) ? $existing['team'] : [];
                        if (isset($existing_team[$i]) && !empty($existing_team[$i]['image'])) {
                            $prev = FCPATH . ltrim($existing_team[$i]['image'], '/');
                            $about_base = realpath(FCPATH . 'assets/images/about/');
                            if ($about_base && strpos(realpath($prev), $about_base) === 0 && is_file($prev)) {
                                @unlink($prev);
                            }
                        }
                        if (isset($team[$i])) {
                            $team[$i]['image'] = 'assets/images/about/' . $name;
                        }
                    }
                }
            }

            $payload['team'] = array_values($team);

            // Merge with existing so empty posted fields won't destroy everything
            $existing = $this->Page_model->get_by_slug('about') ?: [];
            $merged = array_merge($existing, $payload);

            $this->Page_model->save_slug('about', $merged);
            $this->session->set_flashdata('admin_msg', 'About page updated.');
            redirect(base_url('index.php/admin/about'));
            return;
        }

        // GET -> render form
        $about = $this->Page_model->get_by_slug('about');
        $data = ['about' => $about];
        $this->load->view('templates/admin_header');
        $this->load->view('admin/about_edit', $data);
        $this->load->view('templates/admin_footer');
    }

    /**
     * Edit Home page (hero, featured items, categories, best sellers, special, testimonials)
     */
    public function home()
    {
        $this->load->model('Home_model');

        // create images dir if missing
        $img_dir = FCPATH . 'assets/images/home/';
        if (!is_dir($img_dir)) {
            @mkdir($img_dir, 0755, true);
        }

        if ($this->input->method() === 'post') {
            $payload = [];
            $existing = $this->Home_model->get() ?: [];

            // Hero
            $hero_title = $this->input->post('hero_title', true);
            $hero_subtitle = $this->input->post('hero_subtitle', true);
            $intro = $this->input->post('intro', true);
            if ($hero_title !== null) $payload['hero_title'] = $hero_title;
            if ($hero_subtitle !== null) $payload['hero_subtitle'] = $hero_subtitle;
            if ($intro !== null) $payload['intro'] = $intro;

            // Featured items (up to 6)
            $featured = [];
            $f_titles = $this->input->post('featured_title');
            $f_descs = $this->input->post('featured_desc');
            $f_prices = $this->input->post('featured_price');
            $f_ratings = $this->input->post('featured_rating');
            for ($i=0;$i<6;$i++) {
                $item = ['title'=>'','desc'=>'','price'=>'','rating'=>'','image'=>''];
                if (is_array($f_titles) && isset($f_titles[$i])) $item['title'] = $this->input->post('featured_title['.$i.']', true);
                if (is_array($f_descs) && isset($f_descs[$i])) $item['desc'] = $this->input->post('featured_desc['.$i.']', true);
                if (is_array($f_prices) && isset($f_prices[$i])) $item['price'] = $this->input->post('featured_price['.$i.']', true);
                if (is_array($f_ratings) && isset($f_ratings[$i])) $item['rating'] = $this->input->post('featured_rating['.$i.']', true);
                $featured[$i] = $item;
            }

            // Categories (up to 6)
            $categories = [];
            $c_names = $this->input->post('category_name');
            for ($i=0;$i<6;$i++) {
                $cat = ['name'=>'','image'=>''];
                if (is_array($c_names) && isset($c_names[$i])) $cat['name'] = $this->input->post('category_name['.$i.']', true);
                $categories[$i] = $cat;
            }

            // Best sellers (up to 6)
            $best = [];
            $bs_titles = $this->input->post('bs_title');
            $bs_prices = $this->input->post('bs_price');
            for ($i=0;$i<6;$i++) {
                $b = ['title'=>'','price'=>'','image'=>''];
                if (is_array($bs_titles) && isset($bs_titles[$i])) $b['title'] = $this->input->post('bs_title['.$i.']', true);
                if (is_array($bs_prices) && isset($bs_prices[$i])) $b['price'] = $this->input->post('bs_price['.$i.']', true);
                $best[$i] = $b;
            }

            // Special section
            $special = [];
            $special['title'] = $this->input->post('special_title', true);
            $special['sub'] = $this->input->post('special_sub', true);
            $special['lead'] = $this->input->post('special_lead', true);

            // Testimonials (up to 5)
            $testimonials = [];
            $t_texts = $this->input->post('test_text');
            $t_names = $this->input->post('test_name');
            $t_roles = $this->input->post('test_role');
            for ($i=0;$i<5;$i++) {
                $t = ['text'=>'','name'=>'','role'=>''];
                if (is_array($t_texts) && isset($t_texts[$i])) $t['text'] = $this->input->post('test_text['.$i.']', true);
                if (is_array($t_names) && isset($t_names[$i])) $t['name'] = $this->input->post('test_name['.$i.']', true);
                if (is_array($t_roles) && isset($t_roles[$i])) $t['role'] = $this->input->post('test_role['.$i.']', true);
                $testimonials[$i] = $t;
            }

            // Handle uploads: hero_image, featured_image_0..5, category_image_0..5, bs_image_0..5, special_image, test_image_0..4
            if (!empty($_FILES['hero_image']) && $_FILES['hero_image']['error'] === UPLOAD_ERR_OK) {
                $tmp = $_FILES['hero_image']['tmp_name'];
                $name = 'hero_' . time() . '_' . basename($_FILES['hero_image']['name']);
                $dest = $img_dir . $name;
                if (@move_uploaded_file($tmp, $dest)) {
                    if (!empty($existing['hero_image'])) {
                        $prev = FCPATH . ltrim($existing['hero_image'], '/');
                        $home_base = realpath(FCPATH . 'assets/images/home/');
                        if ($home_base && strpos(realpath($prev), $home_base) === 0 && is_file($prev)) @unlink($prev);
                    }
                    $payload['hero_image'] = 'assets/images/home/' . $name;
                }
            }

            // generic helper to process numbered file inputs
            $process_numbered = function($prefix, &$targetArray, $existingArray) use ($img_dir) {
                for ($i=0;$i<count($targetArray);$i++) {
                    $key = $prefix . $i;
                    if (!empty($_FILES[$key]) && $_FILES[$key]['error'] === UPLOAD_ERR_OK) {
                        $tmp = $_FILES[$key]['tmp_name'];
                        $name = $prefix . '_' . $i . '_' . time() . '_' . basename($_FILES[$key]['name']);
                        $dest = $img_dir . $name;
                        if (@move_uploaded_file($tmp, $dest)) {
                            if (!empty($existingArray) && isset($existingArray[$i]) && !empty($existingArray[$i]['image'])) {
                                $prev = FCPATH . ltrim($existingArray[$i]['image'], '/');
                                $home_base = realpath(FCPATH . 'assets/images/home/');
                                if ($home_base && strpos(realpath($prev), $home_base) === 0 && is_file($prev)) @unlink($prev);
                            }
                            $targetArray[$i]['image'] = 'assets/images/home/' . $name;
                        }
                    } else {
                        // preserve existing image if not replaced
                        if (!empty($existingArray) && isset($existingArray[$i]) && !empty($existingArray[$i]['image'])) {
                            $targetArray[$i]['image'] = $existingArray[$i]['image'];
                        }
                    }
                }
            };

            $existing_feat = isset($existing['featured_items']) ? $existing['featured_items'] : [];
            $process_numbered('featured_image_', $featured, $existing_feat);

            $existing_cats = isset($existing['categories']) ? $existing['categories'] : [];
            $process_numbered('category_image_', $categories, $existing_cats);

            $existing_bs = isset($existing['best_sellers']) ? $existing['best_sellers'] : [];
            $process_numbered('bs_image_', $best, $existing_bs);

            // special image
            if (!empty($_FILES['special_image']) && $_FILES['special_image']['error'] === UPLOAD_ERR_OK) {
                $tmp = $_FILES['special_image']['tmp_name'];
                $name = 'special_' . time() . '_' . basename($_FILES['special_image']['name']);
                $dest = $img_dir . $name;
                if (@move_uploaded_file($tmp, $dest)) {
                    if (!empty($existing['special']['image'])) {
                        $prev = FCPATH . ltrim($existing['special']['image'], '/');
                        $home_base = realpath(FCPATH . 'assets/images/home/');
                        if ($home_base && strpos(realpath($prev), $home_base) === 0 && is_file($prev)) @unlink($prev);
                    }
                    $special['image'] = 'assets/images/home/' . $name;
                }
            } else {
                if (!empty($existing['special']['image'])) $special['image'] = $existing['special']['image'];
            }

<<<<<<< HEAD
=======
            // QRIS image (Icescoop QR) - admin can upload store QR here
            $qris = [];
            if (!empty($_FILES['qris_image']) && $_FILES['qris_image']['error'] === UPLOAD_ERR_OK) {
                $tmp = $_FILES['qris_image']['tmp_name'];
                $name = 'qris_' . time() . '_' . basename($_FILES['qris_image']['name']);
                $dest = $img_dir . $name;
                if (@move_uploaded_file($tmp, $dest)) {
                    if (!empty($existing['qris']['image'])) {
                        $prev = FCPATH . ltrim($existing['qris']['image'], '/');
                        $home_base = realpath(FCPATH . 'assets/images/home/');
                        if ($home_base && strpos(realpath($prev), $home_base) === 0 && is_file($prev)) @unlink($prev);
                    }
                    $qris['image'] = 'assets/images/home/' . $name;
                }
            } else {
                if (!empty($existing['qris']['image'])) $qris['image'] = $existing['qris']['image'];
            }

            $payload['qris'] = $qris;

            // Relive section image (the "Relive the Sweet Memories" left image)
            $relive = [];
            if (!empty($_FILES['relive_image']) && $_FILES['relive_image']['error'] === UPLOAD_ERR_OK) {
                $tmp = $_FILES['relive_image']['tmp_name'];
                $name = 'relive_' . time() . '_' . basename($_FILES['relive_image']['name']);
                $dest = $img_dir . $name;
                if (@move_uploaded_file($tmp, $dest)) {
                    if (!empty($existing['relive']['image'])) {
                        $prev = FCPATH . ltrim($existing['relive']['image'], '/');
                        $home_base = realpath(FCPATH . 'assets/images/home/');
                        if ($home_base && strpos(realpath($prev), $home_base) === 0 && is_file($prev)) @unlink($prev);
                    }
                    $relive['image'] = 'assets/images/home/' . $name;
                }
            } else {
                if (!empty($existing['relive']['image'])) $relive['image'] = $existing['relive']['image'];
            }

            $payload['relive'] = $relive;

>>>>>>> 39c0e72 (update)
            // testimonials have no images by default, preserve existing if any
            $existing_tests = isset($existing['testimonials']) ? $existing['testimonials'] : [];
            for ($i=0;$i<count($testimonials);$i++) {
                if (!empty($existing_tests[$i]) && isset($existing_tests[$i]['photo'])) {
                    $testimonials[$i]['photo'] = $existing_tests[$i]['photo'];
                }
            }

            $payload['featured_items'] = array_values(array_filter($featured, function($v){ return !empty($v['title']) || !empty($v['desc']) || !empty($v['image']); }));
            $payload['categories'] = array_values(array_filter($categories, function($v){ return !empty($v['name']) || !empty($v['image']); }));
            $payload['best_sellers'] = array_values(array_filter($best, function($v){ return !empty($v['title']) || !empty($v['price']) || !empty($v['image']); }));
            $payload['special'] = $special;
            $payload['testimonials'] = array_values(array_filter($testimonials, function($v){ return !empty($v['text']); }));

            // features (existing small feature blocks) preserved if present in POST
            $posted_features = $this->input->post('feature_title');
            $features = [];
            if (is_array($posted_features)) {
                for ($i=0;$i<count($posted_features);$i++) {
                    $features[$i] = [
                        'title' => $this->input->post('feature_title['.$i.']', true),
                        'desc' => $this->input->post('feature_desc['.$i.']', true),
                        'image' => isset($existing['features'][$i]['image']) ? $existing['features'][$i]['image'] : ''
                    ];
                }
                $payload['features'] = $features;
            }

            // Merge and save
            $merged = array_merge($existing, $payload);
            $this->Home_model->save($merged);
            $this->session->set_flashdata('admin_msg', 'Home page updated.');
            redirect(base_url('index.php/admin/home'));
            return;
        }

        // GET -> render form
        $home = $this->Home_model->get();
        $data = ['home' => $home];
        $this->load->view('templates/admin_header');
        $this->load->view('admin/home_edit', $data);
        $this->load->view('templates/admin_footer');
    }

    /**
     * List and manage orders in admin area
     */
    public function orders()
    {
        $this->load->library('session');
        $orders = [];
        $filterStatus = $this->input->get('status', true);
        try {
            $this->load->database();
            if ($this->db->table_exists('orders')) {
                $this->db->order_by('created_at', 'DESC');
                if (!empty($filterStatus)) $this->db->where('status', $filterStatus);
                $q = $this->db->get('orders');
                foreach ($q->result_array() as $r) {
                    $r['cart'] = json_decode($r['cart'], true) ?: [];
                    $r['summary'] = json_decode($r['summary'], true) ?: [];
                    $orders[] = $r;
                }
            }
        } catch (Exception $e) {
            // ignore DB errors, fallback to session
        }

        // fallback: session orders (useful in dev when DB not present)
        if (empty($orders)) {
            // fallback: session orders (useful in dev when DB not present)
            $sessOrders = $this->session->userdata('orders') ?: [];
            if (!empty($filterStatus)) {
                foreach ($sessOrders as $s) if (!empty($s['status']) && $s['status'] === $filterStatus) $orders[] = $s;
            } else {
                $orders = $sessOrders;
            }
        }

        $this->load->view('templates/admin_header');
        $this->load->view('admin/orders', ['orders' => $orders, 'filterStatus' => $filterStatus]);
        $this->load->view('templates/admin_footer');
    }

    /**
     * Show admin order detail and allow status update / notes
     */
    public function order_detail($id = null)
    {
        if (empty($id)) { redirect(base_url('index.php/admin/orders')); return; }
        $order = null;
        try {
            $this->load->database();
            if ($this->db->table_exists('orders')) {
                $r = $this->db->where('id', $id)->get('orders')->row_array();
                if (!empty($r)) {
                    $r['cart'] = json_decode($r['cart'], true) ?: [];
                    $r['summary'] = json_decode($r['summary'], true) ?: [];
                    $order = $r;
                }
            }
        } catch (Exception $e) {}

        if (empty($order)) {
            // fallback to session orders
            $orders = $this->session->userdata('orders') ?: [];
            foreach ($orders as $o) if ((string)($o['id'] ?? '') === (string)$id) { $order = $o; break; }
        }

        if (empty($order)) { $this->session->set_flashdata('admin_msg', 'Order not found'); redirect(base_url('index.php/admin/orders')); return; }

        $this->load->view('templates/admin_header');
        $this->load->view('admin/order_detail', ['order' => $order]);
        $this->load->view('templates/admin_footer');
    }

    /**
     * Update order status and admin note (POST)
     */
    public function order_update_post()
    {
        $this->load->library('session');
        $id = $this->input->post('order_id', true);
        $status = $this->input->post('status', true);
        $note = $this->input->post('admin_note', true);

        if (empty($id)) { redirect(base_url('index.php/admin/orders')); return; }

        // persist to DB if table exists
        try {
            $this->load->database();
            if ($this->db->table_exists('orders')) {
                $upd = [];
                if ($status !== null) $upd['status'] = $status;
                if ($note !== null) $upd['admin_note'] = $note;
                if (!empty($upd)) {
                    $this->db->where('id', $id)->update('orders', $upd);
                }
            }
        } catch (Exception $e) {
            // ignore DB errors
        }

        // update session copy
        $orders = $this->session->userdata('orders') ?: [];
        foreach ($orders as &$o) {
            if ((string)($o['id'] ?? '') === (string)$id) {
                if ($status !== null) $o['status'] = $status;
                if ($note !== null) $o['admin_note'] = $note;
            }
        }
        $this->session->set_userdata('orders', $orders);
        $this->session->set_flashdata('admin_msg', 'Order updated');
        redirect(base_url('index.php/admin/order_detail/' . $id));
    }

    /**
     * API endpoint to get counts per important statuses (used by admin polling)
     */
    public function orders_count()
    {
        $this->load->helper('url');
        $counts = [
            'pending_payment' => 0,
            'paid_pending_confirmation' => 0,
            'pending_confirmation' => 0,
        ];
        try {
            $this->load->database();
            if ($this->db->table_exists('orders')) {
                $q = $this->db->select('status, COUNT(*) as cnt')->from('orders')->group_by('status')->get();
                foreach ($q->result_array() as $r) {
                    if (isset($counts[$r['status']])) $counts[$r['status']] = (int)$r['cnt'];
                }
            }
        } catch (Exception $e) {
            // fallback to session
            $sess = $this->session->userdata('orders') ?: [];
            foreach ($sess as $s) if (!empty($s['status']) && isset($counts[$s['status']])) $counts[$s['status']]++;
        }
        header('Content-Type: application/json');
        echo json_encode($counts);
        exit;
    }

    /**
     * Return JSON daily revenue data for N days (used by dashboard AJAX)
     * GET param: days (optional, default 30)
     */
    public function daily_revenue()
    {
        $days = (int)$this->input->get('days');
        if ($days <= 0) $days = 30;
        $this->load->model('Trend_model');
        $daily = $this->Trend_model->get_daily('revenue', $days);
        $values = array_column($daily, 'value');
        $labels = array_column($daily, 'date');
        $sum = array_sum($values);
        if ($sum === 0) {
            // fallback to orders
            try {
                $daily = $this->Trend_model->get_daily_from_orders($days);
                $values = array_column($daily, 'value');
                $labels = array_column($daily, 'date');
                $sum = array_sum($values);
            } catch (Exception $e) { /* ignore */ }
        }

        $labels_display = array_map(function($d){ return date('d M', strtotime($d)); }, $labels);
        $avg = count($values) ? (int)round(array_sum($values)/count($values)) : 0;
        $last = count($values) ? (int)$values[count($values)-1] : 0;
        $prev = count($values) > 1 ? (int)$values[count($values)-2] : 0;
        $delta = $last - $prev;
        $delta_pct = $prev ? round((($last - $prev)/max(1,$prev))*100,1) : null;

        header('Content-Type: application/json');
        echo json_encode([
            'labels' => $labels_display,
            'values' => $values,
            'total' => $sum,
            'avg' => $avg,
            'last' => $last,
            'prev' => $prev,
            'delta' => $delta,
            'delta_pct' => $delta_pct
        ]);
    }

    /**
     * Confirm an order (POST)
     */
    public function order_confirm_post()
    {
        $this->load->library('session');
        $id = $this->input->post('order_id', true);
        if (empty($id)) redirect(base_url('index.php/admin/orders'));
        try {
            $this->load->database();
            if ($this->db->table_exists('orders')) {
                $this->db->where('id', $id)->update('orders', ['status' => 'confirmed']);
            }
        } catch (Exception $e) {}
        // update session orders if present
        $orders = $this->session->userdata('orders') ?: [];
        foreach ($orders as &$o) {
            if ((string)($o['id'] ?? '') === (string)$id) { $o['status'] = 'confirmed'; }
        }
        $this->session->set_userdata('orders', $orders);
        $this->session->set_flashdata('admin_msg', 'Order confirmed.');
        redirect(base_url('index.php/admin/orders'));
    }

    /**
     * Cancel an order (POST)
     */
    public function order_cancel_post()
    {
        $this->load->library('session');
        $id = $this->input->post('order_id', true);
        if (empty($id)) redirect(base_url('index.php/admin/orders'));
        try {
            $this->load->database();
            if ($this->db->table_exists('orders')) {
                // set status cancelled and keep record
                $this->db->where('id', $id)->update('orders', ['status' => 'cancelled']);
            }
        } catch (Exception $e) {}
        // update session orders if present
        $orders = $this->session->userdata('orders') ?: [];
        foreach ($orders as &$o) {
            if ((string)($o['id'] ?? '') === (string)$id) { $o['status'] = 'cancelled'; }
        }
        $this->session->set_userdata('orders', $orders);
        $this->session->set_flashdata('admin_msg', 'Order cancelled.');
        redirect(base_url('index.php/admin/orders'));
    }

    public function blog()
    {
        $this->load->model('Blog_model');

        // If GET delete param present -> perform delete
        $delete_id = $this->input->get('delete');
        if (!empty($delete_id) && is_numeric($delete_id)) {
            $this->Blog_model->delete((int)$delete_id);
            $this->session->set_flashdata('blog_success', 'Post deleted.');
            redirect(base_url('index.php/admin/blog'));
            return;
        }

        // If GET edit param present -> show edit form
        $edit_id = $this->input->get('edit');
        if (!empty($edit_id) && is_numeric($edit_id) && $this->input->method() !== 'post') {
            $post = $this->Blog_model->get((int)$edit_id);
            if (!$post) {
                show_404();
                return;
            }
            $data = ['post' => $post];
            $this->load->view('templates/admin_header');
            $this->load->view('admin/blog/form', $data);
            $this->load->view('templates/admin_footer');
            return;
        }

        // If POST with update param -> handle update
        $update_id = $this->input->get('update');
        if ($this->input->method() === 'post' && !empty($update_id) && is_numeric($update_id)) {
            $id_to_update = (int)$update_id;
            $title = $this->input->post('title', true);
            $this->load->helper('url');
            $slug = $this->input->post('slug', true) ?: url_title($title,'dash',true);
            $excerpt = $this->input->post('excerpt', true);
            $content_html = $this->input->post('content_html', false);
            $content_delta = $this->input->post('content_delta', false);
            $status = $this->input->post('status', true) ?: 'draft';
            $is_featured = $this->input->post('is_featured') ? 1 : 0;

            // ensure unique slug
            $base_slug = $slug;
            $i = 1;
            while ($this->Blog_model->slug_exists($slug, $id_to_update)) {
                $slug = $base_slug . '-' . $i++;
            }

            $update = [
                'title' => $title,
                'slug' => $slug,
                'excerpt' => $excerpt,
                'content_html' => $content_html,
                'content_delta' => $content_delta,
                'status' => $status,
                'is_featured' => $is_featured,
                'updated_at' => date('Y-m-d H:i:s')
            ];
            // Handle featured image upload on update (Admin proxy)
            if (!empty($_FILES) && isset($_FILES['featured_image'])) {
                $fileErr = $_FILES['featured_image']['error'];
                if ($fileErr === UPLOAD_ERR_OK) {
                    $up = $_FILES['featured_image'];
                    $tmp = $up['tmp_name'];
                    $orig = $up['name'];
                    $ext = pathinfo($orig, PATHINFO_EXTENSION);
                    $safe = preg_replace('/[^a-z0-9\-_.]/i', '-', pathinfo($orig, PATHINFO_FILENAME));
                    $filename = time() . '_' . $safe . ($ext ? '.' . $ext : '');
                    $destDir = rtrim(FCPATH, '\\/') . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR;
                    if (!is_dir($destDir)) @mkdir($destDir, 0755, true);
                    $dest = $destDir . $filename;
                    $i = 1;
                    while (file_exists($dest)) {
                        $filename = time() . '_' . $safe . '-' . $i . ($ext ? '.' . $ext : '');
                        $dest = $destDir . $filename;
                        $i++;
                    }
                    if (is_uploaded_file($tmp) && @move_uploaded_file($tmp, $dest)) {
                        $update['featured_image'] = $filename;
                        log_message('debug', '[admin/blog proxy update] uploaded featured_image=' . $filename . ' for post id=' . $id_to_update);
                    } else {
                        $msg = 'Failed to move uploaded file to ' . $dest;
                        log_message('error', '[admin/blog proxy update] ' . $msg);
                        $this->session->set_flashdata('blog_error', 'Cover image upload failed: unable to save file.');
                    }
                } else {
                    log_message('error', '[admin/blog proxy update] upload error code=' . $fileErr);
                    $this->session->set_flashdata('blog_error', 'Cover image upload failed (upload error code ' . $fileErr . ').');
                }
            }
            $this->Blog_model->update($id_to_update, $update);
            $dberr = $this->db->error();
            $affected = $this->db->affected_rows();
            // If no featured_image provided during edit, try extract from content_html (data-URI or local asset)
            if (empty($update['featured_image']) && !empty($update['content_html'])) {
                if (preg_match('/<img[^>]+src=["\']?([^"\' >]+)["\']?[^>]*>/i', $update['content_html'], $m)) {
                    $src = $m[1];
                    // data URI
                    if (strpos($src, 'data:') === 0 && preg_match('/^data:([^;]+);base64,(.+)$/', $src, $d)) {
                        $mime = $d[1];
                        $b64 = $d[2];
                        $ext = '';
                        switch (strtolower($mime)) {
                            case 'image/jpeg': $ext = 'jpg'; break;
                            case 'image/jpg': $ext = 'jpg'; break;
                            case 'image/png': $ext = 'png'; break;
                            case 'image/gif': $ext = 'gif'; break;
                            case 'image/webp': $ext = 'webp'; break;
                            default:
                                $parts = explode('/', $mime);
                                $ext = isset($parts[1]) ? preg_replace('/[^a-z0-9]/', '', $parts[1]) : 'bin';
                        }
                        $basename = 'cover_' . $id_to_update . '_' . time() . '.' . $ext;
                        $destDir = rtrim(FCPATH, '\\/') . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR;
                        if (!is_dir($destDir)) @mkdir($destDir, 0755, true);
                        $dest = $destDir . $basename;
                        $decoded = base64_decode($b64);
                        if ($decoded !== false && @file_put_contents($dest, $decoded) !== false) {
                            $this->Blog_model->update($id_to_update, ['featured_image' => $basename]);
                            log_message('debug', '[admin/blog proxy update] saved data-URI as ' . $basename . ' for id=' . $id_to_update);
                        }
                    } elseif (strpos($src, '/assets/images/') !== false) {
                        $basename = basename($src);
                        if ($basename) {
                            $this->Blog_model->update($id_to_update, ['featured_image' => $basename]);
                            log_message('debug', '[admin/blog proxy update] set existing image ' . $basename . ' for id=' . $id_to_update);
                        }
                    } else {
                        $basename = basename($src);
                        if ($basename) {
                            $this->Blog_model->update($id_to_update, ['featured_image' => $basename]);
                            log_message('debug', '[admin/blog proxy update] set relative image ' . $basename . ' for id=' . $id_to_update);
                        }
                    }
                }
            }
            if ($affected !== 0) {
                $this->session->set_flashdata('blog_success', 'Post updated.');
                redirect(base_url('index.php/admin/blog'));
                return;
            }
            $msg = isset($dberr['message']) && $dberr['message'] ? $dberr['message'] : 'Unknown DB error or 0 affected rows';
            $data = ['post' => $this->Blog_model->get($id_to_update), 'error_message' => 'Failed to update: ' . $msg];
            $this->load->view('templates/admin_header');
            $this->load->view('admin/blog/form', $data);
            echo '<div style="color:#b00; padding:10px;">Debug: ' . htmlspecialchars($data['error_message']) . '</div>';
            $this->load->view('templates/admin_footer');
            return;
        }

        // If POST, handle create (proxy) so inline form submissions are accepted
        if ($this->input->method() === 'post') {
            $title = $this->input->post('title', true);
            $this->load->helper('url');
            $slug = $this->input->post('slug', true) ?: url_title($title,'dash',true);
            $excerpt = $this->input->post('excerpt', true);
            $content_html = $this->input->post('content_html', false);
            $content_delta = $this->input->post('content_delta', false);
            $status = $this->input->post('status', true) ?: 'draft';
            $is_featured = $this->input->post('is_featured') ? 1 : 0;
            $author_id = $this->session->userdata('id') ?: NULL;

            // ensure unique slug
            $base_slug = $slug;
            $i = 1;
            while ($this->Blog_model->slug_exists($slug)) {
                $slug = $base_slug . '-' . $i++;
            }

            $insert = [
                'title' => $title,
                'slug' => $slug,
                'excerpt' => $excerpt,
                'content_html' => $content_html,
                'content_delta' => $content_delta,
                'featured_image' => null,
                'author_id' => $author_id,
                'status' => $status,
                'is_featured' => $is_featured
            ];
            // Handle featured image upload on create (Admin proxy)
            if (!empty($_FILES) && isset($_FILES['featured_image'])) {
                $fileErr = $_FILES['featured_image']['error'];
                if ($fileErr === UPLOAD_ERR_OK) {
                    $up = $_FILES['featured_image'];
                    $tmp = $up['tmp_name'];
                    $orig = $up['name'];
                    $ext = pathinfo($orig, PATHINFO_EXTENSION);
                    $safe = preg_replace('/[^a-z0-9\-_.]/i', '-', pathinfo($orig, PATHINFO_FILENAME));
                    $filename = time() . '_' . $safe . ($ext ? '.' . $ext : '');
                    $destDir = rtrim(FCPATH, '\\/') . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR;
                    if (!is_dir($destDir)) @mkdir($destDir, 0755, true);
                    $dest = $destDir . $filename;
                    $i = 1;
                    while (file_exists($dest)) {
                        $filename = time() . '_' . $safe . '-' . $i . ($ext ? '.' . $ext : '');
                        $dest = $destDir . $filename;
                        $i++;
                    }
                    if (is_uploaded_file($tmp) && @move_uploaded_file($tmp, $dest)) {
                        $insert['featured_image'] = $filename;
                        log_message('debug', '[admin/blog proxy create] uploaded featured_image=' . $filename);
                    } else {
                        $msg = 'Failed to move uploaded file to ' . $dest;
                        log_message('error', '[admin/blog proxy create] ' . $msg);
                        $this->session->set_flashdata('blog_error', 'Cover image upload failed: unable to save file.');
                    }
                } else {
                    log_message('error', '[admin/blog proxy create] upload error code=' . $fileErr);
                    $this->session->set_flashdata('blog_error', 'Cover image upload failed (upload error code ' . $fileErr . ').');
                }
            }
            $id = $this->Blog_model->insert($insert);
            $dberr = $this->db->error();
            $affected = $this->db->affected_rows();
            if ($id && $affected !== 0) {
                // If no featured_image was set via upload, try extracting from content_html
                if (empty($insert['featured_image']) && !empty($insert['content_html'])) {
                    if (preg_match('/<img[^>]+src=["\']?([^"\' >]+)["\']?[^>]*>/i', $insert['content_html'], $m)) {
                        $src = $m[1];
                        if (strpos($src, 'data:') === 0 && preg_match('/^data:([^;]+);base64,(.+)$/', $src, $d)) {
                            $mime = $d[1];
                            $b64 = $d[2];
                            $ext = '';
                            switch (strtolower($mime)) {
                                case 'image/jpeg': $ext = 'jpg'; break;
                                case 'image/jpg': $ext = 'jpg'; break;
                                case 'image/png': $ext = 'png'; break;
                                case 'image/gif': $ext = 'gif'; break;
                                case 'image/webp': $ext = 'webp'; break;
                                default:
                                    $parts = explode('/', $mime);
                                    $ext = isset($parts[1]) ? preg_replace('/[^a-z0-9]/', '', $parts[1]) : 'bin';
                            }
                            $basename = 'cover_' . $id . '_' . time() . '.' . $ext;
                            $destDir = rtrim(FCPATH, '\\/') . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR;
                            if (!is_dir($destDir)) @mkdir($destDir, 0755, true);
                            $dest = $destDir . $basename;
                            $decoded = base64_decode($b64);
                            if ($decoded !== false && @file_put_contents($dest, $decoded) !== false) {
                                $this->Blog_model->update($id, ['featured_image' => $basename]);
                                log_message('debug', '[admin/blog proxy create] saved data-URI as ' . $basename . ' for id=' . $id);
                            }
                        } elseif (strpos($src, '/assets/images/') !== false) {
                            $basename = basename($src);
                            if ($basename) {
                                $this->Blog_model->update($id, ['featured_image' => $basename]);
                                log_message('debug', '[admin/blog proxy create] set existing image ' . $basename . ' for id=' . $id);
                            }
                        } else {
                            $basename = basename($src);
                            if ($basename) {
                                $this->Blog_model->update($id, ['featured_image' => $basename]);
                                log_message('debug', '[admin/blog proxy create] set relative image ' . $basename . ' for id=' . $id);
                            }
                        }
                    }
                }
                $this->session->set_flashdata('blog_success', 'Post saved successfully.');
                redirect(base_url('index.php/admin/blog'));
                return;
            }

            $msg = isset($dberr['message']) && $dberr['message'] ? $dberr['message'] : 'Unknown DB error or 0 affected rows';
            $data = [];
            $data['post'] = [
                'title' => $title,
                'slug' => $slug,
                'excerpt' => $excerpt,
                'content_html' => $content_html,
                'content_delta' => $content_delta,
                'status' => $status,
                'is_featured' => $is_featured
            ];
            $data['error_message'] = 'Failed to save post: ' . $msg;
            $this->load->view('templates/admin_header');
            $this->load->view('admin/blog/form', $data);
            echo '<div style="color:#b00; padding:10px;">Debug: ' . htmlspecialchars($data['error_message']) . '</div>';
            $this->load->view('templates/admin_footer');
            return;
        }

        $data['posts'] = $this->Blog_model->get_all();
        $this->load->view('templates/admin_header');
        $this->load->view('admin/blog/index', $data);
        $this->load->view('templates/admin_footer');
    }

    /**
     * Admin: list and manage menu products
     */
    public function menu()
    {
        $this->load->model('Product_model');
        $this->load->model('Category_model');

        // list products for admin
        $products = $this->db->order_by('created_at','DESC')->get('products')->result_array();
        foreach ($products as &$p) {
            $p['img_url'] = !empty($p['image']) ? base_url($p['image']) : base_url('assets/images/placeholder.svg');
        }

        $data = ['products' => $products, 'categories' => $this->Category_model->all()];
        $this->load->view('templates/admin_header');
        $this->load->view('admin/menu_list', $data);
        $this->load->view('templates/admin_footer');
    }

    public function menu_edit($id = null)
    {
        $this->load->model('Product_model');
        $this->load->model('Category_model');

        $img_dir = FCPATH . 'assets/images/products/';
        if (!is_dir($img_dir)) @mkdir($img_dir, 0755, true);

        if ($this->input->method() === 'post') {
            $payload = [];
            $payload['id'] = $this->input->post('id');
            $payload['name'] = $this->input->post('name', true);
            $payload['description'] = $this->input->post('description', true);
            // new separate fields for card and detail descriptions
            $payload['short_description'] = $this->input->post('short_description', true);
            $payload['long_description'] = $this->input->post('long_description', true);
            $payload['price'] = $this->input->post('price', true);
            $payload['rating'] = $this->input->post('rating', true);
            $payload['featured'] = $this->input->post('featured') ? 1 : 0;

            // handle image upload
            if (!empty($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $tmp = $_FILES['image']['tmp_name'];
                $name = 'prod_' . time() . '_' . basename($_FILES['image']['name']);
                $dest = $img_dir . $name;
                if (@move_uploaded_file($tmp, $dest)) {
                    $payload['image'] = 'assets/images/products/' . $name;
                }
            } else {
                // preserve existing image if editing
                $existing = !empty($payload['id']) ? $this->Product_model->get($payload['id']) : [];
                if (!empty($existing) && empty($payload['image'])) $payload['image'] = $existing['image'] ?? null;
            }

            $saved = $this->Product_model->save($payload);
            // Determine the product id (save returns id for insert/update)
            $prod_id = !empty($payload['id']) ? (int)$payload['id'] : (int)$saved;

            // update category links: remove old, insert new
            $cats = $this->input->post('categories');
            $this->db->where('product_id', $prod_id)->delete('product_category');
            if (is_array($cats)) {
                foreach ($cats as $cid) {
                    $this->db->insert('product_category', ['product_id' => $prod_id, 'category_id' => (int)$cid]);
                }
            }

            $this->session->set_flashdata('admin_msg', 'Product saved.');
            redirect(base_url('index.php/admin/menu'));
            return;
        }

        $product = [];
        $selected = [];
        if (!empty($id)) {
            $product = $this->Product_model->get($id);
            $pc = $this->db->get_where('product_category', ['product_id' => (int)$id])->result_array();
            foreach ($pc as $r) $selected[] = $r['category_id'];
        }

        $data = ['product' => $product, 'categories' => $this->Category_model->all(), 'selected' => $selected];
        $this->load->view('templates/admin_header');
        $this->load->view('admin/menu_form', $data);
        $this->load->view('templates/admin_footer');
    }

    public function menu_delete($id)
    {
        $this->load->model('Product_model');
        $prod = $this->Product_model->get($id);
        if ($prod && !empty($prod['image'])) {
            $prev = FCPATH . ltrim($prod['image'], '/');
            if (is_file($prev)) @unlink($prev);
        }
        $this->db->delete('product_category', ['product_id' => (int)$id]);
        $this->Product_model->delete($id);
        $this->session->set_flashdata('admin_msg', 'Product deleted.');
        redirect(base_url('index.php/admin/menu'));
    }
}
