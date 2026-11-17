<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reply to your message</title>
</head>
<body style="font-family: sans-serif; line-height: 1.6; color: #374151; max-width: 600px; margin: 0 auto; padding: 20px;">
    <h1 style="color: #0f172a;">Reply from {{ config('app.name') }}</h1>
    <p>Hello {{ $contact->user->name }},</p>
    <p>Thank you for getting in touch. Here is our reply regarding your message <strong>{{ $contact->subject }}</strong>:</p>

    <div style="margin: 24px 0; padding: 16px; background: #f8fafc; border-left: 4px solid #22c55e; border-radius: 0 8px 8px 0;">
        {!! nl2br(e($replyBody)) !!}
    </div>

    <p style="margin-top: 24px; color: #6b7280; font-size: 14px;">— {{ config('app.name') }}</p>
</body>
</html>
