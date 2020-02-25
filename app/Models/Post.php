<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\Models\Category;
use App\Models\Comment;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use Sluggable, SoftDeletes;

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

    public function category()
    {
    	return $this->belongsTo('App\Models\Category');
    }

    public function user()
    {
    	return $this->belongsTo('App\Models\User');
    }

    public function admin()
    {
    	return $this->belongsTo('App\Models\Admin');
    }

    public function comment()
    {
    	return $this->belongsTo('App\Models\Comment');
    }

    public function earnablePosts()
    {
    	$posts = self::select('*')->where('post_type', 'earnable')->get();

    	return $posts;
    }

    public function myPosts()
    {
    	$posts = self::select('*')->where('user_id', Auth::user()->id)->get();

    	return $posts;
    }

    public function posts($userId, $isApproved)
    {
        $posts = "";

        if($userId != null)
        {
            $posts = self::select('*')->where('user_id', Auth::user()->id);
        }
        elseif ($isApproved == true) {
            $posts = self::select("*")->where('isApproved', true);
        }else{
            $posts = self::select("*");
        }

        return $posts->orderBy('created_at', 'desc')->paginate(30);
    }

    // public function allPosts()
    // {
    //     $posts = self::select("*")->orderBy('created_at', 'desc')->get();

    //     return $posts;
    // }

    // Get Approved Posts
    // public function approvedposts()
    // {
    //     $posts = self::select("*")->where('isApproved', true)->orderBy('created_at', 'desc')->get();

    //     return $posts;
    // }

    public function pinPosts()
    {
    	$posts = self::select('*')
                ->where(function ($query) {
                    $query->where('post_type', 'pin')
                    ->orWhere('post_type', 'admin');
                })
                ->where('created_at', '>=', Carbon::now()->subDay())
                ->orderBy('created_at', 'desc')
                ->get();

    	return $posts;
    }

    public static function countPosts()
    {
        $posts     =   self::count();

        return $posts;
    }

    public function getPostsByCategeory($slug)
    {
        $category = Category::where('slug', $slug)->first();

        $categoryId = $category->id;

        $posts = self::where('category_id', $categoryId)->paginate(30);

        return $posts;
    }

    public function getCommentsByPost($slug)
    {
        $post = self::where('slug', $slug)->first();

        $postId = $post->id;

        $comments = Comment::where('post_id', $postId)->paginate(30);

        return $comments;
    }
}
