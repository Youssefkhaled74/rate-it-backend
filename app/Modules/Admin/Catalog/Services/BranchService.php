<?php

namespace App\Modules\Admin\Catalog\Services;

use App\Models\Branch;

class BranchService
{
    public function list(array $filters = [])
    {
        $query = Branch::query();
        if (isset($filters['place_id'])) {
            $query->where('place_id', $filters['place_id']);
        }
        if (isset($filters['active'])) {
            $query->where('is_active', (bool) $filters['active']);
        }
        return $query->orderBy('created_at', 'desc')->get();
    }

    public function create(array $data): Branch
    {
        return Branch::create($data);
    }

    public function find(int $id): ?Branch
    {
        return Branch::find($id);
    }

    public function update(int $id, array $data): ?Branch
    {
        $branch = Branch::find($id);
        if (! $branch) return null;
        // If qr_code_value provided, ensure uniqueness
        if (array_key_exists('qr_code_value', $data)) {
            $exists = Branch::where('qr_code_value', $data['qr_code_value'])
                ->where('id', '!=', $id)
                ->exists();
            if ($exists) {
                throw new \RuntimeException('branches.qr_conflict');
            }
        }
        $branch->update($data);
        return $branch;
    }

    public function regenerateQr(int $id): ?Branch
    {
        $branch = Branch::find($id);
        if (! $branch) return null;

        $attempts = 0;
        do {
            try {
                $token = bin2hex(random_bytes(16));
            } catch (\Exception $e) {
                $token = uniqid('qr_', true);
            }
            $conflict = Branch::where('qr_code_value', $token)->where('id', '!=', $id)->exists();
            $attempts++;
        } while ($conflict && $attempts < 5);

        if ($conflict) {
            throw new \RuntimeException('branches.qr_generation_failed');
        }

        $branch->qr_code_value = $token;
        $branch->qr_generated_at = now();
        $branch->save();
        return $branch;
    }

    public function delete(int $id): bool
    {
        $branch = Branch::find($id);
        if (! $branch) return false;
        return (bool) $branch->delete();
    }
}
