<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Blog extends CI_Controller {
    public function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        $this->load->helper(array('url','form'));
        $this->load->model('Blog_model');

        $user = $this->session->userdata();
        if (empty($user) || empty($user['logged_in']) || (!isset($user['role']) || $user['role'] !== 'admin')) {
            $return = current_url();
            redirect(base_url('index.php/login?return=' . urlencode($return)));
            exit;
        }
    }

    public function index()
    {
        $data['posts'] = $this->Blog_model->get_all();
        $this->load->view('templates/admin_header');
        $this->load->view('admin/blog/index', $data);
        $this->load->view('templates/admin_footer');
    }

    public function create()
    {
        if ($this->input->method() === 'post') {
            $title = $this->input->post('title', true);
            $this->load->helper('url');
            $slug = $this->input->post('slug', true) ?: url_title($title,'dash',true);
            $excerpt = $this->input->post('excerpt', true);
            $content_html = $this->input->post('content_html', false);
            $content_delta = $this->input->post('content_delta', false);
            $status = $this->input->post('status', true) ?: 'draft';
            $is_featured = $this->input->post('is_featured') ? 1 : 0;
            $author_id = $this->session->userdata('id') ?: NULL;

            // Debug: record what we received
            $post_summary = 'POST received: title_len=' . strlen($title) .
                ', slug=' . substr($slug,0,100) .
                ', excerpt_len=' . strlen($excerpt) .
                ', content_html_len=' . strlen($content_html);
            log_message('debug', '[admin/blog/create] ' . $post_summary);
            $this->session->set_flashdata('blog_debug', $post_summary);

            // ensure unique slug
            $base_slug = $slug;
            $i = 1;
            while ($this->Blog_model->slug_exists($slug)) {
                $slug = $base_slug . '-' . $i++;
            }

            $insert = [
                'title' => $title,
                'slug' => $slug,
                'excerpt' => $excerpt,
                'content_html' => $content_html,
                'content_delta' => $content_delta,
                'author_id' => $author_id,
                'status' => $status,
                'is_featured' => $is_featured
            ];
            $id = $this->Blog_model->insert($insert);
            // check DB error / affected rows
            $dberr = $this->db->error();
            $affected = $this->db->affected_rows();
            log_message('debug', '[admin/blog/create] insert_id=' . $id . ', affected=' . $affected . ', dberr=' . json_encode($dberr));

            if ($id && $affected !== 0) {
                $this->session->set_flashdata('blog_success', 'Post saved successfully.');
                redirect(base_url('index.php/admin/blog'));
                return;
            }

            // If insert didn't work, show form again with debug info (no redirect)
            $msg = isset($dberr['message']) && $dberr['message'] ? $dberr['message'] : 'Unknown DB error or 0 affected rows';
            $data = [];
            $data['post'] = [
                'title' => $title,
                'slug' => $slug,
                'excerpt' => $excerpt,
                'content_html' => $content_html,
                'content_delta' => $content_delta,
                'status' => $status,
                'is_featured' => $is_featured
            ];
            $data['error_message'] = 'Failed to save post: ' . $msg;
            $data['post_summary'] = $post_summary;
            $this->load->view('templates/admin_header');
            $this->load->view('admin/blog/form', $data);
            echo '<div style="color:#b00; padding:10px;">Debug: ' . htmlspecialchars($data['error_message']) . '<br>' . htmlspecialchars($post_summary) . '</div>';
            $this->load->view('templates/admin_footer');
            return;
        }

        $data = ['post' => null];
        $this->load->view('templates/admin_header');
        $this->load->view('admin/blog/form', $data);
        $this->load->view('templates/admin_footer');
    }

    public function edit($id = NULL)
    {
        if (empty($id)) show_404();
        $post = $this->Blog_model->get((int)$id);
        if (!$post) show_404();

        if ($this->input->method() === 'post') {
            $title = $this->input->post('title', true);
            $this->load->helper('url');
            $slug = $this->input->post('slug', true) ?: url_title($title,'dash',true);
            $excerpt = $this->input->post('excerpt', true);
            $content_html = $this->input->post('content_html', false);
            $content_delta = $this->input->post('content_delta', false);
            $status = $this->input->post('status', true) ?: 'draft';
            $is_featured = $this->input->post('is_featured') ? 1 : 0;

            // ensure unique slug
            $base_slug = $slug;
            $i = 1;
            while ($this->Blog_model->slug_exists($slug, $id)) {
                $slug = $base_slug . '-' . $i++;
            }

            $update = [
                'title' => $title,
                'slug' => $slug,
                'excerpt' => $excerpt,
                'content_html' => $content_html,
                'content_delta' => $content_delta,
                'status' => $status,
                'is_featured' => $is_featured,
                'updated_at' => date('Y-m-d H:i:s')
            ];
            $this->Blog_model->update($id, $update);
            redirect(base_url('index.php/admin/blog'));
            return;
        }

        $data['post'] = $post;
        $this->load->view('templates/admin_header');
        $this->load->view('admin/blog/form', $data);
        $this->load->view('templates/admin_footer');
    }

    public function delete($id = NULL)
    {
        if (empty($id)) show_404();
        $this->Blog_model->delete((int)$id);
        redirect(base_url('index.php/admin/blog'));
    }
}
