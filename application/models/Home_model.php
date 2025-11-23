<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home_model extends CI_Model {

    protected $table = 'home';

    public function __construct()
    {
        parent::__construct();
        if (!isset($this->db) || $this->db === null) {
            $this->load->database();
        }
        $this->ensure_table();
    }

    protected function ensure_table()
    {
        $table = $this->table;
        $sql = "CREATE TABLE IF NOT EXISTS `" . $table . "` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `hero_title` varchar(255) DEFAULT NULL,
            `hero_subtitle` varchar(255) DEFAULT NULL,
            `intro` text,
            `hero_image` varchar(255) DEFAULT NULL,
            `features` longtext DEFAULT NULL,
            `featured_items` longtext DEFAULT NULL,
            `categories` longtext DEFAULT NULL,
            `best_sellers` longtext DEFAULT NULL,
            `special` longtext DEFAULT NULL,
            `testimonials` longtext DEFAULT NULL,
            `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
            `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
        $this->db->query($sql);
    }

    public function get()
    {
        $q = $this->db->get($this->table, 1);
        if ($q->num_rows() === 0) {
            // fallback to pages.slug='home' if present
            if ($this->db->table_exists('pages')) {
                $p = $this->db->get_where('pages', ['slug' => 'home'], 1);
                if ($p && $p->num_rows() > 0) {
                    $prow = $p->row();
                    $pdata = json_decode($prow->data, true);
                    if (is_array($pdata) && !empty($pdata)) {
                        $m = [];
                        $m['hero_title'] = isset($pdata['hero_title']) ? $pdata['hero_title'] : (isset($pdata['title']) ? $pdata['title'] : null);
                        $m['hero_subtitle'] = isset($pdata['hero_subtitle']) ? $pdata['hero_subtitle'] : null;
                        $m['intro'] = isset($pdata['intro']) ? $pdata['intro'] : null;
                        $m['hero_image'] = isset($pdata['hero_image']) ? $pdata['hero_image'] : null;
                        $m['features'] = isset($pdata['features']) && is_array($pdata['features']) ? $pdata['features'] : [];
                        $m['featured_items'] = isset($pdata['featured_items']) ? $pdata['featured_items'] : [];
                        $m['categories'] = isset($pdata['categories']) ? $pdata['categories'] : [];
                        $m['best_sellers'] = isset($pdata['best_sellers']) ? $pdata['best_sellers'] : [];
                        $m['special'] = isset($pdata['special']) ? $pdata['special'] : [];
                        $m['testimonials'] = isset($pdata['testimonials']) ? $pdata['testimonials'] : [];
                        $this->save($m);
                        return $m;
                    }
                }
            }
            return [];
        }
        $row = $q->row_array();
        $data = [];
        $data['hero_title'] = $row['hero_title'];
        $data['hero_subtitle'] = $row['hero_subtitle'];
        $data['intro'] = $row['intro'];
        $data['hero_image'] = $row['hero_image'];
        $data['features'] = json_decode($row['features'], true) ?: [];
        $data['featured_items'] = json_decode($row['featured_items'], true) ?: [];
        $data['categories'] = json_decode($row['categories'], true) ?: [];
        $data['best_sellers'] = json_decode($row['best_sellers'], true) ?: [];
        $data['special'] = json_decode($row['special'], true) ?: [];
        $data['testimonials'] = json_decode($row['testimonials'], true) ?: [];
        $data['_id'] = $row['id'];
        return $data;
    }

    public function save(array $data)
    {
        $now = date('Y-m-d H:i:s');
        $dbrow = [
            'hero_title' => isset($data['hero_title']) ? $data['hero_title'] : null,
            'hero_subtitle' => isset($data['hero_subtitle']) ? $data['hero_subtitle'] : null,
            'intro' => isset($data['intro']) ? $data['intro'] : null,
            'hero_image' => isset($data['hero_image']) ? $data['hero_image'] : null,
            'features' => isset($data['features']) ? json_encode($data['features'], JSON_UNESCAPED_UNICODE) : null,
            'featured_items' => isset($data['featured_items']) ? json_encode($data['featured_items'], JSON_UNESCAPED_UNICODE) : null,
            'categories' => isset($data['categories']) ? json_encode($data['categories'], JSON_UNESCAPED_UNICODE) : null,
            'best_sellers' => isset($data['best_sellers']) ? json_encode($data['best_sellers'], JSON_UNESCAPED_UNICODE) : null,
            'special' => isset($data['special']) ? json_encode($data['special'], JSON_UNESCAPED_UNICODE) : null,
            'testimonials' => isset($data['testimonials']) ? json_encode($data['testimonials'], JSON_UNESCAPED_UNICODE) : null,
            'updated_at' => $now
        ];

        $exists = $this->db->count_all_results($this->table) > 0;
        if ($exists) {
            $this->db->limit(1);
            $this->db->update($this->table, $dbrow);
            return $this->db->affected_rows();
        } else {
            $dbrow['created_at'] = $now;
            $this->db->insert($this->table, $dbrow);
            return $this->db->insert_id();
        }
    }
}
