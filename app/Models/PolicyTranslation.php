<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PolicyTranslation extends Model
{
     protected $fillable = [
        'policy_id',
        'language_code',
        'content'
    ];

    public function policy()
    {
        return $this->belongsTo(
            Policy::class
        );
    }
}
