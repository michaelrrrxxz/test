<?php

namespace App\Http\Controllers;
use App\Models\Batch;
use App\Models\Question;
use App\Models\LoginHistory; // Import the LoginHistory model
use Carbon\Carbon;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth; // Import the Auth facade

class AuthController extends Controller
{
    public function hello()
    {
        return view('Instructors.auth'); // Ensure this view exists
    }

    public function admin()
    {
        return view('Admin.auth'); // Ensure this view exists
    }

    public function root()
    {
        // Check if there is an active batch in the Batch table
        $activeBatch = Batch::where('status', 'active')->first();

        
        
        // Check for the question condition (example: if a question's value is less than 72)
        $questionValue =  Question::active()->count();
    
        if ($questionValue < 72) {
            // If the question value is less than 72, return the active view
            return view('root.no-active-index');
        }
        
        // If there is an active batch and no specific condition related to the question
        if ($activeBatch) {
            return view('root.active-index');
        } else {
            // If no active batch exists, return the "no active" view
            return view('root.no-active-index');
        }
    }

    


    public function edit()
    {
        // Get the currently authenticated user
        $user = Auth::user();
        
        return view('Admin.profile', compact('user'));
    }


    public function update(Request $request)
    {
        // Validate the input
        $request->validate([
            'username' => 'required|string|max:255',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        // Update the user's information
        $user = Auth::user();
        $user->username = $request->input('username');

        if ($request->filled('password')) {
            // Only update the password if it is provided
            $user->password = Hash::make($request->input('password'));
        }

        $user->save();

        // Redirect the user with a success message
        return redirect()->route('profile.edit')->with('success', 'Profile updated successfully!');


        
    }


    public function instructorLogin(Request $request)
{
    $request->validate([
        'username' => 'required',
        'password' => 'required',
    ]);

    // Retrieve credentials
    $credentials = $request->only('username', 'password');

    // Attempt to log in the instructor using the 'instructor' guard
    if (Auth::guard('instructor')->attempt($credentials)) {
        // Log the instructor's login history
        LoginHistory::create([
            'user_id' => Auth::guard('instructor')->id(),  // Get the instructor ID
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'login_at' => Carbon::now(),
        ]);

        // Respond with success
        return response()->json([
            'result' => 'success',
            'message' => 'Login successful',
            'redirect' => route('Instructors/dashboard'),  // Adjust redirect to the instructor's dashboard
        ]);
    }

    // Respond with error if authentication fails
    return response()->json([
        'result' => 'error',
        'message' => 'Invalid credentials',
    ]);
}

    
public function login(Request $request)
{
    // Validate input
    $request->validate([
        'username' => 'required',
        'password' => 'required',
    ]);

    // Set credentials with additional isDeleted check
    $credentials = $request->only('username', 'password');
    $credentials['isDeleted'] = 0; // Ensure the user is not soft-deleted

    if (Auth::attempt($credentials)) {
        $user = Auth::user();

        // Check if the user has either 'instructor' or 'administrator' role
        if (in_array($user->role, ['instructor', 'administrator','user'])) {

            // Store login history
            LoginHistory::create([
                'user_id' => $user->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'login_at' => Carbon::now(),
            ]);

            // Determine redirect route based on the user's role
            $redirectRoute = $user->role === 'instructor' ? route('Instructors/dashboard') : route('Dashboard');

            // Return a successful response with appropriate redirect
            return response()->json([
                'result' => 'success',
                'message' => 'Login successful',
                'redirect' => $redirectRoute,
            ]);
        } else {
            // Logout if role is neither 'instructor' nor 'administrator'
            Auth::logout();

            // Return a role-based error response
            return response()->json([
                'result' => 'error',
                'message' => 'Access denied: Only instructors and administrators can log in here.',
            ], 403); // HTTP status 403 Forbidden
        }
    } else {
        // Return an error response if authentication fails
        return response()->json([
            'result' => 'error',
            'message' => 'Invalid username or password',
        ], 401); // HTTP status 401 Unauthorized
    }
}



    public function logout(Request $request)
    {
        // Update the latest login history with logout time
        $loginHistory = LoginHistory::where('user_id', Auth::id())
            ->whereNull('logout_at') // Ensure it's the last login that hasn't been logged out yet
            ->orderBy('login_at', 'desc')
            ->first();
    
        if ($loginHistory) {
            $loginHistory->update([
                'logout_at' => Carbon::now(),
            ]);
        }
    
        Auth::logout();
    
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    
        return redirect('/')->with('success', 'Logged out successfully');
    }


    public function getLoginHistory()
    {
        $loginHistories = LoginHistory::with('user:id,username')
            ->where('user_id', Auth::id())
            ->get();
    
        // Transform the loginHistories to include the username in the same array
        $loginHistories = $loginHistories->map(function ($loginHistory) {
            return [
                'username' => $loginHistory->user->username, 
                'login_at' => $loginHistory->login_at,
                'logout_at' => $loginHistory->logout_at,
                'ip_address' => $loginHistory->ip_address, 
                'user_agent' => $loginHistory->user_agent,
            ];
        });
    
        return response()->json(['data' => $loginHistories]);
    }
    
    
    

    public function viewLoginHistory(){
        
        return view('Admin.loginhistory');
    }
    


    
    
}