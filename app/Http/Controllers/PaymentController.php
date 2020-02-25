<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Payment;
use App\Models\Referral;
use Paystack;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function __construct(User $user, Payment $payment, Referral $referral)
    {
    	$this->user 		= $user;
    	$this->payment 		= $payment;
    	$this->referral 	= $referral;
    }

    /**
     * Redirect the User to Paystack Payment Page
     * @return Url
     */
    public function redirectToGateway(Request $request)
    {

		$request->merge(['amount' => 110000]);

        return Paystack::getAuthorizationUrl()->redirectNow();
    }
 
    /**
     * Obtain Paystack payment information
     * @return void
     */
    public function handleGatewayCallback()
    {
    	$paymentDetails = Paystack::getPaymentData();

    	DB::transaction(function () use ($paymentDetails)    {

	    	$paymentDetail = $paymentDetails['data'];
	        $paymentData = $paymentDetails['data']['metadata'];

	        $this->payment->user_id 			= $paymentData['user_id'];
	        $this->payment->amount 				= $paymentDetail['amount'];
	        $this->payment->payment_reference 	= $paymentDetail['reference'];
            $this->payment->has_paid            =   true;

	        if ($this->payment->save())
	        {
	        	$newUser = $this->user::find($this->payment->user_id);

	        	$this->user::where('id', $this->payment->user_id)->update(['has_paid' => true, 'package_type' => 'Affiliate']);

	            if($newUser->isReferred == true)
	            {
	            	$referral_new = $this->referral::where('referral_id', $this->payment->user_id)
	            								->where('email', Auth::user()->email)
	            								->first();

		        	$referral_new->bonus 			= 	'700';
		        	$referral_new->package_type 	= 	'Affiliate';
		        	$referral_new->has_paid 		= 	true;

		        	$referral_new->save();
	            }
	        }

        }, 2);

		//return redirect()->back()->with('success', 'Your Account has been upgraded');

		return response()->json(
            ['message' => 'Your Account has been upgraded!']
            , 200
        );
    }

    public function payments()
    {
    	$payments  =  $this->payment->allPayments();

    	return response()->json($payments);
    }

}
