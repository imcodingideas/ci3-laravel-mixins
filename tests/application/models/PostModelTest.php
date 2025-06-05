<?php

use PHPUnit\Framework\TestCase;

#[\AllowDynamicProperties]
class PostModelTest extends TestCase
{
    private $CI;
    private $post_model;

    protected function setUp(): void
    {
        parent::setUp();

        $this->CI = &get_instance();
        $this->CI->load->model('Post_model');
        $this->post_model = $this->CI->Post_model;

        // Reset database before each test
        reset_test_database();
    }

    public function testGetPostsReturnsArray()
    {
        $posts = $this->post_model->get_posts();

        $this->assertIsArray($posts);
        $this->assertCount(3, $posts); // Should have 3 sample posts
    }

    public function testGetPostById()
    {
        $posts = $this->post_model->get_posts();
        $first_post_id = $posts[0]['id'];

        $post = $this->post_model->get_post($first_post_id);

        $this->assertIsArray($post);
        $this->assertEquals($first_post_id, $post['id']);
        $this->assertArrayHasKey('title', $post);
        $this->assertArrayHasKey('content', $post);
        $this->assertArrayHasKey('author', $post);
    }

    public function testGetNonexistentPostReturnsNull()
    {
        $post = $this->post_model->get_post(999);

        $this->assertNull($post);
    }

    public function testCreatePost()
    {
        $post_data = [
            'title' => 'Test Post',
            'content' => 'This is test content',
            'author' => 'Test Author',
            'status' => 'published',
        ];

        $post_id = $this->post_model->create_post($post_data);

        $this->assertIsInt($post_id);
        $this->assertGreaterThan(0, $post_id);

        // Verify post was created
        $created_post = $this->post_model->get_post($post_id);
        $this->assertEquals($post_data['title'], $created_post['title']);
        $this->assertEquals($post_data['content'], $created_post['content']);
        $this->assertEquals($post_data['author'], $created_post['author']);
    }

    public function testCreatePostWithDefaults()
    {
        $post_data = [
            'title' => 'Test Post',
            'content' => 'This is test content',
        ];

        $post_id = $this->post_model->create_post($post_data);
        $created_post = $this->post_model->get_post($post_id);

        $this->assertEquals('Anonymous', $created_post['author']);
        $this->assertEquals('published', $created_post['status']);
    }

    public function testUpdatePost()
    {
        // Create a post first
        $post_data = [
            'title' => 'Original Title',
            'content' => 'Original content',
            'author' => 'Original Author',
        ];

        $post_id = $this->post_model->create_post($post_data);

        // Update the post
        $update_data = [
            'title' => 'Updated Title',
            'content' => 'Updated content',
        ];

        $result = $this->post_model->update_post($post_id, $update_data);

        $this->assertTrue($result);

        // Verify update
        $updated_post = $this->post_model->get_post($post_id);
        $this->assertEquals($update_data['title'], $updated_post['title']);
        $this->assertEquals($update_data['content'], $updated_post['content']);
        $this->assertEquals('Original Author', $updated_post['author']); // Should remain unchanged
    }

    public function testDeletePost()
    {
        // Create a post first
        $post_data = [
            'title' => 'Post to Delete',
            'content' => 'This will be deleted',
        ];

        $post_id = $this->post_model->create_post($post_data);

        // Verify post exists
        $this->assertNotNull($this->post_model->get_post($post_id));

        // Delete the post
        $result = $this->post_model->delete_post($post_id);

        $this->assertTrue($result);

        // Verify post is deleted
        $this->assertNull($this->post_model->get_post($post_id));
    }

    public function testCountPosts()
    {
        $count = $this->post_model->count_posts();

        $this->assertEquals(3, $count); // Should have 3 sample posts

        // Add a post and verify count increases
        $post_data = [
            'title' => 'New Post',
            'content' => 'New content',
        ];

        $this->post_model->create_post($post_data);

        $new_count = $this->post_model->count_posts();
        $this->assertEquals(4, $new_count);
    }

    public function testGetPostsByStatus()
    {
        // Create posts with different statuses
        $this->post_model->create_post([
            'title' => 'Draft Post',
            'content' => 'Draft content',
            'status' => 'draft',
        ]);

        $this->post_model->create_post([
            'title' => 'Archived Post',
            'content' => 'Archived content',
            'status' => 'archived',
        ]);

        // Test published posts (default)
        $published_posts = $this->post_model->get_posts_by_status('published');
        $this->assertCount(3, $published_posts); // Original 3 sample posts

        // Test draft posts
        $draft_posts = $this->post_model->get_posts_by_status('draft');
        $this->assertCount(1, $draft_posts);
        $this->assertEquals('draft', $draft_posts[0]['status']);

        // Test archived posts
        $archived_posts = $this->post_model->get_posts_by_status('archived');
        $this->assertCount(1, $archived_posts);
        $this->assertEquals('archived', $archived_posts[0]['status']);
    }

    public function testGetPostsWithLimit()
    {
        $posts = $this->post_model->get_posts(2);

        $this->assertCount(2, $posts);
    }

    public function testGetPostsWithOffset()
    {
        $all_posts = $this->post_model->get_posts();
        $posts_with_offset = $this->post_model->get_posts(10, 1);

        $this->assertEquals($all_posts[1]['id'], $posts_with_offset[0]['id']);
    }
} 
