<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Policy extends Model
{
    protected $fillable = [
        'type',
        'content'
    ];

    public function translations()
    {
        return $this->hasMany(
            PolicyTranslation::class
        );
    }
}
