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
        return $this->belongsTo(Role::class);
    }
    public function calendar()
    {
        return $this->hasOne(Calendar::class);
    }
}
