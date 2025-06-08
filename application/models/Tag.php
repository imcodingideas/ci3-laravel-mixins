<?php

defined('BASEPATH') || exit('No direct script access allowed');

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $table = 'tags';
    
    protected $fillable = [
        'name',
        'slug',
        'description',
        'color'
    ];
    
    protected $attributes = [
        'color' => '#007bff'
    ];
    
    public $timestamps = true;
    
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    
    /**
     * Get all posts with this tag
     */
    public function posts()
    {
        return $this->belongsToMany(Post::class, 'post_tags', 'tag_id', 'post_id')
                    ->withPivot('created_at');
    }
    
    /**
     * Get only published posts with this tag
     */
    public function publishedPosts()
    {
        return $this->posts()->where('posts.status', 'published');
    }
    
    /**
     * Scope for searching tags by name or slug
     */
    public function scopeSearch($query, $term)
    {
        return $query->where(function($q) use ($term) {
            $q->where('name', 'LIKE', "%{$term}%")
              ->orWhere('slug', 'LIKE', "%{$term}%")
              ->orWhere('description', 'LIKE', "%{$term}%");
        });
    }
    
    /**
     * Scope for getting tags by popularity (post count)
     */
    public function scopePopular($query, $limit = 10)
    {
        return $query->withCount(['publishedPosts'])
                    ->orderBy('published_posts_count', 'desc')
                    ->limit($limit);
    }
    
    /**
     * Scope for ordering by name
     */
    public function scopeAlphabetical($query)
    {
        return $query->orderBy('name', 'asc');
    }
    
    /**
     * Generate slug from name
     */
    public static function generateSlug($name)
    {
        $slug = strtolower(trim($name));
        $slug = preg_replace('/[^a-z0-9-]/', '-', $slug);
        $slug = preg_replace('/-+/', '-', $slug);
        $slug = trim($slug, '-');
        
        // Ensure uniqueness
        $originalSlug = $slug;
        $counter = 1;
        
        while (static::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }
        
        return $slug;
    }
    
    /**
     * Get the tag's display name with color
     */
    public function getDisplayNameAttribute()
    {
        return $this->name;
    }
    
    /**
     * Get the tag's URL
     */
    public function getUrlAttribute()
    {
        return site_url('posts/tag/' . $this->slug);
    }
    
    /**
     * Get the number of published posts with this tag
     */
    public function getPostCountAttribute()
    {
        return $this->publishedPosts()->count();
    }
    
    /**
     * Mutator to automatically generate slug when setting name
     */
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = $value;
        
        if (!$this->slug) {
            $this->attributes['slug'] = self::generateSlug($value);
        }
    }
    
    /**
     * Validate hex color
     */
    public function setColorAttribute($value)
    {
        if (preg_match('/^#[0-9A-Fa-f]{6}$/', $value)) {
            $this->attributes['color'] = $value;
        } else {
            $this->attributes['color'] = '#007bff';
        }
    }
} 
