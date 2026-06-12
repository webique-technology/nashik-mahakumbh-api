<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
     protected $fillable = [
        'title',
        'description',
        'category',
        'image',
        'slug'
    ];
    public function translations()
    {
        return $this->hasMany(BlogTranslation::class);
    }
}
