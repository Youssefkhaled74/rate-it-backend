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
            $mime = Storage::disk('public')->mimeType($path) ?: 'application/octet-stream';
            $content = Storage::disk('public')->get($path);
            return response($content, 200)
                ->header('Content-Type', $mime)
                ->header('Cache-Control', 'public, max-age=31536000');
        } catch (\Throwable $e) {
            abort(404);
        }
    }
}
