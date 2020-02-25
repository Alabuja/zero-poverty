<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use SoftDeletes;
    
    public function allPayments()
    {
    	$payments  	=	self::whereNotNull('payment_reference')->get();

    	return $payments;
    }

    public function user()
    {
    	return $this->belongsTo('App\Models\User');
    }
}
