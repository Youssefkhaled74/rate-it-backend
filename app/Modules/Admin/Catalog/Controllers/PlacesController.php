<?php

namespace App\Modules\Admin\Catalog\Controllers;

use App\Support\Api\BaseApiController;
use Illuminate\Http\Request;
use App\Models\Place;
use App\Modules\Admin\Catalog\Requests\StorePlaceRequest;
use App\Modules\Admin\Catalog\Requests\UpdatePlaceRequest;
use App\Modules\Admin\Catalog\Resources\PlaceResource;
use App\Modules\Admin\Catalog\Services\PlaceService;

class PlacesController extends BaseApiController
{
    protected PlaceService $service;

    public function __construct(PlaceService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $filters = [];
        if ($request->has('active')) {
            $filters['active'] = $request->query('active');
        }
        $items = $this->service->list($filters);
        return $this->success(PlaceResource::collection($items), 'places.list');
    }

    public function store(StorePlaceRequest $request)
    {
        $data = $request->only(['name_en','name_ar','address_en','address_ar','phone','lat','lng','is_active','logo']);
        $place = $this->service->create($data);
        return $this->created(new PlaceResource($place), 'places.created');
    }

    public function show($id)
    {
        $place = $this->service->find((int) $id);
        if (! $place) return $this->error('Not found', null, 404);
        return $this->success(new PlaceResource($place));
    }

    public function update(UpdatePlaceRequest $request, $id)
    {
        $place = $this->service->update((int) $id, $request->only(['name_en','name_ar','address_en','address_ar','phone','lat','lng','is_active','logo']));
        if (! $place) return $this->error('Not found', null, 404);
        return $this->success(new PlaceResource($place), 'places.updated');
    }

    public function destroy($id)
    {
        $ok = $this->service->delete((int) $id);
        if (! $ok) return $this->error('Not found', null, 404);
        return $this->noContent('places.deleted');
    }
}
