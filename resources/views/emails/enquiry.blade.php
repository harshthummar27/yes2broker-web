<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $formTitle }}</title>
</head>
<body style="font-family: Arial, sans-serif; color: #1f2937; line-height: 1.6; margin: 0; padding: 24px; background: #f9fafb;">
    <div style="max-width: 640px; margin: 0 auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 16px rgba(0,0,0,0.08);">
        <div style="background: #1e3a8a; color: #ffffff; padding: 20px 24px;">
            <h1 style="margin: 0; font-size: 20px;">{{ $formTitle }}</h1>
            <p style="margin: 8px 0 0; font-size: 14px; opacity: 0.9;">{{ config('site.name') }} website enquiry</p>
        </div>

        <div style="padding: 24px;">
            @if($source)
                <p style="margin: 0 0 16px;"><strong>Submitted from:</strong> {{ $source }}</p>
            @endif

            <table style="width: 100%; border-collapse: collapse;">
                @foreach($fields as $label => $value)
                    <tr>
                        <td style="padding: 10px 12px; border-bottom: 1px solid #e5e7eb; font-weight: 600; width: 35%; vertical-align: top;">{{ $label }}</td>
                        <td style="padding: 10px 12px; border-bottom: 1px solid #e5e7eb; white-space: pre-wrap;">{{ $value }}</td>
                    </tr>
                @endforeach
            </table>

            @if(count($uploads) > 0)
                <p style="margin: 20px 0 0; font-size: 14px; color: #4b5563;">
                    {{ count($uploads) }} image(s) attached to this email.
                </p>
            @endif
        </div>
    </div>
</body>
</html>
