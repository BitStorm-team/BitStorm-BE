<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;


class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'email_verified_at',
        'password',
        'address',
        'profile_picture',
        'date_of_birth',
        'phone_number',
        'gender',
        'remember_token',
        'role_id',
        'created_at',
        'updated_at'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }
    public function expert()
    {
        return $this->hasOne(ExpertDetail::class);
    }
    protected static function boot()
    {
        parent::boot();

        static::created(function ($user) {
            if ($user->role_id == 3) {
                // Nếu người dùng có role_id = 3, cập nhật user_id trong bảng expert_details
                ExpertDetail::updateOrCreate(['user_id' => $user->id], ['user_id' => $user->id]);
            }
        });
    }
    public function contacts()
    {
        return $this->hasMany(Contact::class);
    }
    public function posts(){
        return $this->hasMany(Post::class);
    }
    public function commentsPosts(){
        return $this-> hasMany(CommentsPost::class);
    }
    public function bookings(){
        return $this-> hasMany(Booking::class);
    }
}
