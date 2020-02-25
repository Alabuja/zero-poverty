<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SponsoredPost;
use App\Models\SponsoredComment;
use App\Models\Activity;
use Cloudder;
use Illuminate\Pagination\LengthAwarePaginator;

class SponsoredPostController extends Controller
{
    public function __construct(SponsoredPost $sponsoredPost, Activity $activity, SponsoredComment $sponsoredComment)
    {
    	$this->sponsoredPost = $sponsoredPost;
        $this->activity         =   $activity;
        $this->sponsoredComment         =   $sponsoredComment;
    }

    public function sponsoredPosts($adminId)
    {
        $posts = $this->sponsoredPost->sponsoredPosts($adminId);

        return response()->json($posts);
    }

    public function store(Request $request)
    {
    	$this->validate($request, [
    		'title' => 'required',
    		'body'	=>	'required',
            'image' =>  'required|mimes:jpeg,jpg,png',
    	]);

        $userId = Auth::guard('admin')->user()->id;

        $image                          =   $request->file('image')->getRealPath();

        Cloudder::upload($image, null);
        list($width, $height)                = getimagesize($image);

        $url = Cloudder::secureShow(Cloudder::getPublicId(), [
            "crop" => "fit", "width" => 200, "height" => 200
        ]);
        
        $sponsoredPost = SponsoredPost::create(['title' => $request->title, 'body' => $request->body, 'admin_id' => $userId, 'image_url' => $url]);

    	return response()->json($sponsoredPost);
    }

    public function sponsoredPost($slug)
    {
        $post = SponsoredPost::where('slug', $slug)->first();

        $postId = $post->id;
        $post->increment('visitCount');

        $viewCount = $post->visitCount;

        $lastComment = $this->sponsoredComment->lastComment($slug);

        return response()->json([
            'post'          =>  $post,
            'lastComment'   =>  $lastComment,
            'viewCount'     =>  $viewCount
        ]);
    }

    public function currentSponsoredPost()
    {
        $currentPost = $this->sponsoredPost->sponsoredPost();

        return response()->json($currentPost);
    }

    public function getCommentsBySponsoredPost($slug)
    {
        $commentsByPost = $this->sponsoredPost->getCommentsBySponsoredPost($slug);

        return response()->json($commentsByPost);
    }

    public function countPosts()
    {
        $count = $this->sponsoredPost->countPosts();

        return response()->json($count);
    }
    
}
