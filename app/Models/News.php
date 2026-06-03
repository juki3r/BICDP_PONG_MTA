<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    protected $fillable = [
        'barangay',
        'municipality',
        'title',
        'content',
        'category',
        'image',
        'status',
        'priority',
        'user_id',
        'published_at',
    ];

    public function views()
    {
        return $this->hasMany(NewsView::class);
    }
}
