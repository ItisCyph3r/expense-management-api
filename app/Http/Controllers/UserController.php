<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of users from the same company.
     */
    public function index(Request $request)
    {
        $companyId = $request->user()->company_id;
        
        // Get users from the same company with eager loading
        $users = User::where('company_id', $companyId)
            ->latest()
            ->paginate(15);
            
        return response()->json($users);
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => ['required', Rule::in(['Admin', 'Manager', 'Employee'])],
        ]);
        
        $admin = $request->user();
        
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'company_id' => $admin->company_id, // Assign to the admin's company
        ]);
        
        return response()->json([
            'message' => 'User created successfully',
            'user' => $user
        ], 201);
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => [
                'sometimes', 
                'required', 
                'string', 
                'email', 
                'max:255',
                Rule::unique('users')->ignore($user->id)
            ],
            'role' => ['sometimes', 'required', Rule::in(['Admin', 'Manager', 'Employee'])],
            'password' => 'sometimes|required|string|min:8|confirmed',
        ]);
        
        // Update user details
        if ($request->has('name')) {
            $user->name = $request->name;
        }
        
        if ($request->has('email')) {
            $user->email = $request->email;
        }
        
        if ($request->has('role')) {
            $user->role = $request->role;
        }
        
        if ($request->has('password')) {
            $user->password = Hash::make($request->password);
        }
        
        $user->save();
        
        return response()->json([
            'message' => 'User updated successfully',
            'user' => $user
        ]);
    }
}