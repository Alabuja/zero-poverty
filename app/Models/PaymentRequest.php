<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentRequest extends Model
{
    use SoftDeletes;
    public function user()
    {
    	return $this->belongsTo('App\Models\User');
    }

    public function allPendingRequests()
    {
    	$requests  = self::whereNotNull('amount_requested')
                            ->where('isPending', false)
                            ->where('created_at', '>=', Carbon::now()->subDays(4))
    						->with("user.bankInfo", "user.referral")
                            ->get();

    	return $requests;
    }

    public function myWithdrawalRequests()
    {
    	$requests = self::whereNotNull('amount_requested')
                            ->where('isApprove', true)
                            ->orWhere('isPending', false)
                            ->where('created_at', '>=', Carbon::now()->subDays(6))
    						->where('user_id', Auth::user()->id)
    						->get();

    	return $requests;
    }
}
