<?php

namespace App\Models;
use App\Models\SponsoredPost;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SponsoredComment extends Model
{
    use SoftDeletes;
    
    protected $table = "sponsored_comments";
    public function user()
    {
    	return $this->belongsTo('App\Models\User');
    }

    public function sponsored_post()
    {
    	return $this->belongsTo('App\Models\SponsoredPost');
    }

    public static function countPostComment($slug)
    {
    	$post = SponsoredPost::where('slug', $slug)->first();

        $postId = $post->id;

        $comments = self::where('sponsored_post_id', $postId)->count();

        return $comments;
    }

    public function lastComment($slug)
    {
    	$post = SponsoredPost::where('slug', $slug)->first();

        $postId = $post->id;

        $comment = self::where('sponsored_post_id', $postId)->orderBy('created_at', 'desc')->first();

        return $comment;
    }
}
