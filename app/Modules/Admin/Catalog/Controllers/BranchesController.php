<?php

namespace App\Modules\Admin\Catalog\Controllers;

use App\Support\Api\BaseApiController;
use Illuminate\Http\Request;
use App\Models\Branch;
use App\Models\Place;
use App\Modules\Admin\Catalog\Requests\StoreBranchRequest;
use App\Modules\Admin\Catalog\Requests\UpdateBranchRequest;
use App\Modules\Admin\Catalog\Resources\BranchResource;
use App\Modules\Admin\Catalog\Services\BranchService;

class BranchesController extends BaseApiController
{
    protected BranchService $service;

    public function __construct(BranchService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $filters = [];
        if ($request->has('place_id')) {
            $filters['place_id'] = (int) $request->query('place_id');
        }
        if ($request->has('active')) {
            $filters['active'] = $request->query('active');
        }
        $items = $this->service->list($filters);
        return $this->success(BranchResource::collection($items), 'branches.list');
    }

    public function store(StoreBranchRequest $request)
    {
        $place = Place::find($request->input('place_id'));
        if (! $place) return $this->error('Place not found', null, 404);
        // If place references a subcategory, ensure the subcategory is configured with criteria
        if ($place->subcategory_id) {
            $sub = $place->subcategory;
            if (! $sub || ! $sub->isReadyForUse()) {
                return $this->error('subcategory.not_ready', null, 422);
            }
        }
        $data = $request->only(['place_id','name_en','name_ar','address_en','address_ar','phone','lat','lng','is_active']);
        $branch = $this->service->create($data);
        return $this->created(new BranchResource($branch), 'branches.created');
    }

    public function show($id)
    {
        $branch = $this->service->find((int) $id);
        if (! $branch) return $this->error('Not found', null, 404);
        return $this->success(new BranchResource($branch));
    }

    public function update(UpdateBranchRequest $request, $id)
    {
        // Ensure the branch's place/subcategory is still valid for usage
        $existing = $this->service->find((int) $id);
        if (! $existing) return $this->error('Not found', null, 404);
        if ($existing->place && $existing->place->subcategory_id) {
            $sub = $existing->place->subcategory;
            if (! $sub || ! $sub->isReadyForUse()) {
                return $this->error('subcategory.not_ready', null, 422);
            }
        }

        $branch = $this->service->update((int) $id, $request->only(['name_en','name_ar','address_en','address_ar','phone','lat','lng','is_active']));
        if (! $branch) return $this->error('Not found', null, 404);
        return $this->success(new BranchResource($branch), 'branches.updated');
    }

    public function destroy($id)
    {
        $ok = $this->service->delete((int) $id);
        if (! $ok) return $this->error('Not found', null, 404);
        return $this->noContent('branches.deleted');
    }

    public function regenerateQr($id)
    {
        $branch = $this->service->find((int) $id);
        if (! $branch) return $this->error('Not found', null, 404);
        try {
            $token = bin2hex(random_bytes(16));
        } catch (\Exception $e) {
            $token = uniqid('qr_', true);
        }
        $branch = $this->service->update((int) $id, ['qr_code_value' => $token, 'qr_generated_at' => now()]);
        return $this->success(new BranchResource($branch), 'branches.qr_regenerated');
    }
}
