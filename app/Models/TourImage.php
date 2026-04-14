<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TourImage extends Model
{
    public $timestamps = false;

    protected $fillable = ['tour_id', 'image_url'];

    public function tour(): BelongsTo
    {
        return $this->belongsTo(Tour::class);
    }
}
