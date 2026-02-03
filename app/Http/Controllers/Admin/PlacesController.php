<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Place;
use App\Models\Subcategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class PlacesController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->get('q', ''));
        $status = $request->get('status');

        $base = Place::query()
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($qq) use ($q) {
                      $qq->where('name_en', 'like', "%{$q}%")
                          ->orWhere('name_ar', 'like', "%{$q}%")
                          ->orWhere('name', 'like', "%{$q}%");
                });
            });

        $totalPlaces = (clone $base)->count();
        $activePlaces = (clone $base)->where('is_active', 1)->count();
        $inactivePlaces = (clone $base)->where('is_active', 0)->count();

        $places = (clone $base)
            ->with(['brand', 'subcategory'])
            ->withCount('branches')
            ->when($status === 'active', fn ($q) => $q->where('is_active', 1))
            ->when($status === 'inactive', fn ($q) => $q->where('is_active', 0))
            ->orderBy('id', 'desc')
            ->paginate(12)
            ->withQueryString();

        return view('admin.places.index', compact(
            'places',
            'q',
            'status',
            'totalPlaces',
            'activePlaces',
            'inactivePlaces'
        ));
    }

    public function create()
    {
        $brands = Brand::query()->orderBy('name_en')->get();
        $subcategories = Subcategory::query()->orderBy('name_en')->get();

        return view('admin.places.create', compact('brands', 'subcategories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'brand_id' => ['nullable', 'exists:brands,id'],
            'subcategory_id' => ['nullable', 'exists:subcategories,id'],
            'name_en' => ['required', 'string', 'max:255'],
            'name_ar' => ['nullable', 'string', 'max:255'],
            'description_en' => ['nullable', 'string'],
            'description_ar' => ['nullable', 'string'],
            'logo' => ['nullable', 'image', 'max:4096'],
            'cover_image' => ['nullable', 'image', 'max:6144'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['logo'] = $this->saveImageToPublicAssets($request, 'logo', 'places');
        $data['cover_image'] = $this->saveImageToPublicAssets($request, 'cover_image', 'places/covers');
        $data['is_active'] = (bool) $request->boolean('is_active', true);

        Place::create($data);

        return redirect()
            ->route('admin.places.index')
            ->with('success', 'Place created successfully.');
    }

    public function show(Place $place)
    {
        $place->load(['brand', 'subcategory']);
        $place->loadCount('branches');
        return view('admin.places.show', compact('place'));
    }

    public function edit(Place $place)
    {
        $brands = Brand::query()->orderBy('name_en')->get();
        $subcategories = Subcategory::query()->orderBy('name_en')->get();

        return view('admin.places.edit', compact('place', 'brands', 'subcategories'));
    }

    public function update(Request $request, Place $place)
    {
        $data = $request->validate([
            'brand_id' => ['nullable', 'exists:brands,id'],
            'subcategory_id' => ['nullable', 'exists:subcategories,id'],
            'name_en' => ['required', 'string', 'max:255'],
            'name_ar' => ['nullable', 'string', 'max:255'],
            'description_en' => ['nullable', 'string'],
            'description_ar' => ['nullable', 'string'],
            'logo' => ['nullable', 'image', 'max:4096'],
            'cover_image' => ['nullable', 'image', 'max:6144'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $newLogo = $this->saveImageToPublicAssets($request, 'logo', 'places');
        if ($newLogo) {
            $this->deletePublicAssetIfExists($place->logo);
            $data['logo'] = $newLogo;
        } else {
            unset($data['logo']);
        }

        $newCover = $this->saveImageToPublicAssets($request, 'cover_image', 'places/covers');
        if ($newCover) {
            $this->deletePublicAssetIfExists($place->cover_image);
            $data['cover_image'] = $newCover;
        } else {
            unset($data['cover_image']);
        }

        $data['is_active'] = (bool) $request->boolean('is_active', false);

        $place->update($data);

        return redirect()
            ->route('admin.places.index')
            ->with('success', 'Place updated successfully.');
    }

    public function toggle(Place $place)
    {
        $place->is_active = ! $place->is_active;
        $place->save();

        return back()->with('success', 'Place status updated.');
    }

    public function destroy(Place $place)
    {
        $this->deletePublicAssetIfExists($place->logo);
        $this->deletePublicAssetIfExists($place->cover_image);
        $place->delete();

        return back()->with('success', 'Place deleted.');
    }

    /**
     * Save uploaded file into: public/assets/images/<folder>/
     * Returns path like: assets/images/places/xxx.png
     */
    private function saveImageToPublicAssets(Request $request, string $field, string $folder): ?string
    {
        if (! $request->hasFile($field)) return null;

        $file = $request->file($field);
        if (! $file->isValid()) return null;

        $ext = strtolower($file->getClientOriginalExtension() ?: 'png');
        $name = Str::uuid()->toString() . '.' . $ext;

        $dir = public_path("assets/images/{$folder}");
        if (! File::exists($dir)) {
            File::makeDirectory($dir, 0755, true);
        }

        $file->move($dir, $name);

        return "assets/images/{$folder}/{$name}";
    }

    private function deletePublicAssetIfExists(?string $path): void
    {
        if (! $path) return;

        $full = public_path($path);
        if (File::exists($full)) {
            @File::delete($full);
        }
    }
}
