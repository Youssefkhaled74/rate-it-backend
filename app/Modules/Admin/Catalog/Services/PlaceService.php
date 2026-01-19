<?php

namespace App\Modules\Admin\Catalog\Services;

use App\Models\Place;

class PlaceService
{
    public function list(array $filters = [])
    {
        $query = Place::query();
        if (isset($filters['active'])) {
            $query->where('is_active', (bool) $filters['active']);
        }
        return $query->orderBy('created_at', 'desc')->get();
    }

    public function create(array $data): Place
    {
        return Place::create($data);
    }

    public function find(int $id): ?Place
    {
        return Place::find($id);
    }

    public function update(int $id, array $data): ?Place
    {
        $place = Place::find($id);
        if (! $place) return null;
        $place->update($data);
        return $place;
    }

    public function delete(int $id): bool
    {
        $place = Place::find($id);
        if (! $place) return false;
        return (bool) $place->delete();
    }
}
