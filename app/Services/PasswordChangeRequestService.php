<?php

declare(strict_types=1);

namespace App\Services;

use App\Mail\PasswordChangeApprovalMail;
use App\Models\PasswordChangeRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class PasswordChangeRequestService
{
    public function createRequest(User $user, string $newPassword, bool $forgotCurrentPassword = false): PasswordChangeRequest
    {
        PasswordChangeRequest::query()
            ->where('user_id', $user->id)
            ->where('status', PasswordChangeRequest::STATUS_PENDING)
            ->update([
                'status' => PasswordChangeRequest::STATUS_REJECTED,
                'resolved_at' => now(),
            ]);

        $expiresAt = now()->addHours((int) config('auth.password_change.approval_ttl_hours', 24));

        $request = PasswordChangeRequest::query()->create([
            'user_id' => $user->id,
            'token' => Str::random(64),
            'password' => Hash::make($newPassword),
            'status' => PasswordChangeRequest::STATUS_PENDING,
            'expires_at' => $expiresAt,
        ]);

        $approveUrl = URL::temporarySignedRoute(
            'admin.password-change.approve',
            $expiresAt,
            ['token' => $request->token]
        );

        $rejectUrl = URL::temporarySignedRoute(
            'admin.password-change.reject',
            $expiresAt,
            ['token' => $request->token]
        );

        $recipient = (string) config('auth.password_change.approval_email');

        Mail::to($recipient)->send(new PasswordChangeApprovalMail(
            user: $user,
            approveUrl: $approveUrl,
            rejectUrl: $rejectUrl,
            expiresAt: $expiresAt,
            forgotCurrentPassword: $forgotCurrentPassword,
        ));

        return $request;
    }

    public function approve(string $token): PasswordChangeRequest
    {
        $request = PasswordChangeRequest::query()
            ->where('token', $token)
            ->with('user')
            ->firstOrFail();

        if ($request->status !== PasswordChangeRequest::STATUS_PENDING) {
            abort(410, 'This password change request has already been processed.');
        }

        if ($request->isExpired()) {
            $request->update([
                'status' => PasswordChangeRequest::STATUS_REJECTED,
                'resolved_at' => now(),
            ]);

            abort(410, 'This password change request has expired.');
        }

        $user = $request->user;
        $user->password = $request->password;
        $user->save();

        $request->update([
            'status' => PasswordChangeRequest::STATUS_APPROVED,
            'resolved_at' => now(),
        ]);

        return $request->fresh(['user']);
    }

    public function reject(string $token): PasswordChangeRequest
    {
        $request = PasswordChangeRequest::query()
            ->where('token', $token)
            ->with('user')
            ->firstOrFail();

        if ($request->status !== PasswordChangeRequest::STATUS_PENDING) {
            abort(410, 'This password change request has already been processed.');
        }

        $request->update([
            'status' => PasswordChangeRequest::STATUS_REJECTED,
            'resolved_at' => now(),
        ]);

        return $request->fresh(['user']);
    }
}
