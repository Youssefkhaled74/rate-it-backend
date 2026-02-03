<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Brand;
use App\Models\Onboarding;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class BannersController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->get('q', ''));

        $banners = Banner::query()
            ->with('brand')
            ->when($q !== '', function ($query) use ($q) {
                $query->where('offer_name', 'like', "%{$q}%");
            })
            ->orderBy('id', 'desc')
            ->get();

        $onboardings = Onboarding::query()
            ->when($q !== '', function ($query) use ($q) {
                $query->where('title', 'like', "%{$q}%")
                      ->orWhere('subtitle', 'like', "%{$q}%");
            })
            ->orderBy('id', 'desc')
            ->get();

        return view('admin.banners.index', compact('banners', 'onboardings', 'q'));
    }

    public function create()
    {
        $brands = Brand::query()->orderBy('name_en')->get();
        return view('admin.banners.create', compact('brands'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'offer_name' => ['required', 'string', 'max:255'],
            'brand_id' => ['nullable', 'exists:brands,id'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'image' => ['nullable', 'image', 'max:6144'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['image'] = $this->saveImageToPublicAssets($request, 'image', 'banners');
        $data['is_active'] = (bool) $request->boolean('is_active', true);

        Banner::create($data);

        return redirect()
            ->route('admin.banners.index')
            ->with('success', 'Banner created successfully.');
    }

    public function show(Banner $banner)
    {
        $banner->load('brand');
        return view('admin.banners.show', compact('banner'));
    }

    public function edit(Banner $banner)
    {
        $brands = Brand::query()->orderBy('name_en')->get();
        return view('admin.banners.edit', compact('banner', 'brands'));
    }

    public function update(Request $request, Banner $banner)
    {
        $data = $request->validate([
            'offer_name' => ['required', 'string', 'max:255'],
            'brand_id' => ['nullable', 'exists:brands,id'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'image' => ['nullable', 'image', 'max:6144'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $newImage = $this->saveImageToPublicAssets($request, 'image', 'banners');
        if ($newImage) {
            $this->deletePublicAssetIfExists($banner->image);
            $data['image'] = $newImage;
        } else {
            unset($data['image']);
        }

        $data['is_active'] = (bool) $request->boolean('is_active', false);

        $banner->update($data);

        return redirect()
            ->route('admin.banners.index')
            ->with('success', 'Banner updated successfully.');
    }

    public function toggle(Banner $banner)
    {
        $banner->is_active = ! $banner->is_active;
        $banner->save();

        return back()->with('success', 'Banner status updated.');
    }

    public function destroy(Banner $banner)
    {
        $this->deletePublicAssetIfExists($banner->image);
        $banner->delete();

        return back()->with('success', 'Banner deleted.');
    }

    /**
     * Save uploaded file into: public/assets/images/<folder>/
     * Returns path like: assets/images/banners/xxx.png
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
