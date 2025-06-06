<?php

defined('BASEPATH') || exit('No direct script access allowed');

#[\AllowDynamicProperties]
class Posts extends MY_Controller {

    public $form_validation;
    public $input;
    public $output;

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('url');
        // Load Eloquent model
        $this->eloquent_model('Post');
    }

    /**
     * List all posts.
     */
    public function index()
    {
        $posts = Post::latest()->limit(10)->get();
        $data['posts'] = $posts->toArray();
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

        $post = Post::find($id);
        $data['post'] = $post ? $post->toArray() : null;

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

            try {
                $post = Post::create($post_data);
                redirect('posts/view/' . $post->id);
            } catch (Exception $e) {
                log_message('error', 'Failed to create post: ' . $e->getMessage());
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

        $post = Post::find($id);
        $data['post'] = $post ? $post->toArray() : null;

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

            try {
                $post = Post::find($id);
                if ($post && $post->update($post_data)) {
                    redirect('posts/view/' . $id);
                } else {
                    show_error('Unable to update post');
                }
            } catch (Exception $e) {
                log_message('error', 'Failed to update post: ' . $e->getMessage());
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

        try {
            $post = Post::find($id);

            if (!$post) {
                show_404();
            }

            if ($post->delete()) {
                redirect('posts');
            } else {
                show_error('Unable to delete post');
            }
        } catch (Exception $e) {
            log_message('error', 'Failed to delete post: ' . $e->getMessage());
            show_error('Unable to delete post');
        }
    }

    /**
     * API endpoint to get posts as JSON.
     */
    public function api()
    {
        $posts = Post::latest()->limit(10)->get();

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($posts->toArray()));
    }
} 
