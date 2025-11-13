<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Blog extends CI_Controller {
    public function index()
    {
        $posts = [
            [
                'title' => "Our strength, Your Business",
                'img' => base_url('assets/images/placeholder.svg'),
                'author' => 'Admin',
                'date' => 'October 10, 2022',
                'excerpt' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod. Lorem ipsum dolor sit amet, consectetur.'
            ],
            [
                'title' => "How's the Economy?",
                'img' => base_url('assets/images/placeholder.svg'),
                'author' => 'Admin',
                'date' => 'October 10, 2022',
                'excerpt' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod. Lorem ipsum dolor sit amet, consectetur.'
            ],
            [
                'title' => "Tasty Innovations",
                'img' => base_url('assets/images/placeholder.svg'),
                'author' => 'Admin',
                'date' => 'October 10, 2022',
                'excerpt' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod. Lorem ipsum dolor sit amet, consectetur.'
            ],
            [
                'title' => "Seasonal Flavours",
                'img' => base_url('assets/images/placeholder.svg'),
                'author' => 'Admin',
                'date' => 'October 10, 2022',
                'excerpt' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod. Lorem ipsum dolor sit amet, consectetur.'
            ],
            [
                'title' => "Sweet Ingredients",
                'img' => base_url('assets/images/placeholder.svg'),
                'author' => 'Admin',
                'date' => 'October 10, 2022',
                'excerpt' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod. Lorem ipsum dolor sit amet, consectetur.'
            ],
            [
                'title' => "Making The Perfect Scoop",
                'img' => base_url('assets/images/placeholder.svg'),
                'author' => 'Admin',
                'date' => 'October 10, 2022',
                'excerpt' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod. Lorem ipsum dolor sit amet, consectetur.'
            ],
        ];

        $data = ['posts' => $posts];
        $this->load->view('templates/header');
        $this->load->view('blog/index', $data);
        $this->load->view('templates/footer');
    }
}
