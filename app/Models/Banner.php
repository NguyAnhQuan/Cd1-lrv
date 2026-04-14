<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    public $timestamps = false;

    protected $fillable = ['image', 'title', 'link', 'placement'];
}
