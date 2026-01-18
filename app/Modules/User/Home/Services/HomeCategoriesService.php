<?php

namespace App\Modules\User\Home\Services;

use App\Models\Category;
use Illuminate\Support\Collection;

class HomeCategoriesService
{
    /**
     * Return active categories for home.
     *
     * @return Collection|Category[]
     */
    public function list(): Collection
    {
        return Category::query()
            ->where('is_active', true)
            ->orderBy('id', 'asc')
            ->get();
    }
}
