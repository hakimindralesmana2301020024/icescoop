<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Icecream_model');
    }

    public function index()
    {
        $data['featured'] = $this->Icecream_model->get_featured();
        $data['categories'] = $this->Icecream_model->get_categories();
        $data['best_sellers'] = $this->Icecream_model->get_best_sellers();
        $data['testimonials'] = $this->Icecream_model->get_testimonials();

        $this->load->view('templates/header');
        $this->load->view('home', $data);
        $this->load->view('templates/footer');
    }

    public function contact()
    {
        $this->load->view('templates/header');
        $this->load->view('contact');
        $this->load->view('templates/footer');
    }

}
