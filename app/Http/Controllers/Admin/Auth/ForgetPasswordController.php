<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Mail;
use DB;

class ForgetPasswordController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('admin.auth.layouts.forget-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
       $Checked= DB::table('users')->where("email",$request->email)->first();
        if($Checked!=null){
            $token = Str::random(64);
            DB::table('password_reset_tokens')->delete([
                'email' => $request->email, 
              ]);
            DB::table('password_reset_tokens')->insert([
                'email' => $request->email, 
                'token' => $token, 
                'created_at' => Carbon::now()
              ]);
    
            Mail::send('emails.forgotpassword', ['token' => $token], function($message) use($request){
                $message->to($request->email);
                $message->subject('Reset Password');
            });
            return redirect()->back()->with('success', 'We have e-mailed your password reset link!');
        }
        else{
            return redirect()->back()->with('error', 'Your Account not Exist');
      }
    }

    public function showResetPasswordForm($token) { 
        // dd($token);
        return view('admin.auth.layouts.forgetpassword-link', ['token' => $token]);
    }

    public function submitResetPasswordForm(Request $request)
    {
        
        $request->validate([
            'password' => 'required|min:5|confirmed',
            'password_confirmation' => 'required'
        ]);

        $updatePassword = DB::table('password_reset_tokens')
                            ->where('token', $request->token)
                            ->first();
        // dd($updatePassword);
        if(!$updatePassword){
            return back()->withInput()->with('error', 'Invalid token!');
        }

        $user = User::where('email', $updatePassword->email)
                    ->update(['password' => Hash::make($request->password)]);
    

        DB::table('password_reset_tokens')->where(['email'=> $updatePassword->email])->delete();

        
        return redirect('/admin')->with('success', 'Your password has been changed!');
       
    }

}