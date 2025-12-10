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
                    // if slug missing, generate and persist a unique slug so links work without manual steps
                    if (empty($bp['slug']) && isset($bp['id'])) {
                        try {
                            $this->load->model('Blog_model');
                            $new_slug = $this->Blog_model->create_unique_slug(isset($bp['title']) ? $bp['title'] : 'post', $bp['id']);
                            $this->Blog_model->update($bp['id'], ['slug' => $new_slug]);
                            $bp['slug'] = $new_slug;
                        } catch (Exception $e) {
                            // ignore and continue without slug
                        }
                    }

                    $data_posts[] = [
                        'id' => isset($bp['id']) ? $bp['id'] : null,
                        'slug' => isset($bp['slug']) ? $bp['slug'] : null,
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
        // load categories if available
        try {
            $this->load->model('Blog_category_model');
            $cats = $this->Blog_category_model->get_all();
        } catch (Exception $e) {
            $cats = [];
        }
        $data['categories'] = $cats;

        $this->load->view('templates/header');
        $this->load->view('blog/index', $data);
        $this->load->view('templates/footer');
    }

    /**
     * AJAX filter endpoint: returns HTML for posts filtered by category slug (or 'all')
     */
    public function filter()
    {
        $cat = $this->input->get('category', true);
        $html = '';

        try {
            $this->load->model('Blog_model');
            $this->load->model('Blog_category_model');

            if (!$cat || $cat === 'all') {
                $posts = $this->Blog_model->get_all();
            } else {
                // try slug -> category id
                $c = $this->Blog_category_model->get_by_slug($cat);
                if ($c && !empty($c['id'])) {
                    $post_ids = $this->Blog_category_model->get_post_ids_for_category($c['id']);
                    if (!empty($post_ids)) {
                        $this->db->where_in('id', $post_ids);
                        $this->db->order_by('created_at','DESC');
                        $posts = $this->db->get('blogs')->result_array();
                    } else {
                        $posts = [];
                    }
                } else {
                    // fallback: try matching category name stored in blogs.category (if exists)
                    $this->db->where('category', $cat);
                    $posts = $this->db->get('blogs')->result_array();
                }
            }
        } catch (Exception $e) {
            $posts = [];
        }

        // render partial HTML for posts grid
        foreach ($posts as $p) {
            $title = isset($p['title']) ? htmlspecialchars($p['title']) : '';
            $excerpt = isset($p['excerpt']) ? htmlspecialchars($p['excerpt']) : '';
            $img = !empty($p['featured_image']) ? base_url('assets/images/' . $p['featured_image']) : base_url('assets/images/placeholder.svg');
            $slug = isset($p['slug']) ? $p['slug'] : (isset($p['id']) ? $p['id'] : '');
            $link = !empty($slug) ? base_url('index.php/blog/'.$slug) : '#';
            $html .= '<article class="post-card">';
            $html .= '<div class="post-img"><img src="'. $img . '" alt="'. $title .'"/></div>';
            $html .= '<div class="post-body">';
            $html .= '<div class="post-meta">Posted by <strong>Admin</strong></div>';
            $html .= '<h3 class="post-title">'. $title .'</h3>';
            $html .= '<p class="post-excerpt">'. $excerpt .'</p>';
            $html .= '<a href="'. $link .'" class="read-more">Read More</a>';
            $html .= '</div></article>';
        }

        header('Content-Type: application/json');
        echo json_encode(['html' => $html]);
    }

    public function details($id = 0)
    {
        // First try to load single post from DB by id or slug
        $post_data = null;
        try {
            $this->load->model('Blog_model');
            $db_post = $this->Blog_model->get($id);
            if (!empty($db_post)) {
                // if slug missing in DB, generate and persist one so future links use slug
                if (empty($db_post['slug']) && isset($db_post['id'])) {
                    try {
                        $this->load->model('Blog_model');
                        $new_slug = $this->Blog_model->create_unique_slug(isset($db_post['title']) ? $db_post['title'] : 'post', $db_post['id']);
                        $this->Blog_model->update($db_post['id'], ['slug' => $new_slug]);
                        $db_post['slug'] = $new_slug;
                    } catch (Exception $e) {
                        // ignore
                    }
                }

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

            // fallback only supports numeric index for hard-coded posts
            if (!is_numeric($id) || !isset($posts[(int)$id])) {
                show_404();
                return;
            }

            $data = [
                'post' => $posts[(int)$id],
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
