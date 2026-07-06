<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\PasswordChangeRequestService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PasswordChangeApprovalController extends Controller
{
    public function approve(string $token, Request $request, PasswordChangeRequestService $service): View
    {
        if (! $request->hasValidSignature()) {
            return view('pages.password-change-result', [
                'status' => 'invalid',
                'message' => 'This approval link is invalid or has expired.',
            ]);
        }

        try {
            $changeRequest = $service->approve($token);
        } catch (\Throwable $exception) {
            report($exception);

            return view('pages.password-change-result', [
                'status' => 'error',
                'message' => $exception->getMessage() ?: 'Unable to approve this password change request.',
            ]);
        }

        return view('pages.password-change-result', [
            'status' => 'approved',
            'user' => $changeRequest->user,
            'message' => 'The admin password has been updated successfully.',
        ]);
    }

    public function reject(string $token, Request $request, PasswordChangeRequestService $service): View
    {
        if (! $request->hasValidSignature()) {
            return view('pages.password-change-result', [
                'status' => 'invalid',
                'message' => 'This rejection link is invalid or has expired.',
            ]);
        }

        try {
            $changeRequest = $service->reject($token);
        } catch (\Throwable $exception) {
            report($exception);

            return view('pages.password-change-result', [
                'status' => 'error',
                'message' => $exception->getMessage() ?: 'Unable to reject this password change request.',
            ]);
        }

        return view('pages.password-change-result', [
            'status' => 'rejected',
            'user' => $changeRequest->user,
            'message' => 'The password change request was declined. The current password remains unchanged.',
        ]);
    }
}
