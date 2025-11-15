<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {
    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_model');
        $this->load->library(['form_validation','session']);
        $this->load->database();
        $this->load->helper(['url','form']);
    }

    /**
     * Show login form and handle login post
     */
    public function index()
    {
        if ($this->input->post()) {
            $this->form_validation->set_rules('email','Email','required|valid_email');
            $this->form_validation->set_rules('password','Password','required');

            if ($this->form_validation->run() === FALSE) {
                // validation failed - show form with errors
                $this->load->view('login/login');
                return;
            }

            $email = $this->input->post('email', true);
            $password = $this->input->post('password', true);

            $user = $this->User_model->get_by_email($email);
            if (!$user) {
                $this->session->set_flashdata('error', 'Email tidak terdaftar.');
                redirect(base_url('index.php/login'));
            }

            if (!password_verify($password, $user['password'])) {
                $this->session->set_flashdata('error', 'Kata sandi salah.');
                redirect(base_url('index.php/login'));
            }

            // login success - set session
            $this->session->set_userdata([
                'user_id' => $user['id'],
                'username' => $user['username'],
                'email' => $user['email'],
                'logged_in' => true
            ]);

            $this->session->set_flashdata('success', 'Berhasil masuk.');
            redirect(base_url());
        }

        // show login form
        $this->load->view('login/login');
    }

    /**
     * Show register form and handle registration post
     */
    public function register()
    {
        if ($this->input->post()) {
            $this->form_validation->set_rules('username','Username','required|min_length[3]|max_length[100]|is_unique[users.username]');
            $this->form_validation->set_rules('email','Email','required|valid_email|is_unique[users.email]');
            $this->form_validation->set_rules('password','Password','required|min_length[6]');
            $this->form_validation->set_rules('password2','Confirm Password','required|matches[password]');

            if ($this->form_validation->run() === FALSE) {
                $this->load->view('login/register');
                return;
            }

            $username = $this->input->post('username', true);
            $email = $this->input->post('email', true);
            $password = $this->input->post('password', true);

            $hash = password_hash($password, PASSWORD_DEFAULT);
            $user_id = $this->User_model->create([
                'username' => $username,
                'email' => $email,
                'password' => $hash,
                'role' => 'user'
            ]);

            if ($user_id) {
                $this->session->set_flashdata('success', 'Pendaftaran berhasil. Silakan masuk.');
                redirect(base_url('index.php/login'));
            } else {
                $this->session->set_flashdata('error', 'Gagal membuat akun. Coba lagi.');
                redirect(base_url('index.php/login/register'));
            }
        }

        $this->load->view('login/register');
    }

    /**
     * Logout user
     */
    public function logout()
    {
        // Preserve return URL so user stays on the same page after logout
        $return = $this->input->get('return', true);
        $this->session->sess_destroy();
        if ($return) {
            // If a full URL (starts with http) redirect directly, otherwise build full url
            redirect($return);
        }

        // Fallback to homepage
        redirect(base_url());
    }
}
