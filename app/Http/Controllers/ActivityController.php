<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Activity;


class ActivityController extends Controller
{
    public function __construct(Activity $activity)
	{
		$this->activity = $activity;
	}

    public function userActivities()
    {
		$activities = '';

    	if(Auth::check()){
            $activities     =   $this->activity->activities()->take(5);
        }
					
		return response()->json($activities);
	}
	
	public function currentActivityEarnings()
	{
		$currentAmount      =   $this->activity->currentActivityEarnings();
		
		return response()->json($currentAmount);
	}
}
