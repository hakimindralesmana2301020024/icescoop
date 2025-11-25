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

        // generate slug if not provided
        if (empty($data['slug']) && !empty($data['title'])) {
            $data['slug'] = $this->create_unique_slug($data['title']);
        } elseif (!empty($data['slug'])) {
            // sanitize provided slug and ensure uniqueness
            $this->load->helper('url');
            $data['slug'] = url_title($data['slug'], 'dash', true);
            if ($this->slug_exists($data['slug'])) {
                $data['slug'] = $this->create_unique_slug($data['slug']);
            }
        }

        $this->db->insert('blogs', $data);
        return $this->db->insert_id();
    }

    public function update($id, $data)
    {
        // If slug not provided but title is updated, regenerate unique slug
        if (empty($data['slug']) && !empty($data['title'])) {
            $data['slug'] = $this->create_unique_slug($data['title'], $id);
        } elseif (!empty($data['slug'])) {
            // sanitize provided slug and ensure uniqueness (exclude current id)
            $this->load->helper('url');
            $data['slug'] = url_title($data['slug'], 'dash', true);
            if ($this->slug_exists($data['slug'], $id)) {
                $data['slug'] = $this->create_unique_slug($data['slug'], $id);
            }
        }

        $this->db->where('id', (int)$id);
        return $this->db->update('blogs', $data);
    }

    public function delete($id)
    {
        $this->db->where('id', (int)$id);
        return $this->db->delete('blogs');
    }

    public function create_unique_slug($text, $exclude_id = null)
    {
        $this->load->helper('url');
        $slug = url_title($text, 'dash', true);
        $base = $slug;
        $i = 0;
        while ($this->slug_exists($slug, $exclude_id)) {
            $i++;
            $slug = $base . '-' . $i;
        }
        return $slug;
    }

    public function slug_exists($slug, $exclude_id = NULL)
    {
        $this->db->where('slug', $slug);
        if ($exclude_id) $this->db->where('id !=', (int)$exclude_id);
        $q = $this->db->get('blogs');
        return $q->num_rows() > 0;
    }
}
