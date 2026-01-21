<?php

namespace App\Support\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class TimestampResource extends JsonResource
{
    /**
     * Create a new resource instance with a Carbon instance.
     *
     * @param Carbon|null $timestamp
     */
    public function __construct(?Carbon $timestamp = null)
    {
        parent::__construct($timestamp);
    }

    public function toArray($request)
    {
        if (! $this->resource) {
            return null;
        }

        return [
            'iso' => $this->resource->toISOString(),
            'readable' => $this->resource->format('M d, Y H:i:s'),
            'relative' => $this->resource->diffForHumans(),
            'unix' => $this->resource->timestamp,
            'date' => $this->resource->format('Y-m-d'),
            'time' => $this->resource->format('H:i:s'),
        ];
    }
}
