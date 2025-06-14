<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        // Fetch all users
        $tokenData = app('token_data');
        $organizationId = $tokenData['organization_id'];

        $users = User::where('status', 1)
                        ->where('organization_id', $organizationId)
                        ->get();

        $users = $users->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'username' => $user->username,
                'role_id' => $user->role_id,
                'role_name' => $user->role ? $user->role->role_name : null,
                'staff_id' => $user->staff_id,
                'partner_id' => $user->partner_id
            ];
        });

        // Return users as a JSON response
        return response()->json($users);
    }

    // Create user
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:user',
            'username' => 'required|string|max:255|unique:user',
            'role' => 'required',
            'staff' => 'nullable|integer|unique:user,staff_id',
            'partner' => 'nullable|integer|unique:user,partner_id'
        ]);

        $createdBy = auth()->id();
        $tokenData = app('token_data');
        $organizationId = $tokenData['organization_id'];

        User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'username' => $validatedData['username'],
            'password' => md5($validatedData['username']),
            'role_id' => $validatedData['role'],
            'staff_id' => $validatedData['staff'] ?? null,
            'partner_id' => $validatedData['partner'] ?? null,
            'status' => 1,
            'organization_id' => $organizationId,
            'created_by' => $createdBy
        ]);

        return response()->json([
            'message' => 'User created successfully'
        ], 201);
    }

    // Update user
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:user,email,' . $id,
            'username' => 'required|string|max:255|unique:user,username,' . $id,
            'role' => 'required',
            'staff' => 'nullable|integer',
            'partner' => 'nullable|integer'
        ]);

        $user = User::findOrFail($id);
        $updatedBy = auth()->id();

        $userData = [
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'username' => $validatedData['username'],
            'role_id' => $validatedData['role'],
            'staff_id' => $validatedData['staff'] ?? null,
            'partner_id' => $validatedData['partner'] ?? null,
            'updated_by' => $updatedBy
        ];
        $user->update($userData);

        return response()->json(['message' => 'User updated successfully'], 200);
    }
}
