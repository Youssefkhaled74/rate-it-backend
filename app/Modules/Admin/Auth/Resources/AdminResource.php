<?php

namespace App\Modules\Admin\Auth\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Support\Resources\TimestampResource;

class AdminResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'role' => $this->role,
            'is_active' => (bool) $this->is_active,
            'roles' => $this->roles->map(fn($role) => [
                'id' => $role->id,
                'name' => $role->name,
                'description' => $role->description,
            ])->values(),
            'permissions' => $this->roles->flatMap->permissions->map(fn($permission) => [
                'id' => $permission->id,
                'name' => $permission->name,
                'description' => $permission->description,
            ])->unique('id')->values(),
            'timestamps' => [
                'created_at' => new TimestampResource($this->created_at),
                'updated_at' => new TimestampResource($this->updated_at),
                'deleted_at' => new TimestampResource($this->deleted_at),
            ],
        ];
    }
}