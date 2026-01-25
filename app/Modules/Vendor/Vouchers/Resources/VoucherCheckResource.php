<?php

namespace App\Modules\Vendor\Vouchers\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Support\Resources\TimestampResource;

class VoucherCheckResource extends JsonResource
{
    public function toArray($request)
    {
        $data = [
            'code' => $this->code,
            'status' => $this->status,
            'issued_at' => $this->issued_at ? new TimestampResource($this->issued_at) : null,
            'expires_at' => $this->expires_at ? new TimestampResource($this->expires_at) : null,
            'brand' => [
                'id' => $this->brand->id ?? null,
                'name' => $this->brand->name ?? null,
            ],
        ];

        // Add used details if voucher is USED
        if ($this->status === 'USED') {
            $data['used_at'] = $this->used_at ? new TimestampResource($this->used_at) : null;
            $data['used_branch'] = $this->usedBranch ? [
                'id' => $this->usedBranch->id,
                'name' => $this->usedBranch->name,
            ] : null;
            $data['verified_by'] = $this->verifiedByVendor ? [
                'id' => $this->verifiedByVendor->id,
                'name' => $this->verifiedByVendor->name,
                'phone' => $this->verifiedByVendor->phone,
            ] : null;
        }

        return $data;
    }
}
