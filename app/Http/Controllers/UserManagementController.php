<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserManagementController extends Controller
{
    // ── GET /api/users ────────────────────────────────────────────
    public function index(): JsonResponse
    {
        $users = User::with('role:role_id,role_name')
            ->select('user_id', 'full_name', 'username', 'role_id', 'status', 'last_login_at', 'email')
            ->orderByDesc('user_id')
            ->get()
            ->map(fn($u) => [
                'id'         => $u->user_id,
                'fullname'   => $u->full_name,
                'username'   => $u->username,
                'role'       => $u->role?->role_name ?? '—',
                'status'     => $u->status ?? 'active',
                'last_login' => $u->last_login_at?->format('Y-m-d H:i') ?? null,
                'email'      => $u->email,
            ]);

        return response()->json(['status' => 'success', 'data' => $users]);
    }

    // ── POST /api/users ───────────────────────────────────────────
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'fullname' => 'required|string|max:100',
            'username' => 'required|string|max:50|unique:users,username',
            'email'    => 'nullable|email|max:120|unique:users,email',
            'role'     => 'required|in:admin,staff',
            'status'   => 'required|in:active,inactive',
            'password' => ['required', Password::min(8)],
        ]);

        $role = Role::where('role_name', $data['role'])->firstOrFail();

        $user = User::create([
            'full_name'     => $data['fullname'],
            'username'      => $data['username'],
            'email'         => $data['email'] ?? null,
            'role_id'       => $role->role_id,
            'status'        => $data['status'],
            'password_hash' => Hash::make($data['password']),
        ]);

        ActivityLog::record(
            action:      'user_created',
            subject:     $user->full_name,
            description: "New {$data['role']} account created.",
            user:        $request->user(),
        );

        return response()->json(['status' => 'success', 'message' => 'User created successfully.']);
    }

    // ── PUT /api/users/{id} ───────────────────────────────────────
    public function update(Request $request, int $id): JsonResponse
    {
        $user = User::findOrFail($id);

        $data = $request->validate([
            'fullname' => 'required|string|max:100',
            'username' => "required|string|max:50|unique:users,username,{$id},user_id",
            'email'    => "nullable|email|max:120|unique:users,email,{$id},user_id",
            'role'     => 'required|in:admin,staff',
            'status'   => 'required|in:active,inactive',
            'password' => ['nullable', Password::min(8)],
        ]);

        $role = Role::where('role_name', $data['role'])->firstOrFail();

        $user->full_name = $data['fullname'];
        $user->username  = $data['username'];
        $user->email     = $data['email'] ?? $user->email;
        $user->role_id   = $role->role_id;
        $user->status    = $data['status'];
        if (!empty($data['password'])) {
            $user->password_hash = Hash::make($data['password']);
        }
        $user->save();

        ActivityLog::record(
            action:      'item_updated',
            subject:     $user->full_name,
            description: "User account updated.",
            user:        $request->user(),
        );

        return response()->json(['status' => 'success', 'message' => 'User updated successfully.']);
    }

    // ── DELETE /api/users/{id} ────────────────────────────────────
    public function destroy(Request $request, int $id): JsonResponse
    {
        if ($request->user()->user_id === $id) {
            return response()->json(['status' => 'error', 'message' => 'You cannot delete your own account.'], 422);
        }

        $user = User::findOrFail($id);
        $name = $user->full_name;
        $user->delete();

        ActivityLog::record(
            action:      'deleted',
            subject:     $name,
            description: "User account deleted.",
            user:        $request->user(),
        );

        return response()->json(['status' => 'success', 'message' => 'User deleted.']);
    }
}