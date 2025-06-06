<?php

defined('BASEPATH') || exit('No direct script access allowed');

#[\AllowDynamicProperties]
class Authors extends MY_Controller {

    public $form_validation;
    public $input;
    public $output;

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('url');
        $this->eloquent_model('Author');
        $this->eloquent_model('Post');
    }

    /**
     * List all authors.
     */
    public function index()
    {
        $authors = Author::withCount('publishedPosts')->orderBy('name')->get();
        $data['authors'] = $authors->toArray();
        $data['title'] = 'Authors';

        $this->load->view('templates/header', $data);
        $this->load->view('authors/index', $data);
        $this->load->view('templates/footer');
    }



    /**
     * Create new author form.
     */
    public function create()
    {
        $this->load->helper('form');
        $this->load->library('form_validation');

        $data['title'] = 'Create Author';

        $this->form_validation->set_rules('name', 'Name', 'required');
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email|is_unique[authors.email]');

        if ($this->form_validation->run() === FALSE) {
            $this->load->view('templates/header', $data);
            $this->load->view('authors/create');
            $this->load->view('templates/footer');
        } else {
            $social_media = [];
            
            // Build social media array
            $social_fields = ['twitter', 'github', 'linkedin', 'dribbble', 'behance', 'instagram'];
            foreach ($social_fields as $field) {
                $value = $this->input->post($field);
                if (!empty($value)) {
                    $social_media[$field] = $value;
                }
            }

            $author_data = [
                'name' => $this->input->post('name'),
                'email' => $this->input->post('email'),
                'bio' => $this->input->post('bio'),
                'website' => $this->input->post('website'),
                'social_media' => $social_media,
                'status' => 'active'
            ];

            try {
                $author = Author::create($author_data);
                redirect('authors');
            } catch (Exception $e) {
                log_message('error', 'Failed to create author: ' . $e->getMessage());
                show_error('Unable to create author');
            }
        }
    }

    /**
     * Edit author.
     */
    public function edit($id = NULL)
    {
        if (!$id) {
            show_404();
        }

        $this->load->helper('form');
        $this->load->library('form_validation');

        $author = Author::find($id);
        if (!$author) {
            show_404();
        }

        $data['author'] = $author->toArray();
        $data['title'] = 'Edit Author: ' . $author->name;

        $this->form_validation->set_rules('name', 'Name', 'required');
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email|callback_email_check[' . $id . ']');

        if ($this->form_validation->run() === FALSE) {
            $this->load->view('templates/header', $data);
            $this->load->view('authors/edit', $data);
            $this->load->view('templates/footer');
        } else {
            $social_media = [];
            
            // Build social media array
            $social_fields = ['twitter', 'github', 'linkedin', 'dribbble', 'behance', 'instagram'];
            foreach ($social_fields as $field) {
                $value = $this->input->post($field);
                if (!empty($value)) {
                    $social_media[$field] = $value;
                }
            }

            $author_data = [
                'name' => $this->input->post('name'),
                'email' => $this->input->post('email'),
                'bio' => $this->input->post('bio'),
                'website' => $this->input->post('website'),
                'social_media' => $social_media,
                'status' => $this->input->post('status')
            ];

            try {
                if ($author->update($author_data)) {
                    redirect('authors');
                } else {
                    show_error('Unable to update author');
                }
            } catch (Exception $e) {
                log_message('error', 'Failed to update author: ' . $e->getMessage());
                show_error('Unable to update author');
            }
        }
    }

    /**
     * Delete author.
     */
    public function delete($id = NULL)
    {
        if (!$id) {
            show_404();
        }

        try {
            $author = Author::find($id);
            if (!$author) {
                show_404();
            }

            if ($author->delete()) {
                redirect('authors');
            } else {
                show_error('Unable to delete author');
            }
        } catch (Exception $e) {
            log_message('error', 'Failed to delete author: ' . $e->getMessage());
            show_error('Unable to delete author');
        }
    }

    /**
     * Custom validation for email uniqueness (excluding current author)
     */
    public function email_check($email, $author_id)
    {
        $exists = Author::where('email', $email)->where('id', '!=', $author_id)->count() > 0;
        if ($exists) {
            $this->form_validation->set_message('email_check', 'This email is already taken.');
            return FALSE;
        }
        return TRUE;
    }
} 
