<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Order extends CI_Controller {
    public function cart()
    {
        // load cart from session
        $this->load->library('session');
        $cart = $this->session->userdata('cart') ?: [];
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

        $cart = $this->session->userdata('cart') ?: [];
        // support modes: set (replace qty) or add (increment). default = add
        $mode = $this->input->post('mode', true) ?: 'add';
        $found = false;
        foreach ($cart as &$c) {
            if (isset($c['id']) && $c['id'] == $item['id']) {
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
        if (empty($pid) || $qty < 0) {
            redirect(base_url('index.php/order/cart'));
            return;
        }
        $cart = $this->session->userdata('cart') ?: [];
        foreach ($cart as $k => &$c) {
            if (isset($c['id']) && $c['id'] == (int)$pid) {
                if ($qty === 0) {
                    unset($cart[$k]);
                } else {
                    $c['qty'] = $qty;
                }
                break;
            }
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
        if (empty($pid)) { redirect(base_url('index.php/order/cart')); return; }
        $cart = $this->session->userdata('cart') ?: [];
        foreach ($cart as $k => $c) {
            if (isset($c['id']) && $c['id'] == (int)$pid) {
                unset($cart[$k]);
                break;
            }
        }
        $cart = array_values($cart);
        $this->session->set_userdata('cart', $cart);
        $count = 0; foreach ($cart as $it) { $count += isset($it['qty']) ? (int)$it['qty'] : 0; }
        $this->session->set_userdata('cart_count', $count);
        redirect(base_url('index.php/order/cart'));
    }
}