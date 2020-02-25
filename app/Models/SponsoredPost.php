<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\Models\SponsoredComment;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;

class SponsoredPost extends Model
{
    use Sluggable, SoftDeletes;

    protected $table = "sponsored_posts";

	/**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable()
    {
        return [
            'slug' => [
                'source' => 'title'
            ]
        ];
    }

    public function admin()
    {
        return $this->belongsTo('App\Models\Admin');
    }

    public function sponsored_comment()
    {
        return $this->hasMany('App\Models\SponsoredComment');
    }

    public function sponsoredPost()
    {
    	$posts = self::select('*')
                ->where('created_at', '>=', Carbon::now()->subDay())
                ->first();

    	return $posts;
    }

    public static function countPosts()
    {
        $posts     =   self::count();

        return $posts;
    }

    public function getCommentsBySponsoredPost($slug)
    {
        $post = self::where('slug', $slug)->first();

        $postId = $post->id;

        $comments = SponsoredComment::where('sponsored_post_id', $postId)->paginate(30);

        return $comments;
    }

    public function sponsoredPosts($adminId)
    {
        $posts = "";
        if($adminId != null)
        {
            $posts = self::where('admin_id', $adminId);
        }else{
            $posts = self::select('*');
        }

        return $posts->orderBy('created_at', 'desc')->paginate(30);
    }
}
