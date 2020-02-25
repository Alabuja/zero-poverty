<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\BankInfo;
use App\Models\User;

class BankInfoController extends Controller
{
    public function __construct(BankInfo $bankInfo, User $user)
	{
        $this->bankInfo = $bankInfo;
		$this->user = $user;
	}

    public function bankInfo()
    {
        $user = $this->user->find(Auth::user()->id);
        
        $bankInfo = $user->bankInfo;

        return response()->json($bankInfo);
    }

    public function update(Request $request)
    {
    	$this->validate($request, [
    		'bank_name' 		=>  'required|string',
    		'account_number' 	=> 'required|string|max:11',
    		'account_name' 		=> 'required|string',
    		'account_type' 		=> 'required|string'
    	]);

    	$userId = Auth::user()->id;

    	$bankName 			= $request->bank_name;
    	$accountNumber 		= $request->account_number;
    	$accountName  		= $request->account_name;
    	$accountType 		= $request->account_type;

        $bankInfo = $this->bankInfo->find($userId);

        if($bankInfo == null)
        {
            $newBankInfo = BankInfo::create([
                'bank_name' => $bankName,
                'account_number' => $accountNumber,
                'account_name'      => $accountName,
                'account_type'      =>  $accountType,
                'user_id'           =>  $userId
            ]);

            return response()->json($newBankInfo, 201);
        }
    	else
        {
            $updatedBankInfo = $this->bankInfo::where('user_id', $userId)->update([
                'bank_name' => $bankName, 'account_number' => $accountNumber,
                'account_name' => $accountName, 'account_type' => $accountType
            ]);

            return response()->json($updatedBankInfo, 200);
        }
    }
}
