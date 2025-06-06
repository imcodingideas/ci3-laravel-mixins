<?php

defined('BASEPATH') || exit('No direct script access allowed');

#[\AllowDynamicProperties]
class Tags extends MY_Controller {

    public $form_validation;
    public $input;
    public $output;

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('url');
        $this->eloquent_model('Tag');
        $this->eloquent_model('Post');
    }

    /**
     * List all tags.
     */
    public function index()
    {
        $tags = Tag::withCount('publishedPosts')->orderBy('name')->get();
        $data['tags'] = $tags->toArray();
        $data['title'] = 'Tags';

        $this->load->view('templates/header', $data);
        $this->load->view('tags/index', $data);
        $this->load->view('templates/footer');
    }



    /**
     * Create new tag form.
     */
    public function create()
    {
        $this->load->helper('form');
        $this->load->library('form_validation');

        $data['title'] = 'Create Tag';

        $this->form_validation->set_rules('name', 'Name', 'required|is_unique[tags.name]');
        $this->form_validation->set_rules('color', 'Color', 'required');

        if ($this->form_validation->run() === FALSE) {
            $this->load->view('templates/header', $data);
            $this->load->view('tags/create');
            $this->load->view('templates/footer');
        } else {
            $tag_data = [
                'name' => $this->input->post('name'),
                'slug' => Tag::generateSlug($this->input->post('name')),
                'description' => $this->input->post('description'),
                'color' => $this->input->post('color')
            ];

            try {
                $tag = Tag::create($tag_data);
                redirect('tags');
            } catch (Exception $e) {
                log_message('error', 'Failed to create tag: ' . $e->getMessage());
                show_error('Unable to create tag');
            }
        }
    }

    /**
     * Edit tag.
     */
    public function edit($id = NULL)
    {
        if (!$id) {
            show_404();
        }

        $this->load->helper('form');
        $this->load->library('form_validation');

        $tag = Tag::find($id);
        if (!$tag) {
            show_404();
        }

        $data['tag'] = $tag->toArray();
        $data['title'] = 'Edit Tag: ' . $tag->name;

        $this->form_validation->set_rules('name', 'Name', 'required|callback_name_check[' . $id . ']');
        $this->form_validation->set_rules('color', 'Color', 'required');

        if ($this->form_validation->run() === FALSE) {
            $this->load->view('templates/header', $data);
            $this->load->view('tags/edit', $data);
            $this->load->view('templates/footer');
        } else {
            $tag_data = [
                'name' => $this->input->post('name'),
                'description' => $this->input->post('description'),
                'color' => $this->input->post('color')
            ];

            // Update slug if name changed
            if ($tag->name !== $this->input->post('name')) {
                $tag_data['slug'] = Tag::generateSlug($this->input->post('name'));
            }

            try {
                if ($tag->update($tag_data)) {
                    redirect('tags');
                } else {
                    show_error('Unable to update tag');
                }
            } catch (Exception $e) {
                log_message('error', 'Failed to update tag: ' . $e->getMessage());
                show_error('Unable to update tag');
            }
        }
    }

    /**
     * Delete tag.
     */
    public function delete($id = NULL)
    {
        if (!$id) {
            show_404();
        }

        try {
            $tag = Tag::find($id);
            if (!$tag) {
                show_404();
            }

            if ($tag->delete()) {
                redirect('tags');
            } else {
                show_error('Unable to delete tag');
            }
        } catch (Exception $e) {
            log_message('error', 'Failed to delete tag: ' . $e->getMessage());
            show_error('Unable to delete tag');
        }
    }

    /**
     * Custom validation for name uniqueness (excluding current tag)
     */
    public function name_check($name, $tag_id)
    {
        $exists = Tag::where('name', $name)->where('id', '!=', $tag_id)->count() > 0;
        if ($exists) {
            $this->form_validation->set_message('name_check', 'This tag name is already taken.');
            return FALSE;
        }
        return TRUE;
    }
} 
