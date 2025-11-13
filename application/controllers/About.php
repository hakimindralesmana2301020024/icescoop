<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class About extends CI_Controller {

    public function index()
    {
        // Render full page using shared header and footer templates
        $this->load->view('templates/header');
        $this->load->view('about/index');
        $this->load->view('templates/footer');
    }
}
