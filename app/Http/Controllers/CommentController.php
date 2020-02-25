<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Comment;
use App\Models\Activity;
use App\Models\Post;

class CommentController extends Controller
{
    public function __construct(Comment $comment, Activity $activity, Post $post)
	{
		$this->comment = $comment;
        $this->activity = $activity;
		$this->post = $post;
	}

    public function store(Request $request, $slug)
    {
    	$this->validate($request, [
    		'body'		=>	'required'
    	]);

        $post = Post::where('slug', $slug)->first();
        $postId = $post->id;

		$userId = Auth::user()->id;
		
		$newComment = Comment::create([
			'post_id' 		=> $postId,
			'user_id'       =>  $userId,
			'body'          =>  $request->body
		]);

    	$count = $this->comment->where('user_id', $userId)->where('post_id', $postId)->count();

    	if($count <= 1)
    	{
    		$this->activity->user_id       =  $userId;

    		if($this->comment->post->post_type == 'admin' || $this->comment->post->post_type == 'pin')
    		{
    			$this->activity->activity_type = 'comment';
    		}
    		else
    		{
        		$this->activity->activity_type = 'forumcomment';
    		}
        	$this->activity->amount_earned = '4';
        	$this->activity->save();
		}
		else
		{
			return response()->json(["message" => "A comment by user already exist on this post"], 409);
		}

    	return response()->json($newComment, 201);
    }

    public function edit($slug, $commentId)
    {
        $post = Post::where('slug', $slug)->first();

        $postId = $post->id;

    	$comment = $this->comment::where('id', $commentId)
                                ->where('post_id', $postId)
                                ->first();

        if ($comment) {
            $return =   [
                            "status"    =>  200,
                            "response"  =>  $comment
                        ];

            return json_encode($return);
        }
    }

    public function update(Request $request, $slug, $commentId)
    {
    	$this->validate($request,[
    		'body' => 'required'
    	]);

    	$body = $request->body;

		$post = Post::where('slug', $slug)->first();
		$postId = $post->id;

    	$updatedComment = $this->comment::where('id', $commentId)
                        ->where('post_id', $postId)
                        ->update(['body' => $body]);

    	return response()->json($updatedComment ,200);
    }
}
