<?php

namespace App\Http\Controllers;

use App\Models\LoginLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
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

        // ── Delete old tokens, create new one ──
        $user->tokens()->delete();
        $token = $user->createToken('rfmoto-token')->plainTextToken;

        $user->update(['last_login' => now()]);

        LoginLog::create([
            'username'       => $request->username,
            'user_id'        => $user->user_id,
            'role_attempted' => $request->role,
            'success'        => true,
            'ip_address'     => $request->ip(),
            'user_agent'     => $request->userAgent(),
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Login successful.',
            'token'   => $token,
            'user'    => [
                'user_id'  => $user->user_id,
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
                'user_id'  => $user->user_id,
                'username' => $user->username,
                'fullname' => $user->full_name,
                'role'     => $user->role->role_name,
            ],
        ]);
    }
}