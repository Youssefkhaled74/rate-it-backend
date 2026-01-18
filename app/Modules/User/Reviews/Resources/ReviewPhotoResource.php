<?php

namespace App\Modules\User\Reviews\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ReviewPhotoResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'url' => $this->storage_path ? asset($this->storage_path) : null,
        ];
    }
}
<?php

namespace App\Modules\User\Reviews\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ReviewPhotoResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'url' => asset($this->storage_path),
            'storage_path' => $this->storage_path,
        ];
    }
}
