<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpertDetail extends Model
{
    use HasFactory;
    public function getAllExpert()
    {
        $experts = User::with('experts')->where('role_id','=',3)->get();
        return $experts;
    }
    public function user()
    {
        return $this->hasOne(User::class);
    }
    public function contacts()
    {
        return $this->hasMany(Contact::class);
    }

}
