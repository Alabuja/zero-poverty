<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use App\Models\Activity;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    public function __construct(Category $category, Post $post, Activity $activity)
	{
        $this->category = $category;
		$this->post = $post;
        $this->activity         =   $activity;
	}

    public function categories()
    {
        $categories = $this->category->categories();

        return response()->json($categories);
    }

    public function store(Request $request)
    {
    	$this->validate($request, [
    		'title' => 'required|unique:categories'
        ]);

        $count = $this->category->where(strtolower('title'), strtolower($request->title))->count();
        
        if($count > 1)
        {
           return response()->json(["message" => "A category with this name already exists"], 409);
        }

    	$newCategory = Category::create([
            'title' => $request->title
        ]);
        
    	return response()->json($newCategory, 201);
    }

    public function getPostsBycategory($slug)
    {
        $posts = $this->post->getPostsByCategeory($slug);

        return response()->json($posts);
    }

}
