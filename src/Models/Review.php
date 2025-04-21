<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model {

    protected $table = 'reviews';
    
    protected $fillable = [
        'hotel_id', 'platform', 'hotel_name', 'rating',
        'review_text', 'review_date', 'country', 'language',
        'provider_id', 'extended_ratings'
    ];
    
    protected $casts = [
        'extended_ratings' => 'array'
    ];
    
    public $timestamps = false;
}
