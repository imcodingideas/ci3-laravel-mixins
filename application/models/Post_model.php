<?php
defined('BASEPATH') || exit('No direct script access allowed');

class Post_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Get all posts
     * 
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function get_posts($limit = 10, $offset = 0)
    {
        $this->db->limit($limit, $offset);
        $this->db->order_by('created_at', 'DESC');
        $query = $this->db->get('posts');
        return $query->result_array();
    }

    /**
     * Get post by ID
     * 
     * @param int $id
     * @return array|null
     */
    public function get_post($id)
    {
        $query = $this->db->get_where('posts', array('id' => $id));
        return $query->row_array();
    }

    /**
     * Create new post
     * 
     * @param array $data
     * @return int|bool
     */
    public function create_post($data)
    {
        $post_data = array(
            'title' => $data['title'],
            'content' => $data['content'],
            'author' => isset($data['author']) ? $data['author'] : 'Anonymous',
            'status' => isset($data['status']) ? $data['status'] : 'published',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        );

        if ($this->db->insert('posts', $post_data)) {
            return $this->db->insert_id();
        }
        return false;
    }

    /**
     * Update post
     * 
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update_post($id, $data)
    {
        $post_data = array(
            'updated_at' => date('Y-m-d H:i:s')
        );

        if (isset($data['title'])) {
            $post_data['title'] = $data['title'];
        }
        if (isset($data['content'])) {
            $post_data['content'] = $data['content'];
        }
        if (isset($data['author'])) {
            $post_data['author'] = $data['author'];
        }
        if (isset($data['status'])) {
            $post_data['status'] = $data['status'];
        }

        $this->db->where('id', $id);
        return $this->db->update('posts', $post_data);
    }

    /**
     * Delete post
     * 
     * @param int $id
     * @return bool
     */
    public function delete_post($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete('posts');
    }

    /**
     * Count total posts
     * 
     * @return int
     */
    public function count_posts()
    {
        return $this->db->count_all('posts');
    }

    /**
     * Get posts by status
     * 
     * @param string $status
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function get_posts_by_status($status = 'published', $limit = 10, $offset = 0)
    {
        $this->db->where('status', $status);
        $this->db->limit($limit, $offset);
        $this->db->order_by('created_at', 'DESC');
        $query = $this->db->get('posts');
        return $query->result_array();
    }
} 
