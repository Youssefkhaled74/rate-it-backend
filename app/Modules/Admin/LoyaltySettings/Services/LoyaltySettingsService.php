<?php

namespace App\Modules\Admin\LoyaltySettings\Services;

use App\Models\PointsSetting;
use App\Support\Exceptions\ApiException;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

class LoyaltySettingsService
{
    public function list(array $filters): LengthAwarePaginator
    {
        $perPage = (int) ($filters['per_page'] ?? 50);
        $query = PointsSetting::query()->orderBy('version', 'desc');
        $page = (int) ($filters['page'] ?? 1);
        return $query->paginate($perPage, ['*'], 'page', $page);
    }

    public function create(array $data): PointsSetting
    {
        // determine next version
        $max = PointsSetting::max('version') ?? 0;
        $data['version'] = $max + 1;

        // attach creating admin if available
        $admin = request()->get('admin');
        if ($admin) $data['created_by_admin_id'] = $admin->id;

        // ensure is_active not mass-activated by default
        $data['is_active'] = $data['is_active'] ?? false;

        return PointsSetting::create($data);
    }

    public function activate(int $id): ?PointsSetting
    {
        $setting = PointsSetting::find($id);
        if (! $setting) return null;

        // if already active, return
        if ($setting->is_active) return $setting;

        $admin = request()->get('admin');
        $adminId = $admin ? $admin->id : null;

        return DB::transaction(function () use ($id, $adminId) {
            // deactivate all via query to bypass model immutability
            DB::table('points_settings')->update(['is_active' => false]);

            // activate selected
            DB::table('points_settings')->where('id', $id)->update(['is_active' => true, 'activated_by_admin_id' => $adminId, 'activated_at' => now()]);

            return PointsSetting::find($id);
        });
    }
}
