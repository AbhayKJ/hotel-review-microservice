<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $table = 'reviews';

    protected $fillable = [
        'hotel_id', 'platform', 'hotel_name', 'review_text',
        'rating', 'review_date'
    ];

    public $timestamps = true;
}
