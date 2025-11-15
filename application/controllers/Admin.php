<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CI_Controller {
    public function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        $this->load->helper('url');
        // Protect admin area: only allow logged-in users with role 'admin'
        $user = $this->session->userdata();
        if (empty($user) || empty($user['logged_in']) || (!isset($user['role']) || $user['role'] !== 'admin')) {
            // redirect to login and return to requested admin page after login
            $return = current_url();
            redirect(base_url('index.php/login?return=' . urlencode($return)));
            exit;
        }
    }

    /**
     * Hard-coded admin dashboard preview
     */
    public function index()
    {
        // For now this page is a hard-coded preview. In future we can add auth checks.
        $this->load->view('templates/admin_header');
        $this->load->view('admin/dashboard');
        $this->load->view('templates/admin_footer');
    }

    /**
     * Edit About page content (GET shows form, POST saves)
     */
    public function about()
    {
        $this->load->model('Page_model');

        // create images dir if missing
        $img_dir = FCPATH . 'assets/images/about/';
        if (!is_dir($img_dir)) {
            @mkdir($img_dir, 0755, true);
        }

        if ($this->input->method() === 'post') {
            // read posted fields
            $payload = [];
            // fetch existing to allow safe deletion of previous images
            $existing = $this->Page_model->get_by_slug('about') ?: [];
            $payload['hero_title'] = $this->input->post('hero_title', true);
            $payload['journey_title'] = $this->input->post('journey_title', true);
            $payload['journey_lead1'] = $this->input->post('journey_lead1', true);
            $payload['journey_lead2'] = $this->input->post('journey_lead2', true);
            $payload['mission_title'] = $this->input->post('mission_title', true);
            $payload['mission_lead'] = $this->input->post('mission_lead', true);

            // Team members (up to 6 simple rows)
            $team = [];
            $names = $this->input->post('team_name');
            $roles = $this->input->post('team_role');
            for ($i=0;$i<6;$i++) {
                if (!empty($names[$i]) || !empty($roles[$i])) {
                    $team[$i] = [
                        'name' => isset($names[$i]) ? $this->input->post('team_name['.$i.']', true) : '',
                        'role' => isset($roles[$i]) ? $this->input->post('team_role['.$i.']', true) : '',
                        'image' => ''
                    ];
                }
            }

            // handle uploads: journey_image, mission_image, team_image_0..5
            $uploaded = [];
            if (!empty($_FILES['journey_image']) && $_FILES['journey_image']['error'] === UPLOAD_ERR_OK) {
                $tmp = $_FILES['journey_image']['tmp_name'];
                $name = 'journey_' . time() . '_' . basename($_FILES['journey_image']['name']);
                $dest = $img_dir . $name;
                if (@move_uploaded_file($tmp, $dest)) {
                    // remove previous image file if present and inside about folder
                    if (!empty($existing['journey_image'])) {
                        $prev = FCPATH . ltrim($existing['journey_image'], '/');
                        $about_base = realpath(FCPATH . 'assets/images/about/');
                        if ($about_base && strpos(realpath($prev), $about_base) === 0 && is_file($prev)) {
                            @unlink($prev);
                        }
                    }
                    $payload['journey_image'] = 'assets/images/about/' . $name;
                }
            }

            if (!empty($_FILES['mission_image']) && $_FILES['mission_image']['error'] === UPLOAD_ERR_OK) {
                $tmp = $_FILES['mission_image']['tmp_name'];
                $name = 'mission_' . time() . '_' . basename($_FILES['mission_image']['name']);
                $dest = $img_dir . $name;
                if (@move_uploaded_file($tmp, $dest)) {
                    if (!empty($existing['mission_image'])) {
                        $prev = FCPATH . ltrim($existing['mission_image'], '/');
                        $about_base = realpath(FCPATH . 'assets/images/about/');
                        if ($about_base && strpos(realpath($prev), $about_base) === 0 && is_file($prev)) {
                            @unlink($prev);
                        }
                    }
                    $payload['mission_image'] = 'assets/images/about/' . $name;
                }
            }

            // team images
            for ($i=0;$i<6;$i++) {
                $key = 'team_image_' . $i;
                if (!empty($_FILES[$key]) && $_FILES[$key]['error'] === UPLOAD_ERR_OK) {
                    $tmp = $_FILES[$key]['tmp_name'];
                    $name = 'team_' . $i . '_' . time() . '_' . basename($_FILES[$key]['name']);
                    $dest = $img_dir . $name;
                    if (@move_uploaded_file($tmp, $dest)) {
                        // delete previous team image if present
                        $existing_team = isset($existing['team']) && is_array($existing['team']) ? $existing['team'] : [];
                        if (isset($existing_team[$i]) && !empty($existing_team[$i]['image'])) {
                            $prev = FCPATH . ltrim($existing_team[$i]['image'], '/');
                            $about_base = realpath(FCPATH . 'assets/images/about/');
                            if ($about_base && strpos(realpath($prev), $about_base) === 0 && is_file($prev)) {
                                @unlink($prev);
                            }
                        }
                        if (isset($team[$i])) {
                            $team[$i]['image'] = 'assets/images/about/' . $name;
                        }
                    }
                }
            }

            $payload['team'] = array_values($team);

            // Merge with existing so empty posted fields won't destroy everything
            $existing = $this->Page_model->get_by_slug('about') ?: [];
            $merged = array_merge($existing, $payload);

            $this->Page_model->save_slug('about', $merged);
            $this->session->set_flashdata('admin_msg', 'About page updated.');
            redirect(base_url('index.php/admin/about'));
            return;
        }

        // GET -> render form
        $about = $this->Page_model->get_by_slug('about');
        $data = ['about' => $about];
        $this->load->view('templates/admin_header');
        $this->load->view('admin/about_edit', $data);
        $this->load->view('templates/admin_footer');
    }
}
