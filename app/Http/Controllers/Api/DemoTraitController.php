<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Support\Traits\Api\ApiResponseTrait;
use App\Support\Traits\Media\PublicUploadTrait;

class DemoTraitController extends Controller
{
    use ApiResponseTrait, PublicUploadTrait;

    public function ping()
    {
        return $this->success(['pong' => true], 'pong');
    }

    public function upload(Request $request)
    {
        $files = $request->file('photos', []);
        if (!is_array($files)) {
            $files = [$files];
        }

        $uploaded = $this->uploadMany($files, 'reviews', ['max_files' => 3]);

        return $this->success($uploaded, 'uploaded');
    }
}
