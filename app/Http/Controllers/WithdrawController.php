<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Withdraw;
use App\Http\Requests\WithdrawRequest;

class WithdrawController extends Controller
{
    public function __construct(Withdraw $withdraw)
	{
		$this->withdraw = $withdraw;
	}

    public function setWithDrawal()
    {
        $withdraw = $this->withdraw->first();

        return response()->json($withdraw);
    }

    public function open(WithdrawRequest $request)
    {
    	$withdraw = $this->withdraw::where('id', 1)->update($request->validated());
        
        return response()->json([
            'message' => 'Withdrawal Status for the week is opened',
            'withdraw' => $withdraw
        ], 200);
    }

    public function close(WithdrawRequest $request)
    {
    	$withdraw = $this->withdraw::where('id', 1)->update($request->validated());

        return response()->json([
            'message' => 'Withdrawal Status for the week is closed',
            'withdraw' => $withdraw
        ], 200);
    }

}
