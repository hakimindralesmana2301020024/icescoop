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
     * Hard-coded admin dashboard preview
     */
    public function index()
    {
        // For now this page is a hard-coded preview. In future we can add auth checks.
        $this->load->view('templates/admin_header');
        $this->load->view('admin/dashboard');
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
