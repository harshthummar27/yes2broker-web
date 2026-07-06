<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Change — {{ config('site.name') }}</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f3f4f6; color: #111827; margin: 0; padding: 24px; }
        .card { max-width: 560px; margin: 0 auto; background: #fff; border-radius: 16px; padding: 32px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); }
        h1 { margin: 0 0 12px; font-size: 24px; color: #001b73; }
        p { line-height: 1.6; margin: 0 0 12px; }
        .status-approved { color: #15803d; }
        .status-rejected { color: #b45309; }
        .status-invalid, .status-error { color: #b91c1c; }
        a { color: #001b73; }
    </style>
</head>
<body>
    <div class="card">
        @if($status === 'approved')
            <h1 class="status-approved">Password Changed</h1>
            <p>{{ $message }}</p>
            @if(isset($user))
                <p><strong>User:</strong> {{ $user->name }} ({{ $user->email }})</p>
            @endif
        @elseif($status === 'rejected')
            <h1 class="status-rejected">Password Change Declined</h1>
            <p>{{ $message }}</p>
            @if(isset($user))
                <p><strong>User:</strong> {{ $user->name }} ({{ $user->email }})</p>
            @endif
        @else
            <h1 class="status-{{ $status }}">Request Unavailable</h1>
            <p>{{ $message ?? 'This password change request could not be processed.' }}</p>
        @endif

        <p style="margin-top: 24px;">
            <a href="{{ url('/admin') }}">Go to Admin Login</a>
        </p>
    </div>
</body>
</html>
