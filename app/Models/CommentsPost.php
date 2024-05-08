<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommentsPost extends Model
{
    use HasFactory;
    protected $fillable = ['post_id', 'user_id', 'content', 'status'];
    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function replies()
    {
        return $this->hasMany(RepliesPost::class,'comment_post_id');
    }
    protected static function boot()
    {
        parent::boot();

        static::created(function (CommentsPost $commentPost) {
            $post = Post::find($commentPost->post_id);
            if ($post) {
                $post->comment_count++;
                $post->save();
            }
        });
    }
}
