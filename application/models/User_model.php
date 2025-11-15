<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Get user by email
     */
    public function get_by_email($email)
    {
        return $this->db->get_where('users', ['email' => $email])->row_array();
    }

    /**
     * Get user by id
     */
    public function get_by_id($id)
    {
        return $this->db->get_where('users', ['id' => $id])->row_array();
    }

    /**
     * Create new user. $data must contain: username, email, password (hashed)
     */
    public function create($data)
    {
        $this->db->insert('users', $data);
        return $this->db->insert_id();
    }
}
