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
} 
