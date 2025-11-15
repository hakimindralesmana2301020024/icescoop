<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Blog extends CI_Controller {
    public function index()
    {
        // Try to load posts from DB (Blog_model). If unavailable, fallback to hard-coded posts.
        $data_posts = [];
        try {
            $this->load->model('Blog_model');
            $db_posts = $this->Blog_model->get_all();
            if (!empty($db_posts)) {
                foreach ($db_posts as $bp) {
                    $data_posts[] = [
                        'title' => $bp['title'],
                        'img' => !empty($bp['featured_image']) ? base_url('assets/images/' . $bp['featured_image']) : base_url('assets/images/placeholder.svg'),
                        'author' => 'Admin',
                        'date' => !empty($bp['created_at']) ? date('F j, Y', strtotime($bp['created_at'])) : '',
                        'excerpt' => $bp['excerpt'] ?: '',
                        'content' => $bp['content_html'] ?: ''
                    ];
                }
            }
        } catch (Exception $e) {
            $data_posts = [];
        }

        if (empty($data_posts)) {
            $data_posts = [
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
        }

        $data = ['posts' => $data_posts];
        $this->load->view('templates/header');
        $this->load->view('blog/index', $data);
        $this->load->view('templates/footer');
    }

    public function details($id = 0)
    {
        // First try to load single post from DB by id (if numeric)
        $post_data = null;
        if (is_numeric($id)) {
            try {
                $this->load->model('Blog_model');
                $db_post = $this->Blog_model->get((int)$id);
                if (!empty($db_post)) {
                    $post_data = [
                        'title' => $db_post['title'],
                        'img' => !empty($db_post['featured_image']) ? base_url('assets/images/' . $db_post['featured_image']) : base_url('assets/images/placeholder.svg'),
                        'author' => 'Admin',
                        'date' => !empty($db_post['created_at']) ? date('F j, Y', strtotime($db_post['created_at'])) : '',
                        'excerpt' => $db_post['excerpt'] ?: '',
                        'content' => $db_post['content_html'] ?: ''
                    ];
                }
            } catch (Exception $e) {
                $post_data = null;
            }
        }

        // fallback to hard-coded posts if DB not available or post not found
        if (empty($post_data)) {
            $posts = [
                [
                    'title' => "Our strength, Your Business",
                    'img' => base_url('assets/images/placeholder.svg'),
                    'author' => 'Admin',
                    'date' => 'October 10, 2022',
                    'excerpt' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod. Lorem ipsum dolor sit amet, consectetur.',
                    'content' => "Google has been investing in AI for many years and bringing its benefits to individuals, businesses and communities. Whether it's publishing state-of-the-art research, building helpful products or developing tools and resources that enable others, we're committed to making AI accessible to everyone."
                ],
                [
                    'title' => "How's the Economy?",
                    'img' => base_url('assets/images/placeholder.svg'),
                    'author' => 'Admin',
                    'date' => 'October 10, 2022',
                    'excerpt' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod. Lorem ipsum dolor sit amet, consectetur.',
                    'content' => "We're now at a pivotal moment in our AI journey. Breakthroughs in generative AI are fundamentally changing how people interact with technology — and at Google, we've been responsibly developing large language models so we can safely bring them to our products."
                ],
                [
                    'title' => "Tasty Innovations",
                    'img' => base_url('assets/images/placeholder.svg'),
                    'author' => 'Admin',
                    'date' => 'October 10, 2022',
                    'excerpt' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod. Lorem ipsum dolor sit amet, consectetur.',
                    'content' => "More than 3 billion people already benefit from AI-powered features in Google Workspace, whether it's using Smart Compose in Gmail or autogenerated summaries in Google Docs."
                ],
                [
                    'title' => "Seasonal Flavours",
                    'img' => base_url('assets/images/placeholder.svg'),
                    'author' => 'Admin',
                    'date' => 'October 10, 2022',
                    'excerpt' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod. Lorem ipsum dolor sit amet, consectetur.',
                    'content' => "We're excited by the potential of generative AI, and the opportunities it will unlock — from helping people express themselves creatively, to helping developers build brand new types of applications."
                ],
                [
                    'title' => "Sweet Ingredients",
                    'img' => base_url('assets/images/placeholder.svg'),
                    'author' => 'Admin',
                    'date' => 'October 10, 2022',
                    'excerpt' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod. Lorem ipsum dolor sit amet, consectetur.',
                    'content' => "Stay tuned for more to come in the weeks and months ahead as we roll out new experiences to testers in the coming weeks."
                ],
                [
                    'title' => "Making The Perfect Scoop",
                    'img' => base_url('assets/images/placeholder.svg'),
                    'author' => 'Admin',
                    'date' => 'October 10, 2022',
                    'excerpt' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod. Lorem ipsum dolor sit amet, consectetur.',
                    'content' => "If you're a manager onboarding a new employee, Workspace saves you time and effort involved in writing that first welcome email."
                ],
            ];

            if (!isset($posts[$id])) {
                show_404();
                return;
            }

            $data = [
                'post' => $posts[$id],
                'popular' => array_slice($posts, 0, 3)
            ];
        } else {
            $data = ['post' => $post_data, 'popular' => []];
        }

        $this->load->view('templates/header');
        $this->load->view('blog/details', $data);
        $this->load->view('templates/footer');
    }
}
