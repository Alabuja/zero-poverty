<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SponsoredComment;
use App\Models\Activity;
use App\Models\SponsoredPost;

class SponsoredCommentController extends Controller
{
    public function __construct(SponsoredComment $comment, Activity $activity, SponsoredPost $sponsoredPost)
	{
		$this->comment = $comment;
        $this->activity = $activity;
		$this->sponsoredPost = $sponsoredPost;
	}

	public function store(Request $request, $slug)
    {
    	$this->validate($request, [
    		'body'				=>	'required'
    	]);

        $post = SponsoredPost::where('slug', $slug)->first();
        $postId = $post->id;

    	$userId = Auth::user()->id;
        
        Comment::create(
            [
                'sponsored_post_id' => $postId, 
                'body' => $request->body, 
                'user_id' => $userId
            ]
        );

    	$count = $this->comment->where('user_id', $userId)->where('sponsored_post_id', $postId)->count();

        $newActivity = "";
    	if($count <= 1)
    	{
            $newActivity = Activity::create(
                [
                    'user_id' => $userId, 
                    'activity_type' => 'sponsored', 
                    'amount_earned' => '50'
                ]
            );
        }
        else
		{
			return response()->json(["message" => "A comment by user already exist on this post"], 409);
		}
        
        return response()->json(['message' => 'Comment Successfully Made', 'response' => $newActivity], 201);
    }

    public function edit($slug, $commentId)
    {
        $post = SponsoredPost::where('slug', $slug);

        $comment = $this->comment::where('id', $commentId)
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

        $post = SponsoredPost::where('slug', $slug)->first();
        $postId = $post->id;

        $comment = $this->comment::where('id', $commentId)
                        ->where('sponsored_post_id', $postId)
                        ->update(['body' => $body]);

        return response()->json(
            [
                'message' => 'Comment successfully made.',
                'response'   =>  $comment
            ], 200
        );
    }
}
