<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Icecream_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
    }

    public function get_featured()
    {
        return [
            [
                'title' => 'Chocolate Brownie Sundae',
                'desc' => 'Rich chocolate ice cream with chunks of brownie.',
                'price' => '$5.49',
                'rating' => '4.9',
                'image' => base_url('assets/images/placeholder.svg')
            ],
            [
                'title' => 'Strawberry Shortcake',
                'desc' => 'Strawberry ice cream layered with shortcake',
                'price' => '$5.29',
                'rating' => '4.8',
                'image' => base_url('assets/images/placeholder.svg')
            ],
            [
                'title' => 'Mint Chocolate Chip Cone',
                'desc' => 'Refreshing mint ice cream with chocolate chips.',
                'price' => '$3.99',
                'rating' => '4.7',
                'image' => base_url('assets/images/placeholder.svg')
            ],
            [
                'title' => 'Classic Vanilla Ice Cream',
                'desc' => 'Creamy vanilla ice cream topped with cherry.',
                'price' => '$4.99',
                'rating' => '4.6',
                'image' => base_url('assets/images/placeholder.svg')
            ],
        ];
    }

    public function get_categories()
    {
        return [
            ['name' => 'Sundaes', 'image' => base_url('assets/images/placeholder.svg')],
            ['name' => 'Ice Cream Cones', 'image' => base_url('assets/images/placeholder.svg')],
            ['name' => 'Milkshakes', 'image' => base_url('assets/images/placeholder.svg')],
            ['name' => 'Seasonal Flavors', 'image' => base_url('assets/images/placeholder.svg')],
        ];
    }

    public function get_best_sellers()
    {
        return [
            ['title' => 'Chocolate Chip Cookie Cone', 'price' => '$4.49', 'image' => base_url('assets/images/placeholder.svg')],
            ['title' => 'Rocky Road Sundae', 'price' => '$5.69', 'image' => base_url('assets/images/placeholder.svg')],
            ['title' => 'Peach Melba Sundae', 'price' => '$5.39', 'image' => base_url('assets/images/placeholder.svg')],
            ['title' => 'Strawberry Sundae', 'price' => '$5.99', 'image' => base_url('assets/images/placeholder.svg')],
        ];
    }

    public function get_testimonials()
    {
        return [
            ['name' => 'Kevin Andrew', 'role' => 'Happy Customer', 'text' => 'Best ice cream ever!'],
            ['name' => 'Siti Rahma', 'role' => 'Happy Customer', 'text' => 'Great flavors and service.'],
            ['name' => 'John Doe', 'role' => 'Happy Customer', 'text' => 'My family loves it!'],
        ];
    }

}
