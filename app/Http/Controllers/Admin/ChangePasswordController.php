<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JobCategory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ChangePasswordController extends Controller
{
    function __construct()
    {

        $this->columns = [

        ];
    }
    public function changePasswordold(Request $request)
    {
            # Validation
            $request->validate([
                'old_password' => 'required',
                'new_password' => 'required',

            ]);

            $user = auth()->user();

            # Match The Old Password
            if(!Hash::check($request->old_password, $user->password)){
                return response()->json(['status' => false,  'message' => "Old Password doesn't match!"]);
            }
           else if (Hash::check($request->new_password, $user->password)) {

            return response()->json(['status' => false, 'message' => "Old Password doesn't match!"]);(['status' => true, 'message' => 'New Password cannot be the same as the old password!']);
            }
            else{
            # Update the new Password
            $user->password  =Hash::make($request->new_password);
            if ($user->save()) {
                // Password updated successfully
                return response()->json(['status' => false, 'message' => "Old Password doesn't match!"]);(['status' => true,  'message' => 'Password changed successfully!']);

            } else {
                // Password update failed
                return response()->json(['status' => false, 'message' => "Old Password doesn't match!"]);(['status' => false, 'message' => 'Password change failed!']);

            }

        }
    }
    public function changePassword(Request $request)
    {
            # Validation
            $request->validate([
                'old_password' => 'required',
                'new_password' => 'required',
                'new_password_confirmation' =>'required',
            ]);
    
            $user = auth()->user();
        
            # Match The Old Password
            if(!Hash::check($request->old_password, $user->password)){
                return response()->json(['status' => false, 'message' => "Old Password doesn't match!"]);
            }

            if($request->new_password!= $request->new_password_confirmation){
                return response()->json(['status' => false, 'message' => "New Password and Confirm Password doesn't match!"]);
            }
             # Check if the new password is the same as the old password
            if (Hash::check($request->new_password, $user->password)) {

                return response()->json(['status' => true, 'message' => 'New Password cannot be the same as the old password!']);
            }
            # Update the new Password
            $user->password  =Hash::make($request->new_password);
            if ($user->save()) {
                // Password updated successfully
                return response()->json(['status' => true, 'message' => 'Password changed successfully!']);
            } else {
                // Password update failed
                return response()->json(['status' => false, 'message' => 'Password change failed!']);
            }


    }


}