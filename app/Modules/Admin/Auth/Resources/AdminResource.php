<?php

namespace App\Modules\Admin\Auth\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

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
                'created_at' => [
                    'iso' => optional($this->created_at)->toISOString(),
                    'readable' => optional($this->created_at)->format('M d, Y H:i:s'),
                    'relative' => optional($this->created_at)->diffForHumans(),
                ],
                'updated_at' => [
                    'iso' => optional($this->updated_at)->toISOString(),
                    'readable' => optional($this->updated_at)->format('M d, Y H:i:s'),
                    'relative' => optional($this->updated_at)->diffForHumans(),
                ],
                'deleted_at' => $this->deleted_at ? [
                    'iso' => $this->deleted_at->toISOString(),
                    'readable' => $this->deleted_at->format('M d, Y H:i:s'),
                    'relative' => $this->deleted_at->diffForHumans(),
                ] : null,
            ],
        ];
    }
}