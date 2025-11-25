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
                'featured_image' => null,
                'content_html' => $content_html,
                'content_delta' => $content_delta,
                'author_id' => $author_id,
                'status' => $status,
                'is_featured' => $is_featured
            ];
            // Handle featured image upload (if provided)
            if (!empty($_FILES) && isset($_FILES['featured_image'])) {
                $fileErr = $_FILES['featured_image']['error'];
                if ($fileErr === UPLOAD_ERR_OK) {
                    $up = $_FILES['featured_image'];
                    $tmp = $up['tmp_name'];
                    $orig = $up['name'];
                    $ext = pathinfo($orig, PATHINFO_EXTENSION);
                    $safe = preg_replace('/[^a-z0-9\-_.]/i', '-', pathinfo($orig, PATHINFO_FILENAME));
                    $filename = time() . '_' . $safe . ($ext ? '.' . $ext : '');
                    $destDir = rtrim(FCPATH, '\\/') . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR;
                    if (!is_dir($destDir)) @mkdir($destDir, 0755, true);
                    $dest = $destDir . $filename;
                    $i = 1;
                    while (file_exists($dest)) {
                        $filename = time() . '_' . $safe . '-' . $i . ($ext ? '.' . $ext : '');
                        $dest = $destDir . $filename;
                        $i++;
                    }
                    if (is_uploaded_file($tmp) && @move_uploaded_file($tmp, $dest)) {
                        $insert['featured_image'] = $filename;
                        log_message('debug', '[admin/blog/create] uploaded featured_image=' . $filename . ' for new post');
                    } else {
                        $msg = 'Failed to move uploaded file to ' . $dest;
                        log_message('error', '[admin/blog/create] ' . $msg);
                        $this->session->set_flashdata('blog_error', 'Cover image upload failed: unable to save file.');
                    }
                } else {
                    log_message('error', '[admin/blog/create] upload error code=' . $fileErr);
                    $this->session->set_flashdata('blog_error', 'Cover image upload failed (upload error code ' . $fileErr . ').');
                }
            }
            $id = $this->Blog_model->insert($insert);
            // check DB error / affected rows
            $dberr = $this->db->error();
            $affected = $this->db->affected_rows();
            log_message('debug', '[admin/blog/create] insert_id=' . $id . ', affected=' . $affected . ', dberr=' . json_encode($dberr));

            if ($id && $affected !== 0) {
                $this->session->set_flashdata('blog_success', 'Post saved successfully.');
                // if no featured_image was set (upload failed or skipped), try extracting from content_html
                if (empty($insert['featured_image']) && !empty($insert['content_html'])) {
                    $this->_extract_and_save_featured($id, $insert['content_html']);
                }
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
            // Handle featured image upload on edit
            if (!empty($_FILES) && isset($_FILES['featured_image'])) {
                $fileErr = $_FILES['featured_image']['error'];
                if ($fileErr === UPLOAD_ERR_OK) {
                    $up = $_FILES['featured_image'];
                    $tmp = $up['tmp_name'];
                    $orig = $up['name'];
                    $ext = pathinfo($orig, PATHINFO_EXTENSION);
                    $safe = preg_replace('/[^a-z0-9\-_.]/i', '-', pathinfo($orig, PATHINFO_FILENAME));
                    $filename = time() . '_' . $safe . ($ext ? '.' . $ext : '');
                    $destDir = rtrim(FCPATH, '\\/') . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR;
                    if (!is_dir($destDir)) @mkdir($destDir, 0755, true);
                    $dest = $destDir . $filename;
                    $i = 1;
                    while (file_exists($dest)) {
                        $filename = time() . '_' . $safe . '-' . $i . ($ext ? '.' . $ext : '');
                        $dest = $destDir . $filename;
                        $i++;
                    }
                    if (is_uploaded_file($tmp) && @move_uploaded_file($tmp, $dest)) {
                        $update['featured_image'] = $filename;
                        log_message('debug', '[admin/blog/edit] uploaded featured_image=' . $filename . ' for post id=' . (int)$id);
                    } else {
                        $msg = 'Failed to move uploaded file to ' . $dest;
                        log_message('error', '[admin/blog/edit] ' . $msg);
                        $this->session->set_flashdata('blog_error', 'Cover image upload failed: unable to save file.');
                    }
                } else {
                    log_message('error', '[admin/blog/edit] upload error code=' . $fileErr);
                    $this->session->set_flashdata('blog_error', 'Cover image upload failed (upload error code ' . $fileErr . ').');
                }
            }
            $this->Blog_model->update($id, $update);
            // If featured_image not provided during edit, try extract from content_html
            if (empty($update['featured_image']) && !empty($update['content_html'])) {
                $this->_extract_and_save_featured($id, $update['content_html']);
            }
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

    /**
     * Extract first image from HTML content and set featured_image for a post
     * Supports data: URI (base64) and images under /assets/images/
     */
    private function _extract_and_save_featured($id, $content_html)
    {
        if (empty($content_html) || empty($id)) return false;

        if (!preg_match('/<img[^>]+src=["\']?([^"\' >]+)["\']?[^>]*>/i', $content_html, $m)) return false;
        $src = $m[1];

        // data URI
        if (strpos($src, 'data:') === 0) {
            if (preg_match('/^data:([^;]+);base64,(.+)$/', $src, $d)) {
                $mime = $d[1];
                $b64 = $d[2];
                $ext = '';
                switch (strtolower($mime)) {
                    case 'image/jpeg': $ext = 'jpg'; break;
                    case 'image/jpg': $ext = 'jpg'; break;
                    case 'image/png': $ext = 'png'; break;
                    case 'image/gif': $ext = 'gif'; break;
                    case 'image/webp': $ext = 'webp'; break;
                    default:
                        $parts = explode('/', $mime);
                        $ext = isset($parts[1]) ? preg_replace('/[^a-z0-9]/', '', $parts[1]) : 'bin';
                }
                $basename = 'cover_' . $id . '_' . time() . '.' . $ext;
                $destDir = rtrim(FCPATH, '\\/') . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR;
                if (!is_dir($destDir)) @mkdir($destDir, 0755, true);
                $dest = $destDir . $basename;
                $decoded = base64_decode($b64);
                if ($decoded !== false && @file_put_contents($dest, $decoded) !== false) {
                    $this->Blog_model->update($id, ['featured_image' => $basename]);
                    log_message('debug', '[admin/blog/_extract] saved data-URI as ' . $basename . ' for id=' . $id);
                    return true;
                }
            }
            return false;
        }

        // if asset already in assets/images
        if (strpos($src, '/assets/images/') !== false) {
            $basename = basename($src);
            if ($basename) {
                $this->Blog_model->update($id, ['featured_image' => $basename]);
                log_message('debug', '[admin/blog/_extract] set existing image ' . $basename . ' for id=' . $id);
                return true;
            }
        }

        // relative path or other -> use basename
        $basename = basename($src);
        if ($basename) {
            $this->Blog_model->update($id, ['featured_image' => $basename]);
            log_message('debug', '[admin/blog/_extract] set relative image ' . $basename . ' for id=' . $id);
            return true;
        }

        return false;
    }
}
