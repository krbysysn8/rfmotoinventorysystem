<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\LoginLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('login');
    }

    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'username' => 'required|string|max:50',
            'password' => 'required|string',
            'role'     => 'required|in:admin,staff',
        ]);

        $user = User::with('role')
            ->whereHas('role', fn($q) => $q->where('role_name', $request->role))
            ->where('username', $request->username)
            ->where('status', 'active')
            ->first();

        if (!$user || !Hash::check($request->password, $user->password_hash)) {

            LoginLog::create([
                'username'       => $request->username,
                'user_id'        => $user?->user_id,
                'role_attempted' => $request->role,
                'success'        => false,
                'ip_address'     => $request->ip(),
                'user_agent'     => $request->userAgent(),
                'fail_reason'    => $user ? 'Wrong password' : 'User not found',
            ]);

            return response()->json([
                'status'  => 'error',
                'message' => 'Invalid username or password for the selected role.',
            ], 401);
        }

        if (Hash::needsRehash($user->password_hash)) {
            $user->update(['password_hash' => Hash::make($request->password)]);
        }

        // Delete old tokens, create new one
        $user->tokens()->delete();
        $token = $user->createToken('rfmoto-token')->plainTextToken;

        // Update last login timestamp (supports both column names)
        $col = \Schema::hasColumn('users', 'last_login') ? 'last_login' : 'last_login';
        $user->update([$col => now()]);

        LoginLog::create([
            'username'       => $request->username,
            'user_id'        => $user->user_id,
            'role_attempted' => $request->role,
            'success'        => true,
            'ip_address'     => $request->ip(),
            'user_agent'     => $request->userAgent(),
        ]);

        // Record in activity logs
        ActivityLog::record(
            action:      'login',
            subject:     $user->full_name ?? $user->username,
            description: "User signed in as {$request->role}.",
            user:        $user,
        );

        return response()->json([
            'status'  => 'success',
            'message' => 'Login successful.',
            'token'   => $token,
            'user'    => [
                'id'       => $user->user_id,
                'username' => $user->username,
                'fullname' => $user->full_name,
                'role'     => $user->role->role_name,
            ],
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Logged out successfully.',
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        $user = $request->user()->load('role');

        return response()->json([
            'status' => 'success',
            'user'   => [
                'id'       => $user->user_id,
                'username' => $user->username,
                'fullname' => $user->full_name,
                'role'     => $user->role->role_name,
            ],
        ]);
    }
}