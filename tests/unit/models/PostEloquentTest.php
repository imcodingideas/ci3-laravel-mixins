<?php

use PHPUnit\Framework\TestCase;

#[\AllowDynamicProperties]
class PostEloquentTest extends TestCase
{
    private $CI;

    protected function setUp(): void
    {
        parent::setUp();

        $this->CI = &get_instance();
        
        // Initialize Eloquent
        $this->_initEloquent();
        
        // Load the eloquent model directly
        require_once APPPATH . 'models/Post.php';

        // Reset database before each test
        reset_test_database();
    }

    private function _initEloquent()
    {
        require_once APPPATH . '../vendor/autoload.php';
        
        $capsule = new \Illuminate\Database\Capsule\Manager;
        
        // Use CodeIgniter database configuration
        $capsule->addConnection([
            'driver'    => 'mysql',
            'host'      => $this->CI->db->hostname,
            'database'  => $this->CI->db->database,
            'username'  => $this->CI->db->username,
            'password'  => $this->CI->db->password,
            'port'      => $this->CI->db->port ?? 3306,
            'charset'   => $this->CI->db->char_set,
            'collation' => $this->CI->db->dbcollat,
            'prefix'    => $this->CI->db->dbprefix,
        ]);
        
        // Make this Capsule instance available globally via static methods
        $capsule->setAsGlobal();
        
        // Setup the Eloquent ORM
        $capsule->bootEloquent();
    }

    public function testEloquentModelIsLoaded()
    {
        $this->assertTrue(class_exists('Post'));
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Model', new Post());
    }

    public function testEloquentCreatePost()
    {
        $post_data = [
            'title' => 'Eloquent Test Post',
            'content' => 'This is an Eloquent test content',
            'author' => 'Eloquent Test Author',
            'status' => 'published',
        ];

        $post = Post::create($post_data);

        $this->assertInstanceOf('Post', $post);
        $this->assertEquals($post_data['title'], $post->title);
        $this->assertEquals($post_data['content'], $post->content);
        $this->assertEquals($post_data['author'], $post->author);
        $this->assertEquals($post_data['status'], $post->status);
        $this->assertNotNull($post->id);
    }

    public function testEloquentCreatePostWithDefaults()
    {
        $post_data = [
            'title' => 'Test Post with Defaults',
            'content' => 'Test content',
        ];

        $post = Post::create($post_data);

        $this->assertEquals('Anonymous', $post->author);
        $this->assertEquals('published', $post->status);
    }

    public function testEloquentFindPost()
    {
        $post = Post::create([
            'title' => 'Findable Post',
            'content' => 'Content to find',
            'author' => 'Test Author',
        ]);

        $found_post = Post::find($post->id);

        $this->assertInstanceOf('Post', $found_post);
        $this->assertEquals($post->id, $found_post->id);
        $this->assertEquals($post->title, $found_post->title);
    }

    public function testEloquentUpdatePost()
    {
        $post = Post::create([
            'title' => 'Original Title',
            'content' => 'Original content',
            'author' => 'Original Author',
        ]);

        $update_data = [
            'title' => 'Updated Title via Eloquent',
            'content' => 'Updated content via Eloquent',
        ];

        $result = $post->update($update_data);

        $this->assertTrue($result);
        
        $post->refresh();
        $this->assertEquals($update_data['title'], $post->title);
        $this->assertEquals($update_data['content'], $post->content);
        $this->assertEquals('Original Author', $post->author); // Should remain unchanged
    }

    public function testEloquentDeletePost()
    {
        $post = Post::create([
            'title' => 'Post to Delete',
            'content' => 'This will be deleted',
        ]);

        $post_id = $post->id;
        $result = $post->delete();

        $this->assertTrue($result);
        $this->assertNull(Post::find($post_id));
    }

    public function testLatestScope()
    {
        // Test that latest scope orders by created_at desc
        $latest_posts = Post::latest()->get();

        // Should be ordered by created_at descending
        $this->assertGreaterThanOrEqual(1, $latest_posts->count());
        
        // Verify the ordering by checking that each subsequent post is older or equal
        for ($i = 0; $i < $latest_posts->count() - 1; $i++) {
            $this->assertGreaterThanOrEqual(
                $latest_posts[$i + 1]->created_at->timestamp,
                $latest_posts[$i]->created_at->timestamp
            );
        }
    }

    public function testPublishedScope()
    {
        Post::create(['title' => 'Published Post', 'content' => 'Published content', 'status' => 'published']);
        Post::create(['title' => 'Draft Post', 'content' => 'Draft content', 'status' => 'draft']);

        $published_posts = Post::published()->get();

        $this->assertGreaterThanOrEqual(1, $published_posts->count());
        foreach ($published_posts as $post) {
            $this->assertEquals('published', $post->status);
        }
    }

    public function testByStatusScope()
    {
        Post::create(['title' => 'Draft Post 1', 'content' => 'Draft content 1', 'status' => 'draft']);
        Post::create(['title' => 'Draft Post 2', 'content' => 'Draft content 2', 'status' => 'draft']);
        Post::create(['title' => 'Published Post', 'content' => 'Published content', 'status' => 'published']);

        $draft_posts = Post::byStatus('draft')->get();
        $published_posts = Post::byStatus('published')->get();

        $this->assertEquals(2, $draft_posts->count());
        $this->assertGreaterThanOrEqual(1, $published_posts->count());

        foreach ($draft_posts as $post) {
            $this->assertEquals('draft', $post->status);
        }
    }

    public function testFormattedCreatedAtAccessor()
    {
        $post = Post::create([
            'title' => 'Test Post',
            'content' => 'Test content',
        ]);

        $formatted_date = $post->formatted_created_at;

        $this->assertIsString($formatted_date);
        // Should match format like "June 6, 2025"
        $this->assertMatchesRegularExpression('/^[A-Za-z]+ \d{1,2}, \d{4}$/', $formatted_date);
    }

    public function testExcerptAccessor()
    {
        $long_content = str_repeat('This is a long content. ', 50); // About 1000 characters
        
        $post = Post::create([
            'title' => 'Post with Long Content',
            'content' => $long_content,
        ]);

        $excerpt = $post->getExcerptAttribute();

        $this->assertIsString($excerpt);
        $this->assertLessThanOrEqual(203, strlen($excerpt)); // 200 + "..."
        $this->assertStringEndsWith('...', $excerpt);
    }

    public function testExcerptAccessorWithShortContent()
    {
        $short_content = 'This is short content.';
        
        $post = Post::create([
            'title' => 'Post with Short Content',
            'content' => $short_content,
        ]);

        $excerpt = $post->getExcerptAttribute();

        $this->assertEquals($short_content, $excerpt);
        $this->assertStringEndsNotWith('...', $excerpt);
    }

    public function testTimestamps()
    {
        $post = Post::create([
            'title' => 'Timestamp Test',
            'content' => 'Testing timestamps',
        ]);

        $this->assertNotNull($post->created_at);
        $this->assertNotNull($post->updated_at);
        $this->assertInstanceOf('Illuminate\Support\Carbon', $post->created_at);
        $this->assertInstanceOf('Illuminate\Support\Carbon', $post->updated_at);

        $original_updated_at = $post->updated_at;
        
        // Update and check if updated_at changes
        sleep(1);
        $post->update(['title' => 'Updated Title']);
        $post->refresh();

        $this->assertNotEquals($original_updated_at, $post->updated_at);
    }

    public function testMassAssignmentProtection()
    {
        // Test that only fillable fields can be mass assigned
        $post_data = [
            'title' => 'Test Post',
            'content' => 'Test content',
            'author' => 'Test Author',
            'status' => 'published',
            'id' => 999, // This should be ignored due to mass assignment protection
        ];

        $post = Post::create($post_data);

        $this->assertNotEquals(999, $post->id);
        $this->assertEquals($post_data['title'], $post->title);
        $this->assertEquals($post_data['content'], $post->content);
        $this->assertEquals($post_data['author'], $post->author);
        $this->assertEquals($post_data['status'], $post->status);
    }

    public function testEloquentCollectionMethods()
    {
        Post::create(['title' => 'Post 1', 'content' => 'Content 1', 'status' => 'published']);
        Post::create(['title' => 'Post 2', 'content' => 'Content 2', 'status' => 'draft']);
        Post::create(['title' => 'Post 3', 'content' => 'Content 3', 'status' => 'published']);

        $posts = Post::all();

        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $posts);
        $this->assertGreaterThanOrEqual(3, $posts->count());

        // Test collection methods
        $published_posts = $posts->where('status', 'published');
        $this->assertGreaterThanOrEqual(2, $published_posts->count());

        $titles = $posts->pluck('title');
        $this->assertInstanceOf('Illuminate\Support\Collection', $titles);
    }

    public function testEloquentQueryBuilder()
    {
        Post::create(['title' => 'Alpha Post', 'content' => 'Alpha content', 'status' => 'published']);
        Post::create(['title' => 'Beta Post', 'content' => 'Beta content', 'status' => 'published']);
        Post::create(['title' => 'Gamma Post', 'content' => 'Gamma content', 'status' => 'draft']);

        // Test chaining query methods
        $posts = Post::where('status', 'published')
                     ->where('title', 'like', '%Post%')
                     ->orderBy('title', 'asc')
                     ->get();

        $this->assertGreaterThanOrEqual(2, $posts->count());
        foreach ($posts as $post) {
            $this->assertEquals('published', $post->status);
            $this->assertStringContainsString('Post', $post->title);
        }
    }
} 
