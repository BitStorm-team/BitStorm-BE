<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;
    public $table = 'bookings';
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function calendar()
    {
        return $this->belongsTo(Calendar::class, 'calendar_id');
    }
    public function feedbackExpert()
    {
        return $this->hasOne(FeedbackExpert::class,'booking_id');
    }
}

