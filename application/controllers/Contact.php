<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Contact extends CI_Controller {
    public function index()
    {
        // Load page inside site template (header/footer)
        $data = [];
        $this->load->view('templates/header');
        $this->load->view('contact/index', $data);
        $this->load->view('templates/footer');
    }
}
