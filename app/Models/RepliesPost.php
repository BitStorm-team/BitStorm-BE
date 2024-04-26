<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RepliesPost extends Model
{
    use HasFactory;

    public function comment()
    {
        return $this->belongsTo(CommentsPost::class,'comment_post_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    protected static function boot()
    {
        parent::boot();
        
        static::created(function (RepliesPost $replyPost) {
            $commentPost = CommentsPost::find($replyPost->comment_post_id);
            if ($commentPost) {
                $post = Post::find($commentPost->post_id);
                if ($post) {
                    $post->comment_count++;
                    $post->save();
                }
            }
        });
    }
}
