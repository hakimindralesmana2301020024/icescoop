<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class About extends CI_Controller {

    public function index()
    {
        // Load Page_model and fetch content for 'about' (fallback to hard-coded view content)
        $this->load->model('Page_model');
        $about = $this->Page_model->get_by_slug('about');

        $data = ['about' => $about];

        $this->load->view('templates/header');
        $this->load->view('about/index', $data);
        $this->load->view('templates/footer');
    }
}
