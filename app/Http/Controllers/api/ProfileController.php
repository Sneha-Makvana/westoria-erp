<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserDetail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{

    public function getProfile()
    {
        $user = Auth::guard('api')->user();

        if (!$user) {
            return response()->json(['status' => false, 'message' => 'Unauthorized - No user found'], 401);
        }

        return response()->json([
            'status' => true,
            'data' => $user->only(['id', 'name', 'email', 'phone', 'profile_image', 'role'])
        ], 200);
    }


    public function updateProfile(Request $request)
    {
        $user = Auth::guard('api')->user();

        if (!$user) {
            return response()->json(['status' => false, 'message' => 'Unauthorized - No user found'], 401);
        }

        $rules = [
            'name' => 'required|string|max:255',
            'email'          => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'required|string|max:10|unique:users,phone,' . $user->id,
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'password' => 'nullable|string|min:8|max:255|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&]).+$/',
        ];

        if ($user->role === 'staff') {
            $rules = array_merge($rules, [
                'salary' => 'nullable|numeric|min:0',
                'joining_date' => 'nullable|date',
                'department_id' => 'nullable|integer|exists:department,id',
                'designation_id' => 'nullable|integer|exists:designation,id',
                'country' => 'nullable|string|max:100',
                'city' => 'nullable|string|max:100',
                'address' => 'nullable|string|max:500',
                'face_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            ]);
        }

        $validator = Validator::make($request->all(), $rules, [
            'password.regex' => 'Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character.',
        ]);

        if ($user->role === 'staff') {
            $validator->after(function ($validator) use ($request) {
                if (!$request->filled('department_id') || !$request->filled('designation_id')) {
                    return;
                }

                $designationBelongsToDepartment = \App\Models\DesignationModel::where('id', $request->designation_id)
                    ->where('department_id', $request->department_id)
                    ->exists();

                if (!$designationBelongsToDepartment) {
                    $validator->errors()->add('designation_id', 'The selected designation does not belong to the selected department.');
                }
            });
        }

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }

        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;

        if ($request->hasFile('profile_image')) {
            $imagePath = $request->file('profile_image')->store('profile_images', 'public');
            $user->profile_image = $imagePath;
        }

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        if ($user->role === 'staff') {
            $userDetail = UserDetail::firstOrNew(['user_id' => $user->id]);
            $userDetail->salary = $request->filled('salary') ? $request->salary : null;
            $userDetail->joining_date = $request->joining_date;
            $userDetail->country = $request->country;
            $userDetail->city = $request->city;
            $userDetail->address = $request->address;

            if ($request->hasFile('face_photo')) {
                $userDetail->face_photo = $request->file('face_photo')->store('faces', 'public');
            }

            $userDetail->save();
        }

        return response()->json(['status' => true, 'message' => 'Profile updated successfully'], 200);
    }
    public function get_subadmin(Request $request)
    {
        $user = Auth::guard('api')->user();
        $role = $user->role ?? '';

        if (!$user || $role !== 'admin') {
            return response()->json(['status' => false, 'message' => 'Unauthorized - Only admin allowed'], 403);
        }

        $subadmin = User::whereIn('role', ['admin', 'sub-admin'])
            ->where('isDeleted', 0)
            ->select('id', 'name', 'profile_image')
            ->get();

        if ($subadmin->isEmpty()) {
            return response()->json(['status' => false, 'message' => 'No subadmin found'], 404);
        }
        // dd($subadmin);
        return response()->json([
            'status' => true,
            'data' => $subadmin,
            'role' => $role
        ], 200);
    }

}
