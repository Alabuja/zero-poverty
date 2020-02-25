<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\Models\Referral;
use App\Models\PaymentRequest;
use Illuminate\Database\Eloquent\SoftDeletes;


class Activity extends Model
{
    use SoftDeletes;
    
    public function user()
    {
    	return $this->belongsTo('App\Models\User');
    }

    public function activities()
    {
    	$activities = self::select('*')->where('user_id', Auth::user()->id)->orderBy('created_at', 'desc')->get();

    	return $activities;
    }

    public function allTimeEarnings()
    {
        $activities = self::select('amount_earned')->where('user_id', Auth::user()->id)->sum('amount_earned');

        $referrals = Referral::select('bonus')->where('package_type', 'Affiliate')->where('user_id', Auth::user()->id)->sum('bonus');

        $total = $activities + $referrals;

        return $total;
    }

    public function currentActivityEarnings()
    {
    	$activities = self::select('amount_earned')->where('user_id', Auth::user()->id)->sum('amount_earned');

        $referrals = Referral::select('bonus')->where('package_type', 'Affiliate')->where('user_id', Auth::user()->id)->sum('bonus');
        
    	$paymentRequestsPaid = PaymentRequest::select('amount_paid')->where('isApprove', true)->where('user_id', Auth::user()->id)->sum('amount_paid');

        $subTotal = $activities + $referrals;

    	$total = $subTotal - $paymentRequestsPaid;

    	return $total;
    }

}
