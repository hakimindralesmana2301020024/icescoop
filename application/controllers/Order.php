<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Order extends CI_Controller {
    public function cart()
    {
        $cart = [
            [
                'name' => 'Classic Vanilla',
                'img' => base_url('assets/images/placeholder.svg'),
                'color' => 'White',
                'size' => 'L',
                'price' => '4.00',
                'qty' => 3,
                'total' => '12.00',
            ],
            [
                'name' => 'Chocolate Brownie',
                'img' => base_url('assets/images/placeholder.svg'),
                'color' => 'Brown',
                'size' => 'M',
                'price' => '5.49',
                'qty' => 4,
                'total' => '23.00',
            ],
            [
                'name' => 'Strawberry Cake',
                'img' => base_url('assets/images/placeholder.svg'),
                'color' => 'Red',
                'size' => 'M',
                'price' => '5.29',
                'qty' => 4,
                'total' => '22.00',
            ],
            [
                'name' => 'Mint Chocolate',
                'img' => base_url('assets/images/placeholder.svg'),
                'color' => 'Green',
                'size' => 'L',
                'price' => '3.99',
                'qty' => 2,
                'total' => '7.00',
            ],
        ];
        $summary = [
            'subtotal' => '63.99',
            'shipping' => '5.00',
            'total' => '68.99',
        ];
        $data = [
            'cart' => $cart,
            'summary' => $summary
        ];
        $this->load->view('templates/header');
        $this->load->view('order/cart', $data);
        $this->load->view('templates/footer');
    }

    public function checkout()
    {
        $cart = [
            [
                'name' => 'Classic Vanilla',
                'img' => base_url('assets/images/placeholder.svg'),
                'color' => 'White',
                'size' => 'L',
                'price' => '4.00',
                'qty' => 3,
                'total' => '12.00',
            ],
            [
                'name' => 'Chocolate Brownie',
                'img' => base_url('assets/images/placeholder.svg'),
                'color' => 'Brown',
                'size' => 'M',
                'price' => '5.49',
                'qty' => 4,
                'total' => '23.00',
            ],
            [
                'name' => 'Strawberry Cake',
                'img' => base_url('assets/images/placeholder.svg'),
                'color' => 'Red',
                'size' => 'M',
                'price' => '5.29',
                'qty' => 4,
                'total' => '22.00',
            ],
            [
                'name' => 'Mint Chocolate',
                'img' => base_url('assets/images/placeholder.svg'),
                'color' => 'Green',
                'size' => 'L',
                'price' => '3.99',
                'qty' => 2,
                'total' => '7.00',
            ],
        ];
        $summary = [
            'subtotal' => '63.99',
            'shipping' => '5.00',
            'total' => '68.99',
        ];

        $data = [
            'cart' => $cart,
            'summary' => $summary
        ];

        $this->load->view('templates/header');
        $this->load->view('order/checkout', $data);
        $this->load->view('templates/footer');
    }
}