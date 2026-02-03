<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class BrandsController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->get('q', ''));
        $status = $request->get('status');

        $base = Brand::query()
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($qq) use ($q) {
                    $qq->where('name_en', 'like', "%{$q}%")
                       ->orWhere('name_ar', 'like', "%{$q}%");
                });
            });

        $totalBrands = (clone $base)->count();
        $activeBrands = (clone $base)->where('is_active', 1)->count();
        $inactiveBrands = (clone $base)->where('is_active', 0)->count();

        $brands = (clone $base)
            ->withCount('places')
            ->when($status === 'active', fn ($q) => $q->where('is_active', 1))
            ->when($status === 'inactive', fn ($q) => $q->where('is_active', 0))
            ->orderBy('id', 'desc')
            ->paginate(12)
            ->withQueryString();

        return view('admin.brands.index', compact(
            'brands',
            'q',
            'status',
            'totalBrands',
            'activeBrands',
            'inactiveBrands'
        ));
    }

    public function create()
    {
        return view('admin.brands.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name_en' => ['required', 'string', 'max:255'],
            'name_ar' => ['nullable', 'string', 'max:255'],
            'description_en' => ['nullable', 'string'],
            'description_ar' => ['nullable', 'string'],
            'logo' => ['nullable', 'image', 'max:4096'],
            'cover_image' => ['nullable', 'image', 'max:6144'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['logo'] = $this->saveImageToPublicAssets($request, 'logo', 'brands');
        $data['cover_image'] = $this->saveImageToPublicAssets($request, 'cover_image', 'brands/covers');
        $data['is_active'] = (bool) $request->boolean('is_active', true);

        Brand::create($data);

        return redirect()
            ->route('admin.brands.index')
            ->with('success', 'Brand created successfully.');
    }

    public function show(Brand $brand)
    {
        $brand->loadCount('places');
        return view('admin.brands.show', compact('brand'));
    }

    public function edit(Brand $brand)
    {
        return view('admin.brands.edit', compact('brand'));
    }

    public function update(Request $request, Brand $brand)
    {
        $data = $request->validate([
            'name_en' => ['required', 'string', 'max:255'],
            'name_ar' => ['nullable', 'string', 'max:255'],
            'description_en' => ['nullable', 'string'],
            'description_ar' => ['nullable', 'string'],
            'logo' => ['nullable', 'image', 'max:4096'],
            'cover_image' => ['nullable', 'image', 'max:6144'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $newLogo = $this->saveImageToPublicAssets($request, 'logo', 'brands');
        if ($newLogo) {
            $this->deletePublicAssetIfExists($brand->logo);
            $data['logo'] = $newLogo;
        } else {
            unset($data['logo']);
        }

        $newCover = $this->saveImageToPublicAssets($request, 'cover_image', 'brands/covers');
        if ($newCover) {
            $this->deletePublicAssetIfExists($brand->cover_image);
            $data['cover_image'] = $newCover;
        } else {
            unset($data['cover_image']);
        }

        $data['is_active'] = (bool) $request->boolean('is_active', false);

        $brand->update($data);

        return redirect()
            ->route('admin.brands.index')
            ->with('success', 'Brand updated successfully.');
    }

    public function toggle(Brand $brand)
    {
        $brand->is_active = ! $brand->is_active;
        $brand->save();

        return back()->with('success', 'Brand status updated.');
    }

    public function destroy(Brand $brand)
    {
        $this->deletePublicAssetIfExists($brand->logo);
        $this->deletePublicAssetIfExists($brand->cover_image);
        $brand->delete();

        return back()->with('success', 'Brand deleted.');
    }

    /**
     * Save uploaded file into: public/assets/images/<folder>/
     * Returns path like: assets/images/brands/xxx.png
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
