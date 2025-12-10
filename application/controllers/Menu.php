<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Menu extends CI_Controller {
    public function index()
    {
        // Use Product and Category models for dynamic content
        $this->load->model('Product_model');
        $this->load->model('Category_model');

        // gather filter params from GET
        $q = $this->input->get('q', true);
        $page = (int)$this->input->get('page') ?: 1;
        // default 6 items per page
        $per = (int)$this->input->get('per_page') ?: 6;
        $sort = $this->input->get('sort', true);
        $min_price = $this->input->get('min_price', true);
        $max_price = $this->input->get('max_price', true);
        $cat = $this->input->get('cat'); // expected as array of ids
        $view = $this->input->get('view', true) ?: 'grid';

        $opts = ['q' => $q, 'page' => $page, 'per_page' => $per, 'sort' => $sort, 'min_price' => $min_price, 'max_price' => $max_price];
        if (is_array($cat)) $opts['categories'] = array_map('intval', $cat);

        $res = $this->Product_model->query($opts);
        $products = $res['rows'];

        // format product image urls
        foreach ($products as &$p) {
            // normalize field names expected by the existing view
            if (empty($p['image'])) {
                $p['image'] = 'assets/images/placeholder.svg';
            }
            $p['img'] = base_url($p['image']);
            // map descriptions: short for cards, long for detail; fall back to legacy 'description'
            $p['short_desc'] = $p['short_description'] ?? $p['description'] ?? '';
            $p['long_desc'] = $p['long_description'] ?? $p['description'] ?? '';
            // ensure name, price, rating exist as strings
            $p['name'] = $p['name'] ?? '';
            $p['price'] = isset($p['price']) ? (string)$p['price'] : '';
            $p['rating'] = isset($p['rating']) ? (string)$p['rating'] : '';
        }

        $categories = $this->Category_model->all();
        // mark active categories if filters provided
        foreach ($categories as &$c) {
            $c['active'] = (!empty($opts['categories']) && in_array($c['id'], $opts['categories']));
        }

        // featured products (limit 4)
        $fopts = ['page' => 1, 'per_page' => 4, 'sort' => 'rating_desc'];
        $fopts['featured'] = 1;
        // quick featured fetch
        $fres = $this->db->order_by('rating','DESC')->get_where('products', ['featured' => 1], 4)->result_array();
        $featured = array_map(function($r){ return ['name'=>$r['name'],'price'=>$r['price']]; }, $fres);

        $this->load->view('templates/header');
        $this->load->view('menu/index', [
            'products' => $products,
            'categories' => $categories,
            'featured' => $featured,
            'pagination' => ['total' => $res['total'], 'page' => $res['page'], 'per_page' => $res['per_page']],
            'view_mode' => $view
        ]);
        $this->load->view('templates/footer');
    }
public function menudetail($id = null) {
    $this->load->model('Product_model');
    $product = null;
    if (!empty($id) && is_numeric($id)) {
        $product = $this->Product_model->get((int)$id);
        if ($product) {
            if (empty($product['image'])) $product['image'] = 'assets/images/placeholder.svg';
            $product['img_url'] = base_url($product['image']);
            if (!isset($product['desc'])) $product['desc'] = $product['description'] ?? '';
            // load size variants for this product (product_sizes joined with sizes)
            try {
                $this->load->database();
                if ($this->db->table_exists('product_sizes')) {
                    $rows = $this->db->select('ps.id as ps_id, ps.price as price, s.id as size_id, s.label as label, s.slug as slug')
                        ->from('product_sizes ps')
                        ->join('sizes s', 's.id = ps.size_id', 'left')
                        ->where('ps.product_id', (int)$id)
                        ->order_by('ps.id', 'ASC')
                        ->get()->result_array();
                    if (!empty($rows)) $product['sizes'] = $rows;
                }
            } catch (Exception $e) {
                // ignore DB errors
            }
        }
    }

    $this->load->view('templates/header');
    $this->load->view('menu/menudetail', ['product' => $product]);
    $this->load->view('templates/footer');
}
}