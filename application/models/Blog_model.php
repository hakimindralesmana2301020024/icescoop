<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Blog_model extends CI_Model {
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function get_all($limit = NULL, $offset = 0)
    {
        if ($limit) $this->db->limit($limit, $offset);
        $this->db->order_by('created_at', 'DESC');
        $query = $this->db->get('blogs');
        return $query->result_array();
    }

    public function get($id_or_slug)
    {
        if (is_numeric($id_or_slug)) {
            $this->db->where('id', (int)$id_or_slug);
        } else {
            $this->db->where('slug', $id_or_slug);
        }
        $q = $this->db->get('blogs');
        return $q->row_array();
    }

    public function insert($data)
    {
        $data['created_at'] = isset($data['created_at']) ? $data['created_at'] : date('Y-m-d H:i:s');
        $this->db->insert('blogs', $data);
        return $this->db->insert_id();
    }

    public function update($id, $data)
    {
        $this->db->where('id', (int)$id);
        return $this->db->update('blogs', $data);
    }

    public function delete($id)
    {
        $this->db->where('id', (int)$id);
        return $this->db->delete('blogs');
    }

    public function slug_exists($slug, $exclude_id = NULL)
    {
        $this->db->where('slug', $slug);
        if ($exclude_id) $this->db->where('id !=', (int)$exclude_id);
        $q = $this->db->get('blogs');
        return $q->num_rows() > 0;
    }
}
