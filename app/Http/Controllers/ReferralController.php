<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Referral;
use App\Models\Activity;
use App\Models\LoginHistory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Carbon\Carbon;

class ReferralController extends Controller
{
    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
	use AuthenticatesUsers;
	
	protected $redirectTo = 'users/dashboard';

	public function __construct(User $user, Activity $activity, Referral $referral)
	{
		$this->user 	= $user;
		$this->activity = $activity;
		$this->referral = $referral;
	}

	public function getReferral($slug, $userId)
	{
		$slug = $slug;
		$userId = $userId;

		return view('referral.show_signup', compact('slug', 'userId'));
	}

    public function store(Request $request, $slug, $userId)
    {
    	$this->validate($request, [
    		'name' 			=> 'required|string',
    		'email'			=>	'required|string',
    		'password'		=>	'required|string|min:6'
    	]);

	    $createdUser =  User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'isReferred' => true,
            'package_type' => 'Free',
        ]);

        
        $loginHistory   = new LoginHistory;

	    if($createdUser)
	    {
	        $this->activity->user_id       =  $createdUser->id;
	        $this->activity->activity_type = 'register';
	        $this->activity->amount_earned = '100';
	        $this->activity->save();

	        $this->user->where('id', $createdUser->id)
                            ->update(['isReferred' => true]);

	        $loginHistory->user_id      =   $createdUser->id;
            $loginHistory->date_time    =   Carbon::now();
            $loginHistory->save();

	        $this->referral->user_id 		= $userId; //PersonReferringAnotherPerson
	        $this->referral->name   		= $request->name;
	        $this->referral->email 			= $request->email;
	        $this->referral->referral_id 	= $createdUser->id; // Person being referred
	        $this->referral->bonus 			= '0';
	        $this->referral->package_type 	= 'Free';
	        $this->referral->save();
	    }

	    $this->guard()->logout();

        $request->session()->invalidate();

        return redirect('users/login');
	}
	
	public function currentUserReferrals()
    {
        $referrals = $this->referral->currentUserReferrals();

        return response()->json($referrals);
    }

	public function currentUserReferralEarnings()
	{
		$referralEarnings =   $this->referral->currentUserReferralEarnings();

		return response()->json($referralEarnings);
	}
}
