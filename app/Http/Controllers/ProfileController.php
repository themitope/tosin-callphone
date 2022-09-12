<?php

namespace App\Http\Controllers;

use App\Http\Requests\UploadProfilePicRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }
    
    public function uploadProfilePic(UploadProfilePicRequest $request)
    {
        $user = auth()->user();
        $image = $request->image;

        try {
            $image_ext = $image->getClientOriginalExtension();
            $file_name = Str::slug($user->username, '-') . '.' . $image_ext;
            $stored_path = Storage::putFileAs('profile_pics', $image, $file_name);

            $saved_path = env('APP_URL') . '/storage' . '/' . $stored_path;

            $user->profile_pic = $saved_path;
            $user->save();
            
            return response()->json([
                'message' => 'Profile Image successfully saved',
                'user' => $user,
            ], 201);

        } catch (\Throwable $th) {
            
            Log::info("Error uploading profile picture {$th->getMessage()}");
            return response()->json(['error' => 'Could not upload profile picture'], 500);

        }
    }
}
