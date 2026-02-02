<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Response;

class PublicAssetController extends BaseController
{
    /**
     * Serve files from the `public` disk (storage/app/public) when the public/storage symlink is not present.
     * URL: /storage-proxy/{path}
     */
    public function storageProxy(string $path)
    {
        if (! Storage::disk('public')->exists($path)) {
            abort(404);
        }

        try {
            $disk = Storage::disk('public');
            /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */

            // Prefer the storage disk mimeType() if available (typical), otherwise fall back
            // to PHP's mime_content_type on the actual storage file path or a safe default.
            if (method_exists($disk, 'mimeType')) {
                $mime = $disk->mimeType($path) ?: 'application/octet-stream';
            } else {
                $publicFile = storage_path('app/public/' . ltrim($path, '/'));
                $mime = function_exists('mime_content_type') ? @mime_content_type($publicFile) : null;
                $mime = $mime ?: 'application/octet-stream';
            }

            $content = $disk->get($path);
            return response($content, 200)
                ->header('Content-Type', $mime)
                ->header('Cache-Control', 'public, max-age=31536000');
        } catch (\Throwable $e) {
            abort(404);
        }
    }
}
