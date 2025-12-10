<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Blog_category_model extends CI_Model {
    protected $cat_table = 'blog_categories';
    protected $map_table = 'blog_post_categories';

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function get_all()
    {
        return $this->db->order_by('name','ASC')->get($this->cat_table)->result_array();
    }

    public function get_by_slug($slug)
    {
        return $this->db->where('slug', $slug)->get($this->cat_table)->row_array();
    }

    public function get_for_post($post_id)
    {
        return $this->db->select('c.*')
            ->from($this->cat_table.' c')
            ->join($this->map_table.' m','m.category_id = c.id')
            ->where('m.post_id', (int)$post_id)
            ->get()->result_array();
    }

    public function get_post_ids_for_category($category_id)
    {
        $rows = $this->db->select('post_id')->from($this->map_table)->where('category_id', (int)$category_id)->get()->result_array();
        return array_column($rows, 'post_id');
    }
}
