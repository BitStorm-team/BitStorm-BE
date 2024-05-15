<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpertDetail extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'experience', 'certificate'];
    protected $primaryKey = 'user_id';
    public function getAllExpert()
    {
        $experts = User::with('expert')->where('role_id','=',3)->get();
        return $experts;
    }
    public function getExpertProfile($id){
        $expert = User::with('expert')->where('role_id','=',3)->find($id);
        return $expert;
    }
    public function getListExpert()
    {
        $experts = User::with('expert')->where('role_id','=',3)
        ->get();
        return $experts;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function contacts()
    {
        return $this->hasMany(Contact::class);
    }

    public function calendars()
    {
        return $this->hasMany(Calendar::class, 'expert_id', 'id');
    }


}
