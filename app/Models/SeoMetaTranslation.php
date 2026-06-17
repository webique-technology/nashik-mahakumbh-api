<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SeoMetaTranslation extends Model
{
    protected $table = 'seo_meta_translations';

    protected $fillable = [
        'seo_meta_id',
        'language_code',
        'title',
        'desc'
    ];
}
