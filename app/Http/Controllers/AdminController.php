<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;
use App\Models\User;
use Cloudder;

class AdminController extends Controller
{
    public function __construct(Admin $admin, User $user)
	{
        $this->admin = $admin;
		$this->user = $user;
	}

    public function updatePassword(Request $request)
    {
        $validator = $this->validate($request, [
            'old' => 'required',
            'password' => 'required|confirmed',
        ]);

        $user = $this->admin->find(Auth::guard('admin')->id());
        $hashedPassword = $user->password;

 
        if (Hash::check($request->old, $hashedPassword)) 
        {
            $user->fill([
                'password' => Hash::make($request->password)
            ])->save();
 
            return response()->json([
                'message' => 'Your password has been changed.'
            ], 200);
        }
 
        return response()->json($validator, 500);
    }

    public function update(Request $request)
    {
        $this->validate($request, [
            'name'              =>  'required|string',
            'email'             =>  'required|string',
            'image'             =>  'required|mimes:jpeg,jpg,png'
        ]);

        if($request->file('image') != null)
        {
            $image                          =   $request->file('image')->getRealPath();

            Cloudder::upload($image, null);
            list($width, $height)                = getimagesize($image);

            $url = Cloudder::secureShow(Cloudder::getPublicId(), [
                "crop" => "fit", "width" => 200, "height" => 200
            ]);
        }
        
        $name          = $request->name;
        $email         = $request->email;

        $updatedAdminUser = $this->admin::where('id', Auth::guard('admin')->user()->id)->update([
            'name' => $name, 'email' => $email, 'image_url' => $url ]);

        return response()->json([
            'message' => 'You just updated your profile',
            'response' => $updatedAdminUser
        ], 200);
    }
}
