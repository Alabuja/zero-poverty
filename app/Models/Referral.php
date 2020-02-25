<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\SoftDeletes;

class Referral extends Model
{
    use SoftDeletes;

    public function user()
    {
    	return $this->belongsTo('App\Models\User');
    }

    public function currentUserReferralEarnings()
    {
    	$referrals = self::select('bonus')
    					  ->where('package_type', 'Affiliate')
    					  ->where('user_id', Auth::user()->id)
    					  ->sum('bonus');

    	return $referrals;
    }

    public function currentUserReferrals()
    {
    	$myReferrals = self::select('*')
    						->where('user_id', Auth::user()->id)
                            ->orWhereNotNull('deleted_at')
    						->paginate(30);

    	return $myReferrals;
    }
}
