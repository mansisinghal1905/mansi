<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\AdminLoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Hash;
use Session;
use App\Models\User;
use Carbon\Carbon;
use DB;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
     //Registration
     public function registration()
     {
         return view('admin.auth.layouts.registration');
     }
     public function registerUser(Request $request)
     {
         $request->validate([
             'name'=>'required',
             'email'=>'required|email:users',
             'password'=>'required|min:5|max:12'
         ]);
 
         $user = new User();
         $user->name = $request->name;
         $user->email = $request->email;
         $user->password = $request->password;
 
         $result = $user->save();
         if($result){
             return back()->with('success','You have registered successfully.');
         } else {
             return back()->with('fail','Something wrong!');
         }
     }


    public function index()
    {
        return view('admin.auth.layouts.login');
    }

    public function store(Request $request)
    {
        try{
            $validator =  $request->validate([
                'email' => 'required',
                'password' => 'required',
            ]);
            $credentials = $request->only('email', 'password');
            if (Auth::attempt($credentials)) 
            {
                $user = Auth::user();
                $user->chat_status = 'active';
                $user->save();

                // Check if the user's attendance for today already exists
                $today = Carbon::today()->toDateString(); // Get today's date
                $attendanceExists = DB::table('attendances')
                    ->where('employee_id', $user->id)
                    ->whereDate('created_at', $today)
                    ->exists();

                if (!$attendanceExists) {
                   
                    DB::table('attendances')->insert([
                        'employee_id' => $user->id,
                        'employee_login_time' => Carbon::now()->format('H:i'),
                        'message' => "Today Login",
                        'office_logout_hrs'=>"19:00",
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                }
                return redirect()->intended('admin/dashboard')->with('success', 'Login Successfully.');
            }
            
            return redirect()->back()->with('error', 'Email address or password is incorrect.');
        }catch(Exception $e){
            // dd($e);
        }
    }


    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request)
    {
        // Session::flush();
        $user = Auth::user();
        if ($user) {
        $user->chat_status = 'inactive';
        $user->save();
        }
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/admin');
    }

   
}
