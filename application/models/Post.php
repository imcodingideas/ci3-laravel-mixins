<?php

defined('BASEPATH') || exit('No direct script access allowed');

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $table = 'posts';
    
    protected $fillable = [
        'title',
        'content', 
        'author',
        'author_id',
        'status'
    ];
    
    protected $attributes = [
        'author' => 'Anonymous',
        'status' => 'published'
    ];
    
    public $timestamps = true;
    
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    
    // Scope for getting published posts
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }
    
    // Scope for getting posts by status
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }
    
    // Scope for ordering by creation date
    public function scopeLatest($query)
    {
        return $query->orderBy('created_at', 'desc');
    }
    
    // Accessor for formatted created date
    public function getFormattedCreatedAtAttribute()
    {
        return $this->created_at->format('F j, Y');
    }
    
    // Accessor for excerpt
    public function getExcerptAttribute($length = 200)
    {
        $content = strip_tags($this->content);
        return strlen($content) > $length ? substr($content, 0, $length) . '...' : $content;
    }
    
    /**
     * Get the author of this post
     */
    public function author()
    {
        return $this->belongsTo(Author::class, 'author_id');
    }
    
    /**
     * Get all tags for this post
     */
    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'post_tags', 'post_id', 'tag_id')
                    ->withPivot('created_at');
    }
    
    /**
     * Scope for searching posts by title, content, or author
     */
    public function scopeSearch($query, $term)
    {
        return $query->where(function($q) use ($term) {
            $q->where('title', 'LIKE', "%{$term}%")
              ->orWhere('content', 'LIKE', "%{$term}%")
              ->orWhere('author', 'LIKE', "%{$term}%")
              ->orWhereHas('author', function($authorQuery) use ($term) {
                  $authorQuery->where('name', 'LIKE', "%{$term}%");
              })
              ->orWhereHas('tags', function($tagQuery) use ($term) {
                  $tagQuery->where('name', 'LIKE', "%{$term}%");
              });
        });
    }
    
    /**
     * Scope for full-text search
     */
    public function scopeFullTextSearch($query, $term)
    {
        return $query->whereRaw(
            "MATCH(title, content) AGAINST(? IN BOOLEAN MODE)",
            [$term]
        );
    }
    
    /**
     * Scope for filtering by tag
     */
    public function scopeWithTag($query, $tagSlug)
    {
        return $query->whereHas('tags', function($tagQuery) use ($tagSlug) {
            $tagQuery->where('slug', $tagSlug);
        });
    }
    
    /**
     * Scope for filtering by author
     */
    public function scopeByAuthor($query, $authorId)
    {
        return $query->where('author_id', $authorId);
    }
    
    /**
     * Get the post's author name (fallback to author field if no author relationship)
     */
    public function getAuthorNameAttribute()
    {
        return $this->author ? $this->author->name : $this->author;
    }
    
    /**
     * Get the post's URL
     */
    public function getUrlAttribute()
    {
        return site_url('posts/view/' . $this->id);
    }
} 
