<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Order extends CI_Controller {
    public function cart()
    {
        // load cart from session
        $this->load->library('session');
        $cart = $this->session->userdata('cart') ?: [];
        // If user is logged in and orders are persisted in DB with user_id, prefer DB-backed orders
        $ci =& get_instance();
        $is_logged = $this->session->userdata('logged_in') ?: false;
        if ($is_logged) {
            try {
                $this->load->database();
                if ($this->db->table_exists('orders')) {
                    // check if user_id column exists
                    $uCol = $this->db->query("SHOW COLUMNS FROM `orders` LIKE 'user_id'")->row_array();
                    if (!empty($uCol)) {
                        $userId = $this->session->userdata('user_id');
                        if (!empty($userId)) {
                            $q = $this->db->where('user_id', $userId)->order_by('created_at', 'DESC')->get('orders');
                            $dbOrders = [];
                            foreach ($q->result_array() as $r) {
                                $dbOrders[] = [
                                    'id' => $r['id'],
                                    'customer_name' => $r['customer_name'],
                                    'customer_phone' => $r['customer_phone'],
                                    'customer_address' => $r['customer_address'],
                                    'payment_method' => $r['payment_method'],
                                    'cart' => json_decode($r['cart'], true) ?: [],
                                    'summary' => json_decode($r['summary'], true) ?: [],
                                    'status' => $r['status'],
                                    'proof_image' => $r['proof_image'],
                                    'created_at' => $r['created_at']
                                ];
                            }
                            if (!empty($dbOrders)) {
                                $this->session->set_userdata('orders', $dbOrders);
                                // set last_order if not set
                                if (empty($this->session->userdata('last_order'))) {
                                    $this->session->set_userdata('last_order', $dbOrders[0]);
                                }
                            }
                        }
                    }
                }
            } catch (Exception $e) {
                // ignore DB read errors here
            }
        }
        // update cart_count in session for header badge
        $count = 0; foreach ($cart as $it) { $count += isset($it['qty']) ? (int)$it['qty'] : 0; }
        $this->session->set_userdata('cart_count', $count);

        // compute summary totals
        $subtotal = 0.0;
        foreach ($cart as &$item) {
            $item_price = isset($item['price']) ? (float)$item['price'] : 0.0;
            $item_qty = isset($item['qty']) ? (int)$item['qty'] : 0;
            // store numeric total (avoid formatting here) so views can format consistently
            $item['total'] = $item_price * $item_qty;
            $subtotal += $item_price * $item_qty;
        }
        // Remove flat shipping by default (display-only shipping removed from view earlier)
        $shipping = 0.0;
        $total = $subtotal + $shipping;
        $summary = [
            'subtotal' => $subtotal,
            'shipping' => $shipping,
            'total' => $total,
        ];
        $data = ['cart' => $cart, 'summary' => $summary];
        $this->load->view('templates/header');
        $this->load->view('order/cart', $data);
        $this->load->view('templates/footer');
    }

    public function checkout()
    {
        // Load cart from session and compute numeric summary (reuse same logic as cart())
        $this->load->library('session');
        $cart = $this->session->userdata('cart') ?: [];

        $subtotal = 0.0;
        foreach ($cart as &$item) {
            $item_price = isset($item['price']) ? (float)$item['price'] : 0.0;
            $item_qty = isset($item['qty']) ? (int)$item['qty'] : 0;
            $item['total'] = $item_price * $item_qty;
            $subtotal += $item_price * $item_qty;
        }
        $shipping = 0.0;
        $total = $subtotal + $shipping;
        $summary = [
            'subtotal' => $subtotal,
            'shipping' => $shipping,
            'total' => $total,
        ];

        $data = [
            'cart' => $cart,
            'summary' => $summary
        ];

        $this->load->view('templates/header');
        $this->load->view('order/checkout', $data);
        $this->load->view('templates/footer');
    }

    // Add product to cart (POST)
    public function add()
    {
        $this->load->library('session');
        $this->load->helper('url');
        // Require user to be logged in before adding to cart
        $is_logged = $this->session->userdata('logged_in') ?: false;
        if (!$is_logged) {
            // remember where the user came from and send them to login
            $return = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : base_url('index.php/menu');
            $msg = 'You must be logged in to add items to cart.';
            $this->session->set_flashdata('order_error', $msg);
            $this->session->set_flashdata('error', $msg);
            redirect(base_url('index.php/login?return=' . urlencode($return)));
            return;
        }
        $pid = $this->input->post('product_id');
        $qty = (int)$this->input->post('qty');
        if (empty($pid) || $qty < 1) {
            // invalid request, redirect back to menu
            redirect(base_url('index.php/menu'));
            return;
        }
        $this->load->model('Product_model');
        $product = $this->Product_model->get((int)$pid);
        if (!$product) {
            redirect(base_url('index.php/menu'));
            return;
        }

        // prepare cart item (store numeric price to avoid formatting/parsing issues)
        $item = [
            'id' => (int)$product['id'],
            'name' => $product['name'],
            'img' => !empty($product['image']) ? base_url($product['image']) : base_url('assets/images/placeholder.svg'),
            'price' => (float)$product['price'],
            'qty' => $qty,
        ];

        // handle size selection (supports numeric product_sizes.id or short labels from hardcoded fallback)
        $sizePost = $this->input->post('size_id', true);
        if (!empty($sizePost)) {
            $item['product_size_id'] = null; // product_sizes.id if available
            $item['size_id'] = null; // sizes.id
            $item['size_label'] = null;
            try {
                $this->load->database();
                if ($this->db->table_exists('product_sizes') && is_numeric($sizePost)) {
                    $row = $this->db->select('ps.id as ps_id, ps.price as price, s.id as size_id, s.label as label')
                        ->from('product_sizes ps')
                        ->join('sizes s', 's.id = ps.size_id', 'left')
                        ->where('ps.id', (int)$sizePost)
                        ->where('ps.product_id', (int)$pid)
                        ->get()->row_array();
                    if (!empty($row)) {
                        $item['product_size_id'] = (int)$row['ps_id'];
                        $item['size_id'] = isset($row['size_id']) ? (int)$row['size_id'] : null;
                        $item['size_label'] = $row['label'] ?? null;
                        $item['price'] = (float)$row['price'];
                    }
                }
                if (empty($item['size_label']) && $this->db->table_exists('sizes')) {
                    $code = strtolower((string)$sizePost);
                    $srow = $this->db->select('id,label,slug')->from('sizes')
                        ->where('slug', $code)
                        ->or_where('label', $sizePost)
                        ->get()->row_array();
                    if (!empty($srow)) {
                        $item['size_id'] = (int)$srow['id'];
                        $item['size_label'] = $srow['label'];
                    }
                }
            } catch (Exception $e) {
                // ignore DB lookup errors; fallback below
            }
            if (empty($item['size_label'])) {
                $map = ['s' => 'Small', 'm' => 'Medium', 'l' => 'Large'];
                $k = strtolower(substr((string)$sizePost, 0, 1));
                $item['size_label'] = $map[$k] ?? (string)$sizePost;
            }
        }

        $cart = $this->session->userdata('cart') ?: [];
        // support modes: set (replace qty) or add (increment). default = add
        $mode = $this->input->post('mode', true) ?: 'add';
        $found = false;
        foreach ($cart as &$c) {
            // match by product id and size (if present) so different sizes create separate cart rows
            $sameProduct = isset($c['id']) && $c['id'] == $item['id'];
            $sameSize = true;
            if (!empty($item['product_size_id']) || !empty($c['product_size_id'])) {
                $sameSize = (!empty($c['product_size_id']) && !empty($item['product_size_id']) && $c['product_size_id'] == $item['product_size_id']);
            } elseif (!empty($item['size_label']) || !empty($c['size_label'])) {
                $sameSize = (string)($c['size_label'] ?? '') === (string)($item['size_label'] ?? '');
            }
            if ($sameProduct && $sameSize) {
                if ($mode === 'set') {
                    $c['qty'] = $item['qty'];
                } else {
                    $c['qty'] = (int)$c['qty'] + $item['qty'];
                }
                $found = true;
                break;
            }
        }
        if (!$found) {
            $cart[] = $item;
        }
        $this->session->set_userdata('cart', $cart);

        // update cart_count in session for header badge
        $count = 0; foreach ($cart as $it) { $count += isset($it['qty']) ? (int)$it['qty'] : 0; }
        $this->session->set_userdata('cart_count', $count);

        // redirect to cart page after adding
        redirect(base_url('index.php/order/cart'));
    }

    // Update cart item quantity (POST)
    public function update()
    {
        $this->load->library('session');
        $pid = $this->input->post('product_id');
        $qty = (int)$this->input->post('qty');
        $psid = $this->input->post('product_size_id', true);
        if (empty($pid) || $qty < 0) {
            redirect(base_url('index.php/order/cart'));
            return;
        }
        $cart = $this->session->userdata('cart') ?: [];
        foreach ($cart as $k => &$c) {
            if (!(isset($c['id']) && $c['id'] == (int)$pid)) continue;
            // if product_size_id provided, ensure it matches cart row
            if (!empty($psid)) {
                $match = false;
                if (!empty($c['product_size_id']) && (string)$c['product_size_id'] === (string)$psid) $match = true;
                if (!$match && !empty($c['size_label']) && (string)$c['size_label'] === (string)$psid) $match = true;
                if (!$match) continue;
            }
            if ($qty === 0) {
                unset($cart[$k]);
            } else {
                $c['qty'] = $qty;
            }
            break;
        }
        // reindex
        $cart = array_values($cart);
        $this->session->set_userdata('cart', $cart);
        // update cart_count
        $count = 0; foreach ($cart as $it) { $count += isset($it['qty']) ? (int)$it['qty'] : 0; }
        $this->session->set_userdata('cart_count', $count);
        redirect(base_url('index.php/order/cart'));
    }

    // Remove item from cart (POST)
    public function remove()
    {
        $this->load->library('session');
        $pid = $this->input->post('product_id');
        $psid = $this->input->post('product_size_id', true);
        if (empty($pid)) { redirect(base_url('index.php/order/cart')); return; }
        $cart = $this->session->userdata('cart') ?: [];
        foreach ($cart as $k => $c) {
            if (!(isset($c['id']) && $c['id'] == (int)$pid)) continue;
            if (!empty($psid)) {
                $match = false;
                if (!empty($c['product_size_id']) && (string)$c['product_size_id'] === (string)$psid) $match = true;
                if (!$match && !empty($c['size_label']) && (string)$c['size_label'] === (string)$psid) $match = true;
                if (!$match) continue;
            }
            unset($cart[$k]);
            break;
        }
        $cart = array_values($cart);
        $this->session->set_userdata('cart', $cart);
        $count = 0; foreach ($cart as $it) { $count += isset($it['qty']) ? (int)$it['qty'] : 0; }
        $this->session->set_userdata('cart_count', $count);
        redirect(base_url('index.php/order/cart'));
    }

    // Place order (handles checkout POST)
    public function place()
    {
        $this->load->library('session');
        $this->load->helper('url');
        if ($this->input->method() !== 'post') {
            redirect(base_url('index.php/order/checkout'));
            return;
        }

        $cart = $this->session->userdata('cart') ?: [];
        if (empty($cart)) {
            $this->session->set_flashdata('order_error', 'Your cart is empty.');
            redirect(base_url('index.php/menu'));
            return;
        }

        $name = trim($this->input->post('customer_name', true));
        $phone = trim($this->input->post('customer_phone', true));
        $address = trim($this->input->post('customer_address', true));
        $pay = $this->input->post('pay', true) ?: 'qris';

        // basic validation
        if ($name === '' || $phone === '' || $address === '') {
            $this->session->set_flashdata('order_error', 'Please fill name, phone and address.');
            redirect(base_url('index.php/order/checkout'));
            return;
        }

        // compute totals
        $subtotal = 0.0;
        foreach ($cart as &$item) {
            $price = isset($item['price']) ? (float)$item['price'] : 0.0;
            $qty = isset($item['qty']) ? (int)$item['qty'] : 0;
            $item['total'] = $price * $qty;
            $subtotal += $item['total'];
        }
        $total = $subtotal; // no shipping by default

        // create simple order record in session (in real app save to DB)
        $status = 'confirmed';
        if ($pay === 'qris') {
            $status = 'pending_payment';
        } elseif ($pay === 'cod' || $pay === 'COD') {
            // COD orders need an explicit user confirmation step (no proof upload)
            $status = 'pending_confirmation';
        }

        $order = [
            'id' => time(),
            'customer_name' => $name,
            'customer_phone' => $phone,
            'customer_address' => $address,
            'payment_method' => $pay,
            'cart' => $cart,
            'summary' => ['subtotal' => $subtotal, 'total' => $total],
            'status' => $status,
            'proof_image' => null,
            'created_at' => date('Y-m-d H:i:s')
        ];

        // if single-item order, expose its size on the order root for convenience
        if (is_array($cart) && count($cart) === 1) {
            $first = $cart[0];
            if (!empty($first['size_id'])) $order['size_id'] = $first['size_id'];
            if (!empty($first['product_size_id'])) $order['product_size_id'] = $first['product_size_id'];
            if (!empty($first['size_label'])) $order['size_label'] = $first['size_label'];
        }

        // append to orders list in session
        $orders = $this->session->userdata('orders') ?: [];
        $orders[] = $order;
        $this->session->set_userdata('orders', $orders);

        // set last_order for display
        $this->session->set_userdata('last_order', $order);

        // clear cart
        $this->session->unset_userdata('cart');
        $this->session->set_userdata('cart_count', 0);

        // If an `orders` DB table exists, try to persist the order there as well.
        try {
            $this->load->database();
            if ($this->db->table_exists('orders')) {
                    // Build DB row; detect if `id` is AUTO_INCREMENT and whether `user_id` column exists
                    $dbRow = [
                        'customer_name' => $order['customer_name'],
                        'customer_phone' => $order['customer_phone'],
                        'customer_address' => $order['customer_address'],
                        'payment_method' => $order['payment_method'],
                        'cart' => json_encode($order['cart']),
                        'summary' => json_encode($order['summary']),
                        'status' => $order['status'],
                        'proof_image' => $order['proof_image'],
                        'created_at' => $order['created_at']
                    ];

                    // If order contains exactly one cart item, surface its size info as top-level columns
                    if (is_array($order['cart']) && count($order['cart']) === 1) {
                        $firstItem = $order['cart'][0];
                        if (!empty($firstItem['size_id'])) {
                            $dbRow['size_id'] = $firstItem['size_id'];
                        }
                        if (!empty($firstItem['size_label'])) {
                            $dbRow['size_label'] = $firstItem['size_label'];
                        }
                    }

                    // check id column extra properties
                    $idIsAuto = false;
                    try {
                        $col = $this->db->query("SHOW COLUMNS FROM `orders` LIKE 'id'")->row_array();
                        if (!empty($col) && stripos($col['Extra'] ?? '', 'auto_increment') !== false) {
                            $idIsAuto = true;
                        }
                    } catch (Exception $e) {
                        // ignore
                    }

                    // If table does not use AUTO_INCREMENT for id, include our generated id
                    if (!$idIsAuto) {
                        $dbRow['id'] = $order['id'];
                    }

                    // Attach user_id when available and column exists
                    $userId = $this->session->userdata('user_id') ?: null;
                    if (!empty($userId)) {
                        try {
                            $uCol = $this->db->query("SHOW COLUMNS FROM `orders` LIKE 'user_id'")->row_array();
                            if (!empty($uCol)) {
                                $dbRow['user_id'] = $userId;
                            }
                        } catch (Exception $e) {
                            // ignore
                        }
                    }

                    // Try insert and log detailed info on failure
                    $res = $this->db->insert('orders', $dbRow);
                    $affected = $this->db->affected_rows();
                    $dberr = $this->db->error();
                    log_message('debug', 'Order::place DB insert res=' . var_export($res, true) . ' affected=' . $affected . ' error=' . json_encode($dberr) . ' query=' . $this->db->last_query());

                    if ($res === false || $affected === 0) {
                        $this->session->set_flashdata('order_error', 'Order saved to session but failed to persist to DB. Check application logs for DB error.');
                    }

                    // If DB assigned an auto-increment id, update session order id to match
                    $insertId = (int)$this->db->insert_id();
                    if ($insertId > 0) {
                        $orders = $this->session->userdata('orders') ?: [];
                        $lastIndex = count($orders) - 1;
                        if ($lastIndex >= 0) {
                            $orders[$lastIndex]['id'] = $insertId;
                            $this->session->set_userdata('orders', $orders);
                            $this->session->set_userdata('last_order', $orders[$lastIndex]);
                            $order['id'] = $insertId;
                        }
                    }
            } else {
                log_message('debug', 'Order::place orders table does not exist');
            }
        } catch (Exception $e) {
            log_message('error', 'Order::place DB error: ' . $e->getMessage());
            $this->session->set_flashdata('order_error', 'Order saved to session but DB write failed (see logs)');
        }

        // After attempting DB persistence, if payment is QRIS redirect to QR upload page
        if ($pay === 'qris') {
            redirect(base_url('index.php/order/qris/' . $order['id']));
            return;
        }

        // If COD, send user to a confirmation page (no proof upload required)
        if ($pay === 'cod' || $pay === 'COD') {
            redirect(base_url('index.php/order/confirm/' . $order['id']));
            return;
        }

        // otherwise redirect to success page
        redirect(base_url('index.php/order/success'));
    }

    /**
     * Show COD confirmation page (no proof upload). User confirms to finalize COD order.
     */
    public function confirm($order_id = null)
    {
        $this->load->library('session');
        $orders = $this->session->userdata('orders') ?: [];
        $found = null;
        foreach ($orders as $o) {
            if ((string)$o['id'] === (string)$order_id) { $found = $o; break; }
        }
        if (!$found) {
            $found = $this->session->userdata('last_order') ?: null;
        }

        $data = ['order' => $found];
        $this->load->view('templates/header');
        $this->load->view('order/confirm', $data);
        $this->load->view('templates/footer');
    }

    /**
     * Handle COD confirmation post. Marks order as confirmed.
     */
    public function confirm_post()
    {
        $this->load->library('session');
        $this->load->helper('url');
        if ($this->input->method() !== 'post') {
            redirect(base_url('index.php/menu'));
            return;
        }
        $order_id = $this->input->post('order_id');
        if (empty($order_id)) {
            $this->session->set_flashdata('order_error', 'Missing order id');
            redirect(base_url('index.php/menu'));
            return;
        }
        $orders = $this->session->userdata('orders') ?: [];
        $foundIndex = null;
        foreach ($orders as $i => $o) {
            if ((string)$o['id'] === (string)$order_id) { $foundIndex = $i; break; }
        }
        if ($foundIndex === null) {
            $this->session->set_flashdata('order_error', 'Order not found');
            redirect(base_url('index.php/menu'));
            return;
        }

        // mark as confirmed
        $orders[$foundIndex]['status'] = 'confirmed';
        $this->session->set_userdata('orders', $orders);
        $this->session->set_userdata('last_order', $orders[$foundIndex]);

        // update DB row status if table exists
        try {
            $this->load->database();
            if ($this->db->table_exists('orders')) {
                $this->db->where('id', $order_id)->update('orders', ['status' => 'confirmed']);
            }
        } catch (Exception $e) {
            // ignore
        }

        redirect(base_url('index.php/order/success'));
    }

    public function success()
    {
        $this->load->library('session');
        $order = $this->session->userdata('last_order') ?: null;
        $data = ['order' => $order];
        $this->load->view('templates/header');
        $this->load->view('order/success', $data);
        $this->load->view('templates/footer');
    }

    /**
     * Show QRIS payment page for an order and allow proof upload
     */
    public function qris($order_id = null)
    {
        $this->load->library('session');
        $orders = $this->session->userdata('orders') ?: [];
        $found = null;
        foreach ($orders as $o) {
            if ((string)$o['id'] === (string)$order_id) { $found = $o; break; }
        }
        if (!$found) {
            // fallback: last_order
            $found = $this->session->userdata('last_order') ?: null;
        }
        // load home config to get qris image if present
        $this->load->model('Home_model');
        $home = $this->Home_model->get();
        $qris_img = isset($home['qris']['image']) ? $home['qris']['image'] : null;
        $data = ['order' => $found, 'qris_image' => $qris_img];
        $this->load->view('templates/header');
        $this->load->view('order/qris', $data);
        $this->load->view('templates/footer');
    }

    /**
     * Show full order detail page for a placed order (frontend user view)
     */
    public function detail($order_id = null)
    {
        $this->load->library('session');
        $found = null;
        $orders = $this->session->userdata('orders') ?: [];
        foreach ($orders as $o) {
            if ((string)$o['id'] === (string)$order_id) { $found = $o; break; }
        }

        // If not found in session, try to load from DB (if orders table exists)
        if (!$found) {
            try {
                $this->load->database();
                if ($this->db->table_exists('orders') && !empty($order_id)) {
                    $r = $this->db->where('id', $order_id)->get('orders')->row_array();
                    if (!empty($r)) {
                        $found = [
                            'id' => $r['id'],
                            'customer_name' => $r['customer_name'],
                            'customer_phone' => $r['customer_phone'],
                            'customer_address' => $r['customer_address'],
                            'payment_method' => $r['payment_method'],
                            'cart' => json_decode($r['cart'], true) ?: [],
                            'summary' => json_decode($r['summary'], true) ?: [],
                            'status' => $r['status'],
                            'proof_image' => $r['proof_image'],
                            'created_at' => $r['created_at']
                        ];
                    }
                }
            } catch (Exception $e) {
                // ignore DB errors
            }
        }

        if (!$found) {
            $this->session->set_flashdata('order_error', 'Order not found');
            redirect(base_url('index.php/order/cart'));
            return;
        }

        $data = ['order' => $found];
        $this->load->view('templates/header');
        $this->load->view('order/detail', $data);
        $this->load->view('templates/footer');
    }

    /**
     * Upload QRIS payment proof for the order
     */
    public function upload_proof()
    {
        $this->load->library('session');
        $this->load->helper('url');
        $order_id = $this->input->post('order_id');
        if (empty($order_id) || empty($_FILES['proof']) ) {
            $this->session->set_flashdata('order_error', 'Missing order or proof file');
            redirect(base_url('index.php/order/checkout'));
            return;
        }

        $orders = $this->session->userdata('orders') ?: [];
        $foundIndex = null;
        foreach ($orders as $i => $o) {
            if ((string)$o['id'] === (string)$order_id) { $foundIndex = $i; break; }
        }
        if ($foundIndex === null) {
            $this->session->set_flashdata('order_error', 'Order not found');
            redirect(base_url('index.php/order/checkout'));
            return;
        }

        // save upload
        $uploadDir = FCPATH . 'assets/images/orders/';
        if (!is_dir($uploadDir)) @mkdir($uploadDir, 0755, true);
        $up = $_FILES['proof'];
        if ($up['error'] !== UPLOAD_ERR_OK) {
            $this->session->set_flashdata('order_error', 'Upload failed');
            redirect(base_url('index.php/order/qris/' . $order_id));
            return;
        }
        $tmp = $up['tmp_name'];
        $name = 'proof_' . $order_id . '_' . time() . '_' . basename($up['name']);
        $dest = $uploadDir . $name;
        if (!@move_uploaded_file($tmp, $dest)) {
            $this->session->set_flashdata('order_error', 'Failed to save proof');
            redirect(base_url('index.php/order/qris/' . $order_id));
            return;
        }

        // attach to order and mark status
        $orders[$foundIndex]['proof_image'] = 'assets/images/orders/' . $name;
        $orders[$foundIndex]['status'] = 'paid_pending_confirmation';
        $this->session->set_userdata('orders', $orders);
        $this->session->set_userdata('last_order', $orders[$foundIndex]);

        // Update DB row if table exists
        try {
            $this->load->database();
            if ($this->db->table_exists('orders')) {
                $this->db->where('id', $order_id)->update('orders', [
                    'proof_image' => $orders[$foundIndex]['proof_image'],
                    'status' => $orders[$foundIndex]['status']
                ]);
            }
        } catch (Exception $e) {
            // ignore
        }

        redirect(base_url('index.php/order/success'));
    }

    /**
     * Cancel an order stored in session. Also attempts to delete DB row and proof file if present.
     */
    public function cancel_post()
    {
        $this->load->library('session');
        $this->load->helper('url');
        if ($this->input->method() !== 'post') {
            redirect(base_url('index.php/order/cart'));
            return;
        }

        $order_id = $this->input->post('order_id');
        if (empty($order_id)) {
            $this->session->set_flashdata('order_error', 'Missing order id');
            redirect(base_url('index.php/order/cart'));
            return;
        }

        $orders = $this->session->userdata('orders') ?: [];
        $foundIndex = null;
        foreach ($orders as $i => $o) {
            if ((string)$o['id'] === (string)$order_id) { $foundIndex = $i; break; }
        }
        if ($foundIndex === null) {
            $this->session->set_flashdata('order_error', 'Order not found');
            redirect(base_url('index.php/order/cart'));
            return;
        }

        // delete proof image file if exists
        $proof = $orders[$foundIndex]['proof_image'] ?? null;
        if (!empty($proof)) {
            $path = FCPATH . ltrim($proof, '/\\');
            if (file_exists($path)) @unlink($path);
        }

        // attempt to delete DB row if orders table exists
        try {
            $this->load->database();
            if ($this->db->table_exists('orders')) {
                $this->db->where('id', $order_id)->delete('orders');
            }
        } catch (Exception $e) {
            // ignore DB errors for now
        }

        // remove from session
        array_splice($orders, $foundIndex, 1);
        $this->session->set_userdata('orders', $orders);

        // clear last_order if it was the cancelled one
        $last = $this->session->userdata('last_order') ?: null;
        if ($last && (string)($last['id'] ?? '') === (string)$order_id) {
            $this->session->unset_userdata('last_order');
        }

        $this->session->set_flashdata('order_success', 'Order cancelled');
        redirect(base_url('index.php/order/cart'));
    }

    /**
     * Clear all placed orders from session (and DB/files if present).
     * POST only.
     */
    public function clear_all_post()
    {
        $this->load->library('session');
        $this->load->helper('url');
        if ($this->input->method() !== 'post') {
            redirect(base_url('index.php/order/cart'));
            return;
        }

        $orders = $this->session->userdata('orders') ?: [];

        // remove any proof files
        foreach ($orders as $o) {
            if (!empty($o['proof_image'])) {
                $path = FCPATH . ltrim($o['proof_image'], '/\\');
                if (file_exists($path)) @unlink($path);
            }
            // attempt DB delete if table exists
            try {
                $this->load->database();
                if ($this->db->table_exists('orders') && !empty($o['id'])) {
                    $this->db->where('id', $o['id'])->delete('orders');
                }
            } catch (Exception $e) {
                // ignore DB errors
            }
        }

        // clear session orders
        $this->session->unset_userdata('orders');
        $this->session->unset_userdata('last_order');

        $this->session->set_flashdata('order_success', 'All placed orders cleared');
        redirect(base_url('index.php/order/cart'));
    }
}