<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
class ProfileController extends Controller
{
    public function getProfile(){
        $user = Auth::user();
      
        
        return response()->json($user);
    }
    
    public function update(Request $request)
    {
        $user = Auth::user();
    
        // Ensure the user is authenticated
        if (!$user) {
            return response()->json([
                'result' => 'error',
                'message' => 'User not found.',
            ]);
        }
    
        // Validate incoming request data
        $validatedData = $request->validate([
            'password' => 'nullable|min:8', // Password is optional, must be at least 8 characters
            'profile' => 'nullable|image|max:2048', // Profile picture must be an image, max size: 2MB
        ]);
    
        // Update the password if provided
        if ($request->filled('password')) {
            $user->password = Hash::make($request->input('password'));
        }
    
        // Handle profile picture upload
        if ($request->hasFile('profile')) {
            $profileImage = $request->file('profile');
    
            // Validate the uploaded file
            if (!$profileImage->isValid()) {
                return response()->json([
                    'result' => 'error',
                    'message' => 'Uploaded file is not valid.',
                ]);
            }
    
            // Generate a unique file name
            $profileImageName = time() . '.' . $profileImage->getClientOriginalExtension();
    
            // Move the file to the "public/profile_pictures" directory
            $path = $profileImage->move(public_path('profile_pictures'), $profileImageName);
    
            // Check if the file was stored successfully
            if (!$path) {
                return response()->json([
                    'result' => 'error',
                    'message' => 'Failed to store the image.',
                ]);
            }
    
            // Delete the old profile picture if it exists
            if ($user->getOriginal('profile')) {
                $oldImagePath = public_path($user->getOriginal('profile'));
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }
    
            // Update user's profile image path in the database
            $user->profile = 'profile_pictures/' . $profileImageName;
        }
    
        // Update other user fields, excluding password and profile
        $user->fill($request->except(['password', 'profile']));
    
        // Save updated user data
        if ($user->save()) {
            return response()->json([
                'result' => 'success',
                'message' => 'The user has been updated successfully.',
            ]);
        }
    
        return response()->json([
            'result' => 'error',
            'message' => 'The user could not be updated. Please try again!',
        ]);
    }

    public function destroy()
    {
        $user = Auth::user(); // Get the currently authenticated user
    
        if ($user) {
            // Mark the user as deleted by setting isDeleted to the current timestamp
            $user->isDeleted = now();
            $user->save();
    
            // Log the user out
            Auth::logout();
    
            // Return a success response
            return response()->json([
                'result' => 'success',
                'message' => 'Your account has been deleted successfully.',
            ]);
        }
    
        // Return an error response if the user is not authenticated or not found
        return response()->json([
            'result' => 'error',
            'message' => 'User not found or already logged out. Please try again!',
        ], 404); // HTTP 404 for not found
    }
    
    
}