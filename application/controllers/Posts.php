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
        // Load Eloquent models
        $this->eloquent_model('Post');
        $this->eloquent_model('Author');
        $this->eloquent_model('Tag');
    }

    /**
     * List all posts with search and filtering.
     */
    public function index()
    {
        $search = $this->input->get('search');
        $tag = $this->input->get('tag');
        $author = $this->input->get('author');
        $limit = $this->input->get('limit') ?: 10;
        $page = $this->input->get('page') ?: 1;
        $offset = ($page - 1) * $limit;

        $query = Post::with(['author', 'tags'])->published()->latest();

        // Apply search filter
        if ($search) {
            if (strlen($search) > 3) {
                // Use full-text search for longer queries
                $query->fullTextSearch($search);
            } else {
                // Use LIKE search for shorter queries
                $query->search($search);
            }
        }

        // Apply tag filter
        if ($tag) {
            $query->withTag($tag);
        }

        // Apply author filter
        if ($author) {
            $query->byAuthor($author);
        }

        $totalPosts = $query->count();
        $posts = $query->offset($offset)->limit($limit)->get();

        $data['posts'] = $posts->toArray();
        $data['search'] = $search;
        $data['current_tag'] = $tag;
        $data['current_author'] = $author;
        $data['title'] = 'All Posts';
        $data['total_posts'] = $totalPosts;
        $data['current_page'] = $page;
        $data['total_pages'] = ceil($totalPosts / $limit);
        
        // Get popular tags for sidebar
        $data['popular_tags'] = Tag::popular(10)->get()->toArray();
        
        // Get recent authors
        $data['recent_authors'] = Author::active()
            ->has('publishedPosts')
            ->withCount('publishedPosts')
            ->orderBy('published_posts_count', 'desc')
            ->limit(5)
            ->get()
            ->toArray();

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

        $post = Post::with(['author', 'tags'])->find($id);
        $data['post'] = $post ? $post->toArray() : null;

        if (!$data['post']) {
            show_404();
        }

        $data['title'] = $data['post']['title'];
        
        // Get related posts by tags
        if ($post && $post->tags->count() > 0) {
            $tagIds = $post->tags->pluck('id')->toArray();
            $data['related_posts'] = Post::with(['author', 'tags'])
                ->published()
                ->whereHas('tags', function($query) use ($tagIds) {
                    $query->whereIn('tags.id', $tagIds);
                })
                ->where('id', '!=', $id)
                ->latest()
                ->limit(3)
                ->get()
                ->toArray();
        } else {
            $data['related_posts'] = [];
        }

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
        
        // Get authors and tags for dropdowns
        $data['authors'] = Author::active()->orderBy('name')->get()->toArray();
        $data['tags'] = Tag::alphabetical()->get()->toArray();

        $this->form_validation->set_rules('title', 'Title', 'required');
        $this->form_validation->set_rules('content', 'Content', 'required');
        $this->form_validation->set_rules('author_id', 'Author', 'required|numeric');

        if ($this->form_validation->run() === FALSE) {
            $this->load->view('templates/header', $data);
            $this->load->view('posts/create', $data);
            $this->load->view('templates/footer');
        } else {
            $post_data = [
                'title' => $this->input->post('title'),
                'content' => $this->input->post('content'),
                'author_id' => $this->input->post('author_id'),
                'status' => $this->input->post('status') ?: 'draft',
            ];

            try {
                $post = Post::create($post_data);
                
                // Sync tags if provided
                $tags = $this->input->post('tags');
                if ($tags && is_array($tags)) {
                    $post->tags()->sync($tags);
                }
                
                redirect('view/' . $post->id);
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

        $post = Post::with(['author', 'tags'])->find($id);
        $data['post'] = $post ? $post->toArray() : null;

        if (!$data['post']) {
            show_404();
        }

        $data['title'] = 'Edit Post';
        
        // Get authors and tags for dropdowns
        $data['authors'] = Author::active()->orderBy('name')->get()->toArray();
        $data['tags'] = Tag::alphabetical()->get()->toArray();

        $this->form_validation->set_rules('title', 'Title', 'required');
        $this->form_validation->set_rules('content', 'Content', 'required');
        $this->form_validation->set_rules('author_id', 'Author', 'required|numeric');

        if ($this->form_validation->run() === FALSE) {
            $this->load->view('templates/header', $data);
            $this->load->view('posts/edit', $data);
            $this->load->view('templates/footer');
        } else {
            $post_data = [
                'title' => $this->input->post('title'),
                'content' => $this->input->post('content'),
                'author_id' => $this->input->post('author_id'),
                'status' => $this->input->post('status'),
            ];

            try {
                $post = Post::find($id);
                if ($post && $post->update($post_data)) {
                    // Sync tags
                    $tags = $this->input->post('tags');
                    if (is_array($tags)) {
                        $post->tags()->sync($tags);
                    } else {
                        // Clear all tags if none selected
                        $post->tags()->sync([]);
                    }
                    
                    redirect('view/' . $id);
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
                redirect('/');
            } else {
                show_error('Unable to delete post');
            }
        } catch (Exception $e) {
            log_message('error', 'Failed to delete post: ' . $e->getMessage());
            show_error('Unable to delete post');
        }
    }

    /**
     * Show posts by tag.
     */
    public function tag($slug = NULL)
    {
        if (!$slug) {
            show_404();
        }

        $tag = Tag::where('slug', $slug)->first();
        if (!$tag) {
            show_404();
        }

        $posts = Post::with(['author', 'tags'])
            ->published()
            ->withTag($slug)
            ->latest()
            ->limit(10)
            ->get();

        $data['posts'] = $posts->toArray();
        $data['tag'] = $tag->toArray();
        $data['title'] = 'Posts tagged with: ' . $tag->name;
        $data['popular_tags'] = Tag::popular(10)->get()->toArray();

        $this->load->view('templates/header', $data);
        $this->load->view('posts/tag', $data);
        $this->load->view('templates/footer');
    }

    /**
     * Show posts by author.
     */
    public function author($id = NULL)
    {
        if (!$id) {
            show_404();
        }

        $author = Author::active()->find($id);
        if (!$author) {
            show_404();
        }

        $posts = Post::with(['author', 'tags'])
            ->published()
            ->byAuthor($id)
            ->latest()
            ->limit(10)
            ->get();

        $data['posts'] = $posts->toArray();
        $data['author'] = $author->toArray();
        $data['title'] = 'Posts by: ' . $author->name;
        $data['popular_tags'] = Tag::popular(10)->get()->toArray();

        $this->load->view('templates/header', $data);
        $this->load->view('posts/author', $data);
        $this->load->view('templates/footer');
    }

    /**
     * Search endpoint.
     */
    public function search()
    {
        $query = $this->input->get('q');
        
        if (!$query) {
            redirect('/');
        }

        // Redirect to index with search parameter
        redirect('/?search=' . urlencode($query));
    }

    /**
     * API endpoint to get posts as JSON.
     */
    public function api()
    {
        $search = $this->input->get('search');
        $tag = $this->input->get('tag');
        $author = $this->input->get('author');
        $limit = $this->input->get('limit') ?: 10;

        $query = Post::with(['author', 'tags'])->published()->latest();

        if ($search) {
            $query->search($search);
        }

        if ($tag) {
            $query->withTag($tag);
        }

        if ($author) {
            $query->byAuthor($author);
        }

        $posts = $query->limit($limit)->get();

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($posts->toArray()));
    }
} 
