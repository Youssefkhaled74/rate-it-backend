<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Subcategory;
use App\Models\VendorUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

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
            ->withCount(['places','branches'])
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
        $subcategories = Subcategory::query()->orderBy('name_en')->get();
        return view('admin.brands.create', compact('subcategories'));
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
            'subcategory_id' => ['nullable', 'exists:subcategories,id'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date'],
            'is_active' => ['nullable', 'boolean'],
        ]);
        if (!empty($data['start_date']) && !empty($data['end_date']) && $data['end_date'] < $data['start_date']) {
            return back()->withErrors(['end_date' => __('admin.end_date_after_start')])->withInput();
        }

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
        $subcategories = Subcategory::query()->orderBy('name_en')->get();
        $vendorAdmin = VendorUser::where('role', 'VENDOR_ADMIN')
            ->where('brand_id', $brand->id)
            ->first();
        return view('admin.brands.edit', compact('brand', 'subcategories', 'vendorAdmin'));
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
            'subcategory_id' => ['nullable', 'exists:subcategories,id'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date'],
            'is_active' => ['nullable', 'boolean'],
        ]);
        if (!empty($data['start_date']) && !empty($data['end_date']) && $data['end_date'] < $data['start_date']) {
            return back()->withErrors(['end_date' => __('admin.end_date_after_start')])->withInput();
        }

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

    public function saveVendorAdmin(Request $request, Brand $brand)
    {
        $vendor = VendorUser::where('role', 'VENDOR_ADMIN')
            ->where('brand_id', $brand->id)
            ->first();

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'phone' => [
                'required',
                'regex:/^[0-9+\-\s()]+$/',
                'max:20',
                Rule::unique('vendor_users', 'phone')->ignore($vendor?->id),
            ],
            'email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('vendor_users', 'email')->ignore($vendor?->id),
            ],
        ];

        if ($vendor) {
            $rules['password'] = ['nullable', 'string', 'min:6', 'confirmed'];
        } else {
            $rules['password'] = ['required', 'string', 'min:6', 'confirmed'];
        }

        $data = $request->validate($rules);

        if ($vendor) {
            $updates = [
                'name' => $data['name'],
                'phone' => $data['phone'],
                'email' => $data['email'] ?? null,
            ];
            if (!empty($data['password'])) {
                $updates['password_hash'] = Hash::make($data['password']);
            }
            $vendor->update($updates);
            $message = 'Brand admin updated.';
        } else {
            VendorUser::create([
                'brand_id' => $brand->id,
                'branch_id' => null,
                'name' => $data['name'],
                'phone' => $data['phone'],
                'email' => $data['email'] ?? null,
                'password_hash' => Hash::make($data['password']),
                'role' => 'VENDOR_ADMIN',
                'is_active' => true,
            ]);
            $message = 'Brand admin created.';
        }

        return back()->with('success', $message);
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
