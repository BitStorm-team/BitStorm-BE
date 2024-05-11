<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;
    public $table = 'contacts';
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function expertDetail()
    {
        return $this->belongsTo(ExpertDetail::class);
    }
}
