<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CI_Controller {
    public function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        $this->load->helper('url');
    }

    /**
     * Hard-coded admin dashboard preview
     */
    public function index()
    {
        // For now this page is a hard-coded preview. In future we can add auth checks.
        $this->load->view('templates/admin_header');
        $this->load->view('admin/dashboard');
        $this->load->view('templates/admin_footer');
    }
}
