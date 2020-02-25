<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Activity;
use App\Models\Referral;
use App\Models\Post;
use App\Models\SponsoredPost;
use App\Models\PaymentRequest;
use App\Models\Withdraw;
use App\Models\BankInfo;
use Image;
use Carbon\Carbon;

class UsersController extends Controller
{
    public function __construct(User $user, Referral $referral, Activity $activity, Post $post, SponsoredPost $sponsoredPost, Withdraw $withdraw, BankInfo $bankInfo, PaymentRequest $paymentRequest)
	{
        $this->user = $user;
        $this->referral = $referral;
		$this->activity = $activity;
        $this->post = $post;
        $this->sponsoredPost = $sponsoredPost;
        $this->withdraw = $withdraw;
        $this->bankInfo = $bankInfo;
        $this->paymentRequest = $paymentRequest;
	}

    public function users()
    {
        $users = $this->user->getUsers();

        return response()->json($users);
    }

    public function user($id)
    {
        $user = $this->user->find($id);

        return response()->json($user);
    }

    public function update(Request $request)
    {
    	$this->validate($request, [
    		'name' 				=>  'required|string',
    		'email' 			=> 	'required|string',
    		'phone_number' 		=> 	'nullable',
    		'mobile_number' 	=> 	'nullable',
    		'date_of_birth'  	=> 	'nullable|date',
    		'city'				=> 	'nullable',
    		'address'			=>	'nullable',
    		'facebook_username'	=>	'nullable',
    		'twitter_username'	=>	'nullable',
    		'google_username'	=>	'nullable',
    		'signature'			=>	'nullable',
    		'gender'			=>	'nullable',
    		'about_me'			=>	'nullable',
    		'image'				=>	'nullable'
    	]);

    	$userId = Auth::user()->id;

    	$name 					= $request->name;
    	$email 					= $request->email;
    	$phoneNumber  			= $request->phone_number;
    	$mobileNumber 			= $request->mobile_number;
    	$dateOfBirth 			= $request->date_of_birth;
    	$city 					= $request->city;
    	$facebookUsername 		= $request->facebook_username;
    	$twitterUsername 		= $request->twitter_username;
    	$googleUsername 		= $request->google_username;
    	$signature 				= $request->signature;
    	$gender 				= $request->gender;
    	$about_me 				= $request->about_me;

        $filename = '';
    	if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = time() . '.' . $image->getClientOriginalExtension();
            Image::make($image)->resize(300, 300)->save(public_path('/users/' . $filename));
        }

    	$user = $this->user::where('id', $userId)->update([
    		'name' => $name, 'email' => $email,
    		'phone_number' => $phoneNumber, 'mobile_number' => $mobileNumber,
    		'date_of_birth' => $dateOfBirth, 'city' => $city, 
    		'facebook_username' => $facebookUsername, 
    		'twitter_username' => $twitterUsername, 
    		'google_username' => $googleUsername, 'signature' => $signature, 
    		'gender' => $gender, 'about_me' => $about_me, 'image' => $filename
    	]);

        return response()->json([
            'message' => 'You just updated your profile',
            'response' => $user
        ], 200);
    }

    public function updatePassword(Request $request)
    {
    	$validator = $this->validate($request, [
            'old' => 'required',
            'password' => 'required|confirmed',
        ]);

        $user = $this->user->find(Auth::id());
        $hashedPassword = $user->password;
 
        if (Hash::check($request->old, $hashedPassword)) 
        {
            $user->fill([
                'password' => Hash::make($request->password)
            ])->save();
                
            return response()->json([
                'message' => 'Your password has been changed.'
            ], 200);
        }

        return response()->json($validator, 500);
    }

    public function countUsers()
    {
        $count = $this->user->countUsers();

        return response()->json($count);
    }

}
