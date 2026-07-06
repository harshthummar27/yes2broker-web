<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Password Change Approval</title>
</head>
<body style="font-family: Arial, sans-serif; color: #1f2937; line-height: 1.6; margin: 0; padding: 24px; background: #f9fafb;">
    <div style="max-width: 640px; margin: 0 auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 16px rgba(0,0,0,0.08);">
        <div style="background: #001b73; color: #ffffff; padding: 20px 24px;">
            <h1 style="margin: 0; font-size: 20px;">Admin Password Change Request</h1>
            <p style="margin: 8px 0 0; font-size: 14px; opacity: 0.9;">{{ config('site.name') }} admin panel</p>
        </div>

        <div style="padding: 24px;">
            @if($forgotCurrentPassword)
                <p style="margin: 0 0 16px; padding: 12px 16px; background: #fef3c7; border: 1px solid #f59e0b; border-radius: 8px; color: #92400e; font-size: 14px;">
                    <strong>Forgot current password:</strong> this user could not verify their existing password. Extra care is recommended before approving.
                </p>
            @endif

            <p style="margin: 0 0 16px;">
                An admin user has requested a password change. Please review the details below and choose an action.
            </p>

            <table style="width: 100%; border-collapse: collapse; margin-bottom: 24px;">
                <tr>
                    <td style="padding: 10px 12px; border-bottom: 1px solid #e5e7eb; font-weight: 600; width: 35%;">Name</td>
                    <td style="padding: 10px 12px; border-bottom: 1px solid #e5e7eb;">{{ $user->name }}</td>
                </tr>
                <tr>
                    <td style="padding: 10px 12px; border-bottom: 1px solid #e5e7eb; font-weight: 600;">Email</td>
                    <td style="padding: 10px 12px; border-bottom: 1px solid #e5e7eb;">{{ $user->email }}</td>
                </tr>
                <tr>
                    <td style="padding: 10px 12px; border-bottom: 1px solid #e5e7eb; font-weight: 600;">Requested At</td>
                    <td style="padding: 10px 12px; border-bottom: 1px solid #e5e7eb;">{{ now()->format('d M Y, h:i A') }}</td>
                </tr>
                <tr>
                    <td style="padding: 10px 12px; font-weight: 600;">Link Expires</td>
                    <td style="padding: 10px 12px;">{{ $expiresAt->format('d M Y, h:i A') }}</td>
                </tr>
            </table>

            <p style="margin: 0 0 20px; font-size: 14px; color: #4b5563;">
                Click <strong>Yes</strong> to approve the password change, or <strong>No</strong> to keep the current password.
            </p>

            <div style="display: flex; flex-wrap: wrap; gap: 12px;">
                <a href="{{ $approveUrl }}"
                   style="display: inline-block; background: #16a34a; color: #ffffff; text-decoration: none; font-weight: 700; padding: 12px 24px; border-radius: 8px;">
                    Yes — Change Password
                </a>
                <a href="{{ $rejectUrl }}"
                   style="display: inline-block; background: #dc2626; color: #ffffff; text-decoration: none; font-weight: 700; padding: 12px 24px; border-radius: 8px;">
                    No — Do Not Change
                </a>
            </div>
        </div>
    </div>
</body>
</html>
