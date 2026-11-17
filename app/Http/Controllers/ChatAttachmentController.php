<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ChatAttachmentController extends Controller
{
    public function __invoke(Message $message): StreamedResponse
    {
        abort_unless($message->hasAttachment(), 404);

        // Access is enforced by the `signed` route middleware (URL only returned from authenticated chat APIs).

        $disk = $message->attachment_disk ?: 'local';
        $filename = $message->attachment_name ?: basename((string) $message->attachment_path);
        $mimeType = (string) ($message->attachment_mime_type ?: 'application/octet-stream');

        return Storage::disk($disk)->response(
            $message->attachment_path,
            $filename,
            [
                'Content-Type' => $mimeType,
                'Content-Disposition' => 'inline; filename="'.$filename.'"',
            ]
        );
    }
}
