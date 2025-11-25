<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Category_model extends CI_Model {
    protected $table = 'categories';

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function all()
    {
        return $this->db->order_by('name','ASC')->get($this->table)->result_array();
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
            'updated_at' => $now
        ];
        if (!empty($data['id'])) {
            $this->db->where('id', (int)$data['id'])->update($this->table, $row);
            return $this->db->affected_rows();
        }
        $row['created_at'] = $now;
        $this->db->insert($this->table, $row);
        return $this->db->insert_id();
    }

    public function delete($id)
    {
        return $this->db->delete($this->table, ['id' => (int)$id]);
    }
}
