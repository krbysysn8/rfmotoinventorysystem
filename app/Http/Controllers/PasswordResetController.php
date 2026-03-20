<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\PasswordResetToken;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class PasswordResetController extends Controller
{
    // ─────────────────────────────────────────────────────────────
    //  GET /reset-password?token=xxx
    //  Blade page — the link the user clicks from their email
    // ─────────────────────────────────────────────────────────────
    public function showResetForm(Request $request)
    {
        $token = $request->query('token');

        if (! $token) {
            return redirect('/login')->with('error', 'Invalid or missing reset token.');
        }

        $record = PasswordResetToken::where('token', $token)
            ->where('used', false)
            ->where('expires_at', '>', now())
            ->with('user')
            ->first();

        if (! $record) {
            return view('reset-password', ['invalid' => true]);
        }

        return view('reset-password', [
            'invalid' => false,
            'token'   => $token,
            'name'    => $record->user?->full_name ?? $record->user?->username ?? 'User',
        ]);
    }

    // ─────────────────────────────────────────────────────────────
    //  POST /api/password/forgot
    //  Called from the Login page — ADMIN ONLY
    //  Sends reset email to the admin's own email on record
    // ─────────────────────────────────────────────────────────────
    public function forgot(Request $request): JsonResponse
    {
        $request->validate([
            'username' => 'required|string|max:50',
        ]);

        // Find the user first, then check role via loaded relationship
        $user = User::with('role')
            ->where('username', $request->username)
            ->where('status', 'active')
            ->first();

        if (! $user) {
            return response()->json([
                'status'  => 'error',
                'message' => 'No active account found with that username.',
            ], 404);
        }

        // Check if admin role
        if ($user->role?->role_name !== 'admin') {
            return response()->json([
                'status'  => 'error',
                'message' => 'This feature is only available for admin accounts.',
            ], 403);
        }

        if (! $user->email) {
            return response()->json([
                'status'  => 'error',
                'message' => 'This admin account has no email on file. Please contact your system administrator.',
            ], 422);
        }

        $token = $this->createToken($user, 'forgot');
        $this->sendResetEmail($user, $token, 'forgot');

        ActivityLog::record(
            'password_reset_requested',
            $user->username,
            "Admin requested a password reset email.",
        );

        return response()->json([
            'status'  => 'success',
            'message' => "Reset link sent to {$user->email}. Check your inbox.",
        ]);
    }

    // ─────────────────────────────────────────────────────────────
    //  POST /api/password/reset
    //  Called from the reset-password Blade page (token link)
    //  Works for both admin (forgot) and staff (admin-triggered)
    // ─────────────────────────────────────────────────────────────
    public function reset(Request $request): JsonResponse
    {
        $request->validate([
            'token'    => 'required|string|size:64',
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $record = PasswordResetToken::where('token', $request->token)
            ->where('used', false)
            ->where('expires_at', '>', now())
            ->with('user')
            ->first();

        if (! $record || ! $record->user) {
            return response()->json([
                'status'  => 'error',
                'message' => 'This reset link is invalid or has expired.',
            ], 422);
        }

        $user = $record->user;
        $user->update(['password_hash' => Hash::make($request->password)]);
        $user->tokens()->delete(); // Force re-login after reset

        $record->update(['used' => true]);

        $userName = $user->full_name ?? $user->username;

        ActivityLog::record(
            'password_reset',
            $userName,
            "Password was reset via email link (type: {$record->type}).",
        );

        return response()->json([
            'status'  => 'success',
            'message' => 'Password has been reset successfully. You can now log in.',
        ]);
    }

    // ─────────────────────────────────────────────────────────────
    //  POST /api/password/admin-send-reset
    //  Admin action from User Management — send reset email to a staff
    //  Requires: auth:sanctum + role:admin
    // ─────────────────────────────────────────────────────────────
    public function adminSendReset(Request $request): JsonResponse
    {
        $request->validate([
            'user_id' => 'required|integer|exists:users,user_id',
        ]);

        $target = User::with('role')->find($request->user_id);

        if (! $target) {
            return response()->json(['status' => 'error', 'message' => 'User not found.'], 404);
        }

        if (! $target->email) {
            return response()->json([
                'status'  => 'error',
                'message' => "This user has no email address on file. Add one in their profile first.",
            ], 422);
        }

        $token = $this->createToken($target, 'admin_reset');
        $this->sendResetEmail($target, $token, 'admin_reset');

        $targetName = $target->full_name ?? $target->username;

        ActivityLog::record(
            'password_reset_sent',
            $targetName,
            "Admin sent a password reset email to {$target->email}.",
            $request->user(),
        );

        return response()->json([
            'status'  => 'success',
            'message' => "Password reset email sent to {$target->email}.",
        ]);
    }

    // ─────────────────────────────────────────────────────────────
    //  POST /api/password/admin-set
    //  Admin action from User Management — directly set a new password
    //  for a staff member (no email required)
    //  Requires: auth:sanctum + role:admin
    // ─────────────────────────────────────────────────────────────
    public function adminSetPassword(Request $request): JsonResponse
    {
        $request->validate([
            'user_id'  => 'required|integer|exists:users,user_id',
            'password' => ['required', Password::min(8)],
        ]);

        $target = User::find($request->user_id);

        if (! $target) {
            return response()->json(['status' => 'error', 'message' => 'User not found.'], 404);
        }

        // Prevent admin from using this endpoint on another admin
        if ($target->role?->role_name === 'admin' && $target->user_id !== $request->user()->user_id) {
            return response()->json([
                'status'  => 'error',
                'message' => 'You cannot directly set the password of another admin account.',
            ], 403);
        }

        $target->update(['password_hash' => Hash::make($request->password)]);
        $target->tokens()->delete();

        $targetName = $target->full_name ?? $target->username;

        ActivityLog::record(
            'password_set',
            $targetName,
            "Admin manually set a new password for this user.",
            $request->user(),
        );

        $targetName = $target->full_name ?? $target->username;

        return response()->json([
            'status'  => 'success',
            'message' => "Password updated for {$targetName}.",
        ]);
    }

    // ─────────────────────────────────────────────────────────────
    //  Helpers
    // ─────────────────────────────────────────────────────────────
    private function createToken(User $user, string $type): string
    {
        // Invalidate any previous unused tokens for this user + type
        PasswordResetToken::where('user_id', $user->user_id)
            ->where('type', $type)
            ->where('used', false)
            ->delete();

        $token = Str::random(64);

        PasswordResetToken::create([
            'user_id'    => $user->user_id,
            'token'      => $token,
            'type'       => $type,
            'used'       => false,
            'expires_at' => now()->addMinutes(60),
            'created_at' => now(),
        ]);

        return $token;
    }

    private function sendResetEmail(User $user, string $token, string $type): void
    {
        $resetUrl  = config('app.url') . '/reset-password?token=' . $token;
        $name      = $user->full_name ?? $user->username;
        $appName   = config('app.name', 'RF Moto Parts');
        $expiresIn = '60 minutes';

        $subject = $type === 'admin_reset'
            ? "[{$appName}] Your Password Has Been Reset by Admin"
            : "[{$appName}] Password Reset Request";

        $intro = $type === 'admin_reset'
            ? "An administrator has initiated a password reset for your account."
            : "We received a request to reset your password.";

        $html = <<<HTML
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
  body { margin:0; padding:0; background:#eef3f7; font-family:'Segoe UI',Arial,sans-serif; }
  .wrap { max-width:560px; margin:40px auto; background:#fff; border-radius:16px; overflow:hidden;
          box-shadow:0 4px 24px rgba(0,0,0,.10); }
  .stripe { height:4px; background:linear-gradient(90deg,#0ea5c9,#17b8dc,#7ee8fa,#0ea5c9); }
  .header { padding:32px 40px 20px; text-align:center; }
  .logo-text { font-size:22px; font-weight:800; letter-spacing:.06em; text-transform:uppercase; color:#0d1b26; }
  .logo-text span { color:#17b8dc; }
  .sub { font-size:11px; color:#7f99ab; letter-spacing:.18em; text-transform:uppercase; margin-top:4px; }
  .body { padding:0 40px 32px; }
  h2 { font-size:20px; color:#0d1b26; margin:0 0 12px; }
  p { font-size:14px; color:#3a5068; line-height:1.65; margin:0 0 16px; }
  .btn { display:inline-block; padding:14px 32px; background:linear-gradient(90deg,#0ea5c9,#17b8dc);
         color:#fff; border-radius:10px; text-decoration:none; font-size:15px; font-weight:700;
         letter-spacing:.05em; margin:8px 0 20px; }
  .link-box { background:#eef3f7; border-radius:8px; padding:12px 16px; font-size:12px;
              color:#7f99ab; word-break:break-all; margin-top:-8px; margin-bottom:16px; }
  .footer { padding:16px 40px; background:#f5f8fa; border-top:1px solid #dde5ea;
            font-size:11px; color:#7f99ab; text-align:center; }
</style>
</head>
<body>
<div class="wrap">
  <div class="stripe"></div>
  <div class="header">
    <div class="logo-text">R.F. <span>Moto</span> Parts</div>
    <div class="sub">Inventory Management System</div>
  </div>
  <div class="body">
    <h2>Hi, {$name}</h2>
    <p>{$intro}</p>
    <p>Click the button below to set a new password. This link expires in <strong>{$expiresIn}</strong>.</p>
    <p style="text-align:center;">
      <a class="btn" href="{$resetUrl}">Reset My Password</a>
    </p>
    <p style="font-size:13px;color:#7f99ab;">Or copy this link into your browser:</p>
    <div class="link-box">{$resetUrl}</div>
    <p style="font-size:12px;color:#7f99ab;">If you didn't request this, you can safely ignore this email. Your password won't change until you click the link above.</p>
  </div>
  <div class="footer">&copy; {$appName} &nbsp;·&nbsp; This is an automated message, please do not reply.</div>
</div>
</body>
</html>
HTML;

        Mail::html($html, function ($message) use ($user, $subject) {
            $message->to($user->email, $user->full_name ?? $user->username)
                    ->subject($subject);
        });
    }
}
