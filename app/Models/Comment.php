<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'user_id',
        'post_id',
        'content',
        'status',
        'created_at',
        'updated_at',
        'parent_id'
    ];

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
        return $this->hasMany(Comment::class, 'parent_id')->with('replies');
    }

    protected static function boot()
    {
        parent::boot();

        static::created(function (Comment $commentPost) {
            $post = Post::find($commentPost->post_id);
            if ($post) {
                $post->comment_count++;
                $post->save();
            }
        });
    }
}
