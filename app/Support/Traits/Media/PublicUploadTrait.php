<?php

namespace App\Support\Traits\Media;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;
use App\Support\Exceptions\ApiException;

trait PublicUploadTrait
{
    protected function baseUploadsDir(): string
    {
        return rtrim(Config::get('uploads.base_dir', 'uploads'), '/');
    }

    protected function allowedMimes(): array
    {
        return Config::get('uploads.allowed_mimes', ['jpg','jpeg','png','webp']);
    }

    protected function maxSizeKb(): int
    {
        return (int) Config::get('uploads.max_size_kb', 5120);
    }

    protected function maxFilesForContext(string $context): int
    {
        $contexts = Config::get('uploads.contexts', []);
        return isset($contexts[$context]['max_files']) ? (int) $contexts[$context]['max_files'] : 10;
    }

    public function uploadOne(UploadedFile $file, string $context, array $options = []): array
    {
        $this->validateFile($file, $context, $options);

        $base = public_path($this->baseUploadsDir());
        $y = date('Y');
        $m = date('m');
        $targetDir = $base . DIRECTORY_SEPARATOR . $context . DIRECTORY_SEPARATOR . $y . DIRECTORY_SEPARATOR . $m;

        if (!File::exists($targetDir)) {
            File::makeDirectory($targetDir, 0755, true);
        }

        $ext = strtolower($file->getClientOriginalExtension());
        $name = Str::uuid()->toString() . '_' . time() . '.' . $ext;
        $file->move($targetDir, $name);

        $relative = $this->baseUploadsDir() . '/' . $context . '/' . $y . '/' . $m . '/' . $name;
        $urlBase = config('app.url') ?: env('APP_URL', request()->getSchemeAndHttpHost());

        return [
            'path' => $relative,
            'url' => rtrim($urlBase, '/') . '/' . ltrim($relative, '/'),
            'mime' => $file->getClientMimeType(),
            'size' => $file->getSize(),
        ];
    }

    public function uploadMany(array $files, string $context, array $options = []): array
    {
        $maxFiles = $options['max_files'] ?? $this->maxFilesForContext($context);
        if (count($files) > $maxFiles) {
            throw new ApiException("Too many files for context {$context}. Maximum allowed is {$maxFiles}.", 422);
        }

        $results = [];
        foreach ($files as $file) {
            if (!($file instanceof UploadedFile)) continue;
            $results[] = $this->uploadOne($file, $context, $options);
        }

        return $results;
    }

    protected function validateFile(UploadedFile $file, string $context, array $options = []): void
    {
        $allowed = $options['allowed_mimes'] ?? $this->allowedMimes();
        $maxKb = $options['max_size_kb'] ?? $this->maxSizeKb();

        $ext = strtolower($file->getClientOriginalExtension());
        if (!in_array($ext, $allowed, true)) {
            throw new ApiException("Invalid file type: {$ext}", 422);
        }

        $sizeKb = (int) ceil($file->getSize() / 1024);
        if ($sizeKb > $maxKb) {
            throw new ApiException("File is too large ({$sizeKb} KB). Max allowed is {$maxKb} KB", 422);
        }
    }
}
