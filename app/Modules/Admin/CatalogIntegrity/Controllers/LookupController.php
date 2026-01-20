<?php

namespace App\Modules\Admin\CatalogIntegrity\Controllers;

use App\Support\Api\BaseApiController;
use Illuminate\Http\Request;
use App\Modules\Admin\Catalog\Resources\SubcategoryResource;
use App\Modules\Admin\Catalog\Resources\BranchResource;
use App\Models\Subcategory;
use App\Models\Branch;
use App\Modules\Admin\CatalogIntegrity\Requests\GetSubcategoriesRequest;
use App\Modules\Admin\CatalogIntegrity\Requests\GetPlaceBranchesRequest;

class LookupController extends BaseApiController
{
    public function subcategories(GetSubcategoriesRequest $request, $categoryId)
    {
        $q = Subcategory::where('category_id', (int) $categoryId)->orderBy('sort_order');
        if ($request->has('q')) {
            $q->where('name', 'like', '%' . $request->query('q') . '%');
        }
        $items = $q->get();
        return $this->success(SubcategoryResource::collection($items), 'subcategories.list');
    }

    public function placeBranches(GetPlaceBranchesRequest $request, $placeId)
    {
        $q = Branch::where('place_id', (int) $placeId)->orderBy('created_at', 'desc');
        $items = $q->get();
        return $this->success(BranchResource::collection($items), 'branches.list');
    }
}
