<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Testimony extends Model
{
    use SoftDeletes;
    
    public function user()
    {
    	return $this->belongsTo('App\Models\User');
    }
}
