<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LikePost extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'post_id',
    ];
    protected static function boot()
    {
        parent::boot();

        static::created(function (LikePost $likePost) {
            $post = Post::find($likePost->post_id);
            if ($post) {
                $post->like_count++;
                $post->save();
            }
        });

        static::deleted(function (LikePost $likePost) {
            $post = Post::find($likePost->post_id);
            if ($post) {
                $post->like_count--;
                $post->save();
            }
        });
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}
