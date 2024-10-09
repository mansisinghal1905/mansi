<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserProfileController extends Controller
{
   
    public function store(Request $request)
{
    // Validate incoming request
    $request->validate([
        'name' => 'required|string|max:255',
        'phone_number' => 'required|numeric|digits_between:10,15',
        'avatar' => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
    ]);

    // Retrieve the current user
    $user = auth()->user();

    // Handle avatar upload
    if ($request->hasFile('avatar')) {
        // Delete old avatar if exists
        if ($user->avatar) {
            $oldImage = public_path('profileimage') . '/' . $user->avatar;
            if (file_exists($oldImage)) {
                @unlink($oldImage);
            }
        }

        // Store new avatar
        $avatarName = time() . '.' . $request->avatar->getClientOriginalExtension();
        $request->avatar->move(public_path('profileimage'), $avatarName);


        $user->avatar = $avatarName;
    }

    $user->name = $request->name;
    $user->phone_number = $request->phone_number;
    $user->save();

    // Provide feedback to the user
    return back()->with('success', 'Profile updated successfully!');
}

}