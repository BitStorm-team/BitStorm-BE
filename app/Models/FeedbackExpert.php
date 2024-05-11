<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class FeedbackExpert extends Model
{
    use HasFactory;
    protected $fillable = [
        'booking_id',
    ];
    public function booking()
    {
        return $this->belongsTo(Booking::class,'booking_id');
    }
    
}
