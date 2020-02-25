<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\PaymentRequest;
use App\Models\Activity;
use App\Models\User;
use App\Models\Referral;
use Carbon\Carbon;

class PaymentRequestController extends Controller
{
    public function __construct(PaymentRequest $paymentRequest, Activity $activity, Referral $referral, User $user)
	{
        $this->paymentRequest   = $paymentRequest;
        $this->activity         = $activity;
        $this->referral         = $referral;
		$this->user             = $user;
	}

    public function store(Request $request)
    {
    	$this->validate($request, [
    		'amount_requested' => 'required'
    	]);

        $currentAmount      =   $this->activity->currentActivityEarnings();

        if($request->amount_requested != $currentAmount)
        {
            $this->paymentRequest->amount_requested = $currentAmount;
        }
        else
        {
            $this->paymentRequest->amount_requested = $request->amount_requested;
        }

        $userId = Auth::user()->id;

        $newPaymentRequest = PaymentRequest::create([
            'user_id'   =>  $userId,
            'amount_requested'  =>  $this->paymentRequest->amount_requested
        ]);

        return response()->json($newPaymentRequest, 201);
    }

    public function approve(Request $request, $id)
    {
        $this->validate($request, [
            'amount_paid'   => 'required',
            'referralCount' => 'required'
        ]);

        $amountPaid     =   $request->amount_paid;
        $referralCount  =   $request->referralCount;

        $newPaymentRequest  = $this->paymentRequest->find($id);
        $userId             = $newPaymentRequest->user_id;

        if($referralCount > 0)
        {
            $getUserReferral = $this->referral->where('user_id', $userId)->where('package_type', 'Affiliate')->take($referralCount)->get();
            
            foreach ($getUserReferral as $key => $user) {
                    
                if ($amountPaid >= 700) {
                    $newUserId = $user->user_id;

                    $this->referral::where('user_id', $newUserId)->delete();
                }
                $amountPaid = $amountPaid - 700;
            }
        }

        $updatePaymentRequest = $this->paymentRequest::where('id', $id)
                            ->update(['isApprove' => true, 'isPending' => true, 'amount_paid' => $request->amount_paid]);

        return response()->json(
            [
                'message'   => 'This request has been approved!',
                'response'  =>  $updatePaymentRequest
            ]
            , 200
        );
    }

    public function unApprove(Request $request, $id)
    {
        $this->paymentRequest::where('id', $id)
                            ->update(['isApprove' => false, 'isPending' => true]);

        return response()->json(
            ['message' => 'This request has been approved!']
            , 200
        );
    }

    public function paymentRequests()
    {
    	$paymentRequests  =  $this->paymentRequest->allPendingRequests();

    	return response()->json($paymentRequests);
    }

    public function withDrawalRequests()
    { 
        $paymentRequests    =   $this->paymentRequest->myWithdrawalRequests();

        return response()->json($paymentRequests);
    }

}
