<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Calendar extends Model
{
    use HasFactory;
    public $table = 'calendars';

    public function expertDetail()
    {
        return $this->belongsTo(ExpertDetail::class);
    }
}

