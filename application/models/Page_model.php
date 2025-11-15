<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Page_model extends CI_Model {

    protected $table = 'pages';

    public function __construct()
    {
        parent::__construct();
        // Make sure database is loaded (controller may not have loaded it yet)
        if (!isset($this->db) || $this->db === null) {
            $this->load->database();
        }
        // ensure pages table exists
        $this->ensure_table();
    }

    // Ensure pages table exists (simple JSON-backed page storage)
    protected function ensure_table()
    {
        // escape table name safely; fall back if db methods unavailable
        $table_name = isset($this->db) ? $this->db->escape_str($this->table) : $this->table;
        $sql = "CREATE TABLE IF NOT EXISTS `".$table_name."` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `slug` varchar(100) NOT NULL,
            `data` longtext NOT NULL,
            `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
            `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `slug` (`slug`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
        $this->db->query($sql);
    }

    public function get_by_slug($slug)
    {
        $q = $this->db->get_where($this->table, ['slug' => $slug], 1);
        if ($q->num_rows() === 0) return null;
        $row = $q->row();
        $data = json_decode($row->data, true);
        if (!is_array($data)) $data = [];
        $data['_id'] = $row->id;
        $data['_slug'] = $row->slug;
        return $data;
    }

    public function save_slug($slug, array $data)
    {
        $payload = json_encode($data, JSON_UNESCAPED_UNICODE);
        $now = date('Y-m-d H:i:s');
        $exists = $this->db->get_where($this->table, ['slug' => $slug], 1)->num_rows() > 0;
        if ($exists) {
            $this->db->where('slug', $slug);
            return $this->db->update($this->table, ['data' => $payload, 'updated_at' => $now]);
        } else {
            return $this->db->insert($this->table, ['slug' => $slug, 'data' => $payload, 'created_at' => $now, 'updated_at' => $now]);
        }
    }

    public function delete_by_slug($slug)
    {
        return $this->db->delete($this->table, ['slug' => $slug]);
    }
}
