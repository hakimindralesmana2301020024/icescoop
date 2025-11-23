<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Home_model');
    }

    public function index()
    {
        // Load editable home data (admin edits saved to Home_model)
        $home = $this->Home_model->get();

        // Prepare variables expected by the public view `application/views/home.php`
        $data = [];
        $data['home'] = $home;
        $data['featured'] = isset($home['featured_items']) && is_array($home['featured_items']) ? $home['featured_items'] : [];
        $data['categories'] = isset($home['categories']) && is_array($home['categories']) ? $home['categories'] : [];
        $data['best_sellers'] = isset($home['best_sellers']) && is_array($home['best_sellers']) ? $home['best_sellers'] : [];
        $data['testimonials'] = isset($home['testimonials']) && is_array($home['testimonials']) ? $home['testimonials'] : [];

        // Also expose hero fields in case we want to display them in the view later
        $data['hero_title'] = isset($home['hero_title']) ? $home['hero_title'] : '';
        $data['hero_subtitle'] = isset($home['hero_subtitle']) ? $home['hero_subtitle'] : '';
        $data['intro'] = isset($home['intro']) ? $home['intro'] : '';
        $data['hero_image'] = isset($home['hero_image']) ? $home['hero_image'] : '';

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
