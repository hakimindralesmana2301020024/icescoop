<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Contact extends CI_Controller {
    public function __construct()
    {
        parent::__construct();
        $this->load->helper(['url','form']);
        $this->load->library('session');
        $this->load->model('Contact_model');
    }

    // optional: show contact page (existing view handles it), but keep for direct access
    public function index()
    {
        $this->load->view('templates/header');
        $this->load->view('contact');
        $this->load->view('templates/footer');
    }

    public function submit()
    {
        if ($this->input->method() !== 'post') {
            redirect(base_url('index.php/contact'));
            return;
        }

        $first = $this->input->post('first_name', true);
        $last = $this->input->post('last_name', true);
        $name = trim($first . ' ' . $last);
        $email = $this->input->post('email', true);
        $phone = $this->input->post('phone', true);
        $message = $this->input->post('message', true);
        $subject = $this->input->post('subject', true) ?: null;

        if (empty($message) || empty($name)) {
            $this->session->set_flashdata('contact_error', 'Please fill required fields.');
            redirect(base_url('index.php/contact'));
            return;
        }

        $id = $this->Contact_model->insert_message([
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'subject' => $subject,
            'message' => $message
        ]);

        if ($id) {
            $this->session->set_flashdata('contact_success', 'Thank you — your message has been received.');
        } else {
            $this->session->set_flashdata('contact_error', 'Failed to submit your message. Please try again.');
        }

        redirect(base_url('index.php/contact'));
    }
}
