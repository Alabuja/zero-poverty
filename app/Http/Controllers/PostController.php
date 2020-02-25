<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Post;
use App\Models\Comment;
use App\Models\Activity;
use App\Models\Category;
use Illuminate\Pagination\LengthAwarePaginator;

class PostController extends Controller
{
    public function __construct(Post $post, Activity $activity, Category $category, Comment $comment)
	{
		$this->post       = $post;
        $this->activity   =   $activity;
        $this->category   = $category;
        $this->comment    = $comment;
	}

    public function store(Request $request)
    {
    	$this->validate($request, [
    		'title'       	=> 	'required',
    		'category_id' 	=> 	'required|numeric',
            'body'			=>	'required',
            'post_type'     =>  'required'
    	]);

        $userId = Auth::user()->id;
        
        $adminId = Auth::guard('admin')->user()->id;

        if($adminId != null)
        {
            $newPost = Post::create([
                'title' => $request->title,
                'category_id'   =>  $request->category_id,
                'admin_id'       =>  $adminId,
                'body'          =>  $request->body,
                'post_type'     =>  $request->post_type,
                'isApproved'    =>  true
            ]);
        }
        else{
            $newPost = Post::create([
                'title' => $request->title,
                'category_id'   =>  $request->category_id,
                'user_id'       =>  $userId,
                'body'          =>  $request->body,
                'post_type'     =>  $request->post_type
            ]);
        }

        return response()->json($newPost, 201);
    }

    public function post($slug)
    {
        $post = Post::select('*')->where('slug', $slug)->first();
        
        $postId = $post->id;
        $post->increment('visitCount');

        $viewCount = $post->visitCount;

        $lastComment = $this->comment->lastComment($slug);

        return response()->json([
            'post'      =>  $post,
            'viewCount' =>  $viewCount,
            'lastComment' => $lastComment
        ]);
    }

    public function posts($userId, $isApproved)
    {
        $posts = $this->post->posts($userId, $isApproved);

        return response()->json($posts);
    }

    public function approveOrRejectPosts(Request $request, $postId, $isApproved)
    {
        if($isApproved == true)
        {
            $updatedPost = $this->post::where('id', $postId)->update(['isApproved' => true]);
        
            $post = $this->post::where('id', $postId)->first();
            $userId = $post->userId;

            Activity::create([
                'user_id'   =>  $userId,
                'activity_type' =>  'posts',
                'amount_earned' =>  '50'
            ]);
            
            return response()->json(
                ['message' => 'Post has been rejected!', 'response' => $updatedPost]
                , 200
            );
        }
        else
        {
            $rejectedPost = $this->post::where('id', $postId)->update(['isApproved' => false]);

            return response()->json(
                [
                    'message' => 'Post has been rejected!',
                    'response'  =>  $rejectedPost
                ]
                , 200
            );
        }
    }

    public function deletePosts(Request $request, $postId)
    {
        $this->post::where('id', $postId)->delete();

        return response()->json(null
            , 204
        );
    }

    public function getPinPosts()
    {
        $pinPost       =   $this->post->pinPosts();

        return response()->json($pinPost);
    }

    public function getCommentsByPost($slug)
    {
        $commentsByPost = $this->post->getCommentsByPost($slug);

        return response()->json($commentsByPost);
    }

    public function countPosts()
    {
        $count = $this->post->countPosts();

        return response()->json($count);
    }
}
