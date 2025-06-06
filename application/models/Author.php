<?php

defined('BASEPATH') || exit('No direct script access allowed');

use Illuminate\Database\Eloquent\Model;

class Author extends Model
{
    protected $table = 'authors';
    
    protected $fillable = [
        'name',
        'email',
        'bio',
        'avatar',
        'website',
        'social_media',
        'status'
    ];
    
    protected $attributes = [
        'status' => 'active'
    ];
    
    public $timestamps = true;
    
    protected $casts = [
        'social_media' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    
    /**
     * Get all posts by this author
     */
    public function posts()
    {
        return $this->hasMany(Post::class, 'author_id');
    }
    
    /**
     * Get only published posts by this author
     */
    public function publishedPosts()
    {
        return $this->posts()->where('status', 'published');
    }
    
    /**
     * Scope for getting active authors
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
    
    /**
     * Scope for searching authors by name or email
     */
    public function scopeSearch($query, $term)
    {
        return $query->where(function($q) use ($term) {
            $q->where('name', 'LIKE', "%{$term}%")
              ->orWhere('email', 'LIKE', "%{$term}%")
              ->orWhere('bio', 'LIKE', "%{$term}%");
        });
    }
    
    /**
     * Scope for full-text search
     */
    public function scopeFullTextSearch($query, $term)
    {
        return $query->whereRaw(
            "MATCH(name, bio) AGAINST(? IN BOOLEAN MODE)",
            [$term]
        );
    }
    
    /**
     * Get the author's display name
     */
    public function getDisplayNameAttribute()
    {
        return $this->name;
    }
    
    /**
     * Get the author's avatar URL
     */
    public function getAvatarUrlAttribute()
    {
        if ($this->avatar) {
            return base_url('uploads/avatars/' . $this->avatar);
        }
        
        // Generate Gravatar URL as fallback
        $hash = md5(strtolower(trim($this->email)));
        return "https://www.gravatar.com/avatar/{$hash}?d=identicon&s=80";
    }
    
    /**
     * Get the author's social media links
     */
    public function getSocialLinksAttribute()
    {
        $social = $this->social_media ?: [];
        $links = [];
        
        $platforms = [
            'twitter' => 'https://twitter.com/',
            'github' => 'https://github.com/',
            'linkedin' => 'https://linkedin.com/in/',
            'dribbble' => 'https://dribbble.com/',
            'behance' => 'https://behance.net/',
            'instagram' => 'https://instagram.com/'
        ];
        
        foreach ($social as $platform => $username) {
            if (isset($platforms[$platform])) {
                $username = str_replace('@', '', $username);
                $links[$platform] = $platforms[$platform] . $username;
            }
        }
        
        return $links;
    }
    
    /**
     * Get the number of published posts
     */
    public function getPostCountAttribute()
    {
        return $this->publishedPosts()->count();
    }
} 
