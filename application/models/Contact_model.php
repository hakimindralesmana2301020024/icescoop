<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Contact_model extends CI_Model {
    protected $table = 'contact_messages';

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function insert_message($data)
    {
        $insert = [
            'name' => $data['name'] ?? '',
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
            'subject' => $data['subject'] ?? null,
            'message' => $data['message'] ?? '',
            'status' => isset($data['status']) ? (int)$data['status'] : 0,
            'created_at' => date('Y-m-d H:i:s')
        ];
        $this->db->insert($this->table, $insert);
        return $this->db->insert_id();
    }

    public function get_messages($limit = 50, $offset = 0)
    {
        return $this->db->order_by('created_at', 'DESC')->get($this->table, (int)$limit, (int)$offset)->result_array();
    }

    public function get_by_id($id)
    {
        return $this->db->where('id', (int)$id)->get($this->table)->row_array();
    }

    public function get_unread_count()
    {
        $r = $this->db->select('COUNT(*) as cnt')->from($this->table)->where('status', 0)->get()->row_array();
        return isset($r['cnt']) ? (int)$r['cnt'] : 0;
    }

    public function mark_read($id)
    {
        return $this->db->where('id', (int)$id)->update($this->table, ['status' => 1]);
    }

    public function delete($id)
    {
        return $this->db->where('id', (int)$id)->delete($this->table);
    }
}
