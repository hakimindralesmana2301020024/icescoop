<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Product_model extends CI_Model {
    protected $table = 'products';

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function get($id)
    {
        return $this->db->get_where($this->table, ['id' => (int)$id])->row_array();
    }

    public function save($data)
    {
        $now = date('Y-m-d H:i:s');
        $row = [
            'name' => $data['name'] ?? null,
            'slug' => $data['slug'] ?? url_title($data['name'] ?? '', 'dash', true),
            // new short and long descriptions
            'short_description' => $data['short_description'] ?? ($data['description'] ?? null),
            'long_description' => $data['long_description'] ?? ($data['description'] ?? null),
            // legacy field kept for compatibility
            'description' => $data['description'] ?? null,
            'price' => isset($data['price']) ? (float)$data['price'] : null,
            'rating' => isset($data['rating']) ? (float)$data['rating'] : null,
            'image' => $data['image'] ?? null,
            'featured' => !empty($data['featured']) ? 1 : 0,
            'updated_at' => $now
        ];
        if (!empty($data['id'])) {
            $this->db->where('id', (int)$data['id'])->update($this->table, $row);
            // Always return the product id for consistent caller behavior
            return (int)$data['id'];
        }
        $row['created_at'] = $now;
        $this->db->insert($this->table, $row);
        return (int)$this->db->insert_id();
    }

    public function delete($id)
    {
        return $this->db->delete($this->table, ['id' => (int)$id]);
    }

    /**
     * Query products with filters: search(q), categories array, min_price, max_price, sort, page, per_page
     */
    public function query(array $opts = [])
    {
        $q = $this->db;
        $q->from($this->table);

        if (!empty($opts['q'])) {
            $like = '%' . $this->db->escape_like_str($opts['q']) . '%';
            $q->group_start();
            $q->like('name', $opts['q']);
            $q->or_like('description', $opts['q']);
            $q->group_end();
        }

        if (!empty($opts['categories']) && is_array($opts['categories'])) {
            // join pivot
            $q->join('product_category pc', 'pc.product_id = products.id', 'left');
            $q->where_in('pc.category_id', $opts['categories']);
            $q->group_by('products.id');
        }

        if (isset($opts['min_price']) && $opts['min_price'] !== '') $q->where('price >=', (float)$opts['min_price']);
        if (isset($opts['max_price']) && $opts['max_price'] !== '') $q->where('price <=', (float)$opts['max_price']);

        // sorting
        if (!empty($opts['sort'])) {
            switch ($opts['sort']) {
                case 'price_asc': $q->order_by('price', 'ASC'); break;
                case 'price_desc': $q->order_by('price', 'DESC'); break;
                case 'rating_desc': $q->order_by('rating', 'DESC'); break;
                case 'newest': default: $q->order_by('created_at', 'DESC'); break;
            }
        } else {
            $q->order_by('created_at', 'DESC');
        }

        $page = max(1, (int)($opts['page'] ?? 1));
        $per = max(1, (int)($opts['per_page'] ?? 12));
        $offset = ($page - 1) * $per;

        $clone = clone $q;
        $total = $clone->get()->num_rows();

        $q->limit($per, $offset);
        $rows = $q->get()->result_array();

        return ['total' => $total, 'per_page' => $per, 'page' => $page, 'rows' => $rows];
    }
}
