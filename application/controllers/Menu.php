<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Menu extends CI_Controller {
    public function index()
    {
        // Hardcoded data for demo
        $products = [
            [
                'name' => 'Classic Vanilla Ice Cream',
                'img' => base_url('assets/images/placeholder.svg'),
                'desc' => 'Creamy vanilla ice cream topped with cherry.',
                'price' => '4.99',
                'rating' => '4.85',
                'featured' => false
            ],
            [
                'name' => 'Chocolate Brownie Sundae',
                'img' => base_url('assets/images/placeholder.svg'),
                'desc' => 'Rich chocolate ice cream with chunky brownie.',
                'price' => '5.49',
                'rating' => '4.95',
                'featured' => false
            ],
            [
                'name' => 'Strawberry Shortcake',
                'img' => base_url('assets/images/placeholder.svg'),
                'desc' => 'Strawberry ice cream layered with shortcake.',
                'price' => '6.29',
                'rating' => '4.88',
                'featured' => false
            ],
            [
                'name' => 'Mint Chocolate Chip Cone',
                'img' => base_url('assets/images/placeholder.svg'),
                'desc' => 'Refreshing mint ice cream with chocolate chips.',
                'price' => '3.99',
                'rating' => '4.79',
                'featured' => false
            ],
            [
                'name' => 'Strawberry Sundae',
                'img' => base_url('assets/images/placeholder.svg'),
                'desc' => 'Strawberry ice cream with fresh strawberries.',
                'price' => '5.99',
                'rating' => '4.75',
                'featured' => false
            ],
            [
                'name' => 'Chocolate Chip Cookie Cone',
                'img' => base_url('assets/images/placeholder.svg'),
                'desc' => 'Chocolate chip cookie dough ice cream in a cone.',
                'price' => '4.99',
                'rating' => '4.82',
                'featured' => false
            ],
             
        ];
        $categories = [
            ['name' => 'Cone/Cup (6)', 'active' => true],
            ['name' => 'Frozen Yogurt (3)', 'active' => false],
            ['name' => 'Ice Cream Cake (5)', 'active' => false],
            ['name' => 'Milkshakes (2)', 'active' => false],
            ['name' => 'Popsicles (4)', 'active' => false],
            ['name' => 'Sundaes (3)', 'active' => false],
        ];
        $featured = [
            ['name' => 'Rocky Road', 'price' => '6.99'],
            ['name' => 'Peach Melba', 'price' => '5.99'],
            ['name' => 'Classic Vanilla', 'price' => '3.99'],
            ['name' => 'Strawberry Cake', 'price' => '4.99'],
        ];
        $this->load->view('templates/header');
        $this->load->view('menu/index', [
            'products' => $products,
            'categories' => $categories,
            'featured' => $featured
        ]);
        $this->load->view('templates/footer');
    }
public function menudetail() {
    $this->load->view('templates/header');
    $this->load->view('menu/menudetail');
    $this->load->view('templates/footer');
}
}