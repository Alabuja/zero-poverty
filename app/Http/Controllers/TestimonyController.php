<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Testimony;

class TestimonyController extends Controller
{
    public function __construct(Testimony $testimony)
	{
		$this->testimony = $testimony;
	}

	public function store(Request $request)
    {
    	$this->validate($request, [
    		'rating'	=>	'required',
    		'message'	=>	'required'
    	]);

        $userId = Auth::user()->id;
        
        $testimony = Testimony::create(['rating' => $request->rating, 'message' => $request->message, 'user_id' => $userId]);
        
        return response()->json($testimony, 201);
    }

}
