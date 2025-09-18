<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserAuthController extends Controller
{
    /**
     * 🔹 List all users (Admin only - optional)
     */
    public function index()
    {
        $users = User::all();
        return response()->json($users);
    }

    /**
     * 🔹 Register a new user
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role'     => 'nullable|string|in:user,admin'
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => $request->role ?? 'user'
        ]);

        $token = $user->createToken('user-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => '✅ User registered successfully',
            'user'    => $user,
            'token'   => $token
        ], 201);
    }

    /**
     * 🔹 User login
     */
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => '❌ Invalid credentials'
            ], 401);
        }

        // remove old tokens (optional security)
        $user->tokens()->delete();

        $token = $user->createToken('user-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => '✅ Login successful',
            'user'    => $user,
            'token'   => $token
        ]);
    }

    /**
     * 🔹 Get logged-in user info
     */
    public function me(Request $request)
    {
        return response()->json($request->user());
    }

    /**
     * 🔹 Logout user
     */
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'success' => true,
            'message' => '🚪 Logged out successfully'
        ]);
    }

    /**
     * 🔹 Update user (optional - profile update or admin)
     */
    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name'     => 'sometimes|string|max:255',
            'email'    => 'sometimes|email|unique:users,email,' . $user->id,
            'password' => 'sometimes|string|min:6',
            'role'     => 'sometimes|string|in:user,admin'
        ]);

        if ($request->has('password')) {
            $request->merge(['password' => Hash::make($request->password)]);
        }

        $user->update($request->all());

        return response()->json([
            'success' => true,
            'message' => '✅ User updated',
            'user'    => $user
        ]);
    }

    /**
     * 🔹 Delete user (optional - admin)
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => '🗑️ User deleted'
        ]);
    }
}
