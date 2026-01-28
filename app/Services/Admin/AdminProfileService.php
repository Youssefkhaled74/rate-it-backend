<?php

namespace App\Services\Admin;

use App\Models\Admin;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\File;

class AdminProfileService
{
    // we will store files directly under public/ for immediate web access
    protected string $publicBase = 'admin-photos';

    /**
     * Update admin photo: store file, delete old, update model.
     */
    public function updatePhoto(Admin $admin, UploadedFile $file): Admin
    {
        // validate file externally
        $path = "{$this->publicBase}/{$admin->id}";
        $ext = $file->getClientOriginalExtension();
        $filename = now()->format('YmdHis') . '_' . Str::random(8) . '.' . $ext;

        // ensure public directory exists
        $targetDir = public_path($path);
        if (! is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        $target = $targetDir . DIRECTORY_SEPARATOR . $filename;
        // move uploaded file to public folder
        $moved = $file->move($targetDir, $filename);

        if (! $moved || ! file_exists($target)) {
            throw new \RuntimeException('Failed to store admin photo');
        }

        $stored = "{$path}/{$filename}"; // relative to public

        // delete old file if exists and is under admin-photos
        // delete old file if it was stored under public admin-photos
        if (! empty($admin->photo_path) && Str::startsWith($admin->photo_path, "{$this->publicBase}/{$admin->id}/")) {
            try {
                $old = public_path($admin->photo_path);
                if (file_exists($old)) {
                    @unlink($old);
                }
            } catch (\Throwable $e) {
                // ignore deletion errors
            }
        }

        $admin->photo_path = $stored;
        $admin->save();

        return $admin;
    }

    /**
     * Remove admin photo (delete file and null DB column)
     */
    public function removePhoto(Admin $admin): Admin
    {
        if (! empty($admin->photo_path) && Str::startsWith($admin->photo_path, "{$this->publicBase}/{$admin->id}/")) {
            try {
                $old = public_path($admin->photo_path);
                if (file_exists($old)) {
                    @unlink($old);
                }
            } catch (\Throwable $e) {
            }
        }
        $admin->photo_path = null;
        $admin->save();
        return $admin;
    }
}
