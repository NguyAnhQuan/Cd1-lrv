<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'name',
        'description',
        'image',
        'status',
        'slug',
    ];

    public function tours(): HasMany
    {
        return $this->hasMany(Tour::class);
    }
}
