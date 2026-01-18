<?php

namespace App\Support\Traits\Uploads;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

trait PublicUploadTrait
{
    protected function allowedImageExtensions(): array
    {
        return ['jpg', 'jpeg', 'png', 'webp'];
    }

    protected function maxImageSizeInKilobytes(): int
    {
        return config('uploads.max_kb', 5120); // default 5MB
    }

    public function uploadPublicImage(UploadedFile $file, string $dir = 'uploads/banners'): string
    {
        $ext = strtolower($file->getClientOriginalExtension());
        if (! in_array($ext, $this->allowedImageExtensions(), true)) {
            throw new \InvalidArgumentException('Invalid image extension.');
        }

        $sizeKb = (int) ceil($file->getSize() / 1024);
        if ($sizeKb > $this->maxImageSizeInKilobytes()) {
            throw new \InvalidArgumentException('Image too large.');
        }

        $publicDir = public_path(trim($dir, '/'));
        if (! is_dir($publicDir)) {
            mkdir($publicDir, 0755, true);
        }

        $filename = Str::random(12) . '-' . time() . '.' . $ext;
        $file->move($publicDir, $filename);

        return trim($dir, '/') . '/' . $filename;
    }
}
