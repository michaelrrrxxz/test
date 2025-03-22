<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\OtpMail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Storage;
class UserController extends Controller
{
public function getUsers()
{
    $users = User::active()->get();
    return response()->json(['data' => $users]);
}
public function getInstructors()
{
    $users = User::active()->instructor()->get();
    return response()->json(['data' => $users]);
}

    
        public function index(){
        return view('Admin.Users.index');
    }

    public function store(Request $request)
    {

      
        // Validate the request data, including file upload and conditional department field
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|min:4|unique:users,username',
            'password' => [
                'required',
                'string',
                'min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\W).*$/'
            ],
            'role' => 'required|in:administrator,user,instructor',
            'profile' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'department' => 'required_if:role,instructor|max:255'
        ], [
            'password.regex' => 'The password must contain at least one uppercase letter, one lowercase letter, and one special character.',
            'password.min' => 'The password must be at least 8 characters long.',
            'profile.image' => 'The profile picture must be an image.',
            'profile.mimes' => 'The profile picture must be a file of type: jpeg, png, jpg, gif, svg.',
            'profile.max' => 'The profile picture must not be larger than 2MB.',
            'department.required_if' => 'The department field is required when the role is Instructor.'
        ]);
    
        // If validation fails, return error response
        if ($validator->fails()) {
            return response()->json([
                'result' => 'error',
                'message' => $validator->errors()->first()
            ]);
        }
    
        // Prepare data for insertion
        $data = $request->all();
        $data['password'] = Hash::make($request->input('password'));
    
        // Handle profile picture upload
        if ($request->hasFile('profile')) {
            $profileImage = $request->file('profile');
            $profileImageName = time() . '.' . $profileImage->getClientOriginalExtension();
    
            // Check if directory exists, create if not
            $directoryPath = public_path('storage/profile_pictures');
            if (!file_exists($directoryPath)) {
                mkdir($directoryPath, 0777, true);
            }
    
            // Move to storage/profile_pictures directory
            $profileImage->move($directoryPath, $profileImageName);
            $data['profile'] = 'storage/profile_pictures/' . $profileImageName; // Store the image path in the database
        }
    
        // Create the user in the database
        $username = $data['username'];
        $user = User::create($data);
    
        // Log the activity
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'added',
            'description' => 'Added user ' . $username,
        ]);

        return response()->json([
            'result' => $user ? 'success' : 'error',
            'message' => $user ? 'The user has been saved' : 'The user could not be saved. Please try again!'
        ]);
    }
    


    public function show(string $id)
    {
        $user = user::find($id);
        return response()->json($user);
    }

    public function edit(string $id)
    {
       $user = user::find($id);
        return response()->json($user);
    }

  
  
    
    public function update(Request $request, string $id)
    {
        // Find the user by ID
        $user = User::find($id);
    
        if (!$user) {
            return response()->json(['result' => 'error', 'message' => 'User not found.']);
        }
    
        // Validate incoming request
        $validatedData = $request->validate([
            'password' => 'nullable|min:8',
            'profile' => 'nullable|image|max:2048', // Ensure profile is an image (max size: 2MB)
        ]);
    
        // Update the password if provided
        if ($request->filled('password')) {
            $user->password = Hash::make($request->input('password'));
        }
    
        // Handle profile picture upload
        if ($request->hasFile('profile')) {
            $profileImage = $request->file('profile');
    
            // Debug: Check if the file is valid and the upload exists
            if (!$profileImage->isValid()) {
                return response()->json(['result' => 'error', 'message' => 'Uploaded file is not valid.']);
            }
    
            $profileImageName = time() . '.' . $profileImage->getClientOriginalExtension();
    
            // Store the file in the "public/profile_pictures" directory
            $path = $profileImage->storeAs('profile_pictures', $profileImageName, 'public');
    
            // Check if the file was actually stored
            if (!$path) {
                return response()->json(['result' => 'error', 'message' => 'Failed to store the image.']);
            }
    
            // Update the user's profile image path in the database
            $user->profile = 'storage/' . $path;
    
            // Optional: Delete the old profile image if it exists
            if ($user->getOriginal('profile')) {
                $oldImagePath = str_replace('storage/', '', $user->getOriginal('profile')); // Remove 'storage/' prefix
                Storage::disk('public')->delete($oldImagePath); // Delete from 'public' disk
            }
        }
    
        // Update the user with the rest of the fields (exclude password and profile)
        $user->fill($request->except('password', 'profile'));
        $isUpdated = $user->save();
    
        // Log activity for update action
        if ($isUpdated) {
            ActivityLog::create([
                'user_id' => auth()->id(), // Current logged-in user ID
                'action' => 'updated',
                'description' => "Updated user with ID $id",
            ]);
    
            return response()->json(['result' => 'success', 'message' => 'The user has been updated successfully.']);
        } else {
            return response()->json(['result' => 'error', 'message' => 'The user could not be updated. Please try again!']);
        }
    }
      

    public function destroy(string $id)
    {
        $user = User::find($id);
        $username = $user ? $user->username : null;
    
        if ($user) {
            // Mark the user as deleted by setting isDeleted to 1
            $user->isDeleted = 1;
            $user->save();
    
            // Log the activity
            ActivityLog::create([
                'user_id' => auth()->id(), // Current logged-in user ID
                'action' => 'delete',
                'description' => 'Deleted user ' . $username,
            ]);
    
            return response()->json([
                'result' => 'success',
                'message' => 'The user has been marked as deleted.',
            ]);
        }
    
        return response()->json([
            'result' => 'error',
            'message' => 'The user could not be found. Please try again!',
        ]);
    }
    




public function sendOtp(Request $request)
{
    // Generate a random OTP
    $otp = rand(100000, 999999);

    // Send OTP email
    Mail::to($request->input('email'))->send(new OtpMail($otp));

    // Optionally, save the OTP to the session or database for later verification
    session(['otp' => $otp]);

    return response()->json(['message' => 'OTP sent successfully!']);
}
public function verifyOtp(Request $request)
{
    $inputOtp = $request->input('otp');

    // Check OTP
    if (session('otp') == $inputOtp) {
        return response()->json(['message' => 'OTP verified successfully!']);
    }

    return response()->json(['message' => 'Invalid OTP'], 400);
}


}