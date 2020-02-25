<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{
    use SoftDeletes;
    
    public function user()
    {
    	return $this->belongsTo('App\Models\User');
    }

    public function post()
    {
    	return $this->belongsTo('App\Models\Post');
    }

    public static function commentCount($postId)
    {
    	$comments = self::where('post_id', $postId)->count();

    	return $comments;
    }

    public static function countPostComment($slug)
    {
        $post = Post::where('slug', $slug)->first();

        $postId = $post->id;

        $comments = self::where('post_id', $postId)->count();

        return $comments;
    }

    public function lastComment($slug)
    {
        $post = Post::where('slug', $slug)->first();

        $postId = $post->id;

        $comment = self::where('post_id', $postId)->orderBy('created_at', 'desc')->first();

        return $comment;
    }

}
