<?php

defined('BASEPATH') || exit('No direct script access allowed');

class Posts extends CI_Controller {

    public $form_validation;
    public $input;
    public $Post_model;
    public $output;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Post_model');
        $this->load->helper('url');
    }

    /**
     * List all posts.
     */
    public function index()
    {
        $data['posts'] = $this->Post_model->get_posts();
        $data['title'] = 'All Posts';

        $this->load->view('templates/header', $data);
        $this->load->view('posts/index', $data);
        $this->load->view('templates/footer');
    }

    /**
     * Show single post.
     */
    public function view($id = NULL)
    {
        if (!$id) {
            show_404();
        }

        $data['post'] = $this->Post_model->get_post($id);

        if (!$data['post']) {
            show_404();
        }

        $data['title'] = $data['post']['title'];

        $this->load->view('templates/header', $data);
        $this->load->view('posts/view', $data);
        $this->load->view('templates/footer');
    }

    /**
     * Create new post form.
     */
    public function create()
    {
        $this->load->helper('form');
        $this->load->library('form_validation');

        $data['title'] = 'Create Post';

        $this->form_validation->set_rules('title', 'Title', 'required');
        $this->form_validation->set_rules('content', 'Content', 'required');

        if ($this->form_validation->run() === FALSE) {
            $this->load->view('templates/header', $data);
            $this->load->view('posts/create');
            $this->load->view('templates/footer');
        } else {
            $post_data = [
                'title' => $this->input->post('title'),
                'content' => $this->input->post('content'),
                'author' => $this->input->post('author'),
            ];

            $post_id = $this->Post_model->create_post($post_data);

            if ($post_id) {
                redirect('posts/view/' . $post_id);
            } else {
                show_error('Unable to create post');
            }
        }
    }

    /**
     * Edit post.
     */
    public function edit($id = NULL)
    {
        if (!$id) {
            show_404();
        }

        $this->load->helper('form');
        $this->load->library('form_validation');

        $data['post'] = $this->Post_model->get_post($id);

        if (!$data['post']) {
            show_404();
        }

        $data['title'] = 'Edit Post';

        $this->form_validation->set_rules('title', 'Title', 'required');
        $this->form_validation->set_rules('content', 'Content', 'required');

        if ($this->form_validation->run() === FALSE) {
            $this->load->view('templates/header', $data);
            $this->load->view('posts/edit', $data);
            $this->load->view('templates/footer');
        } else {
            $post_data = [
                'title' => $this->input->post('title'),
                'content' => $this->input->post('content'),
                'author' => $this->input->post('author'),
            ];

            if ($this->Post_model->update_post($id, $post_data)) {
                redirect('posts/view/' . $id);
            } else {
                show_error('Unable to update post');
            }
        }
    }

    /**
     * Delete post.
     */
    public function delete($id = NULL)
    {
        if (!$id) {
            show_404();
        }

        $post = $this->Post_model->get_post($id);

        if (!$post) {
            show_404();
        }

        if ($this->Post_model->delete_post($id)) {
            redirect('posts');
        } else {
            show_error('Unable to delete post');
        }
    }

    /**
     * API endpoint to get posts as JSON.
     */
    public function api()
    {
        $posts = $this->Post_model->get_posts();

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($posts));
    }
} 
