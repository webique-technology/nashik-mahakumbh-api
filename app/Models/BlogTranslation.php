<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlogTranslation extends Model
{
    protected $fillable = [
        'blog_id',
        'language_code',
        'title',
        'description'
    ];

    public function blog()
    {
        return $this->belongsTo(Blog::class);
    }
}
