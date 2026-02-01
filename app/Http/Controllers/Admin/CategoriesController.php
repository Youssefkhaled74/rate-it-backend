<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CategoryStoreRequest;
use App\Http\Requests\Admin\CategoryUpdateRequest;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class CategoriesController extends Controller
{
    public function index(Request $request)
    {
        // لو عندك authorize/policies ممكن تفعلهم هنا بعد ما تحل موضوع الصلاحيات
        // $this->authorize('viewAny', Category::class);

        $q = trim((string) $request->get('q', ''));
        $status = $request->get('status', ''); // active | inactive | ''

        $categories = Category::query()
            ->withCount('subcategories')
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($qq) use ($q) {
                    $qq->where('name_en', 'like', "%{$q}%")
                       ->orWhere('name_ar', 'like', "%{$q}%");
                });
            })
            ->when($status !== '', function ($query) use ($status) {
                $query->where('is_active', $status === 'active');
            })
            ->orderByRaw("COALESCE(sort_order, 999999) asc")
            ->orderBy('id', 'desc')
            ->paginate(12)
            ->withQueryString();

        return view('admin.categories.index', compact('categories', 'q', 'status'));
    }

    public function create()
    {
        // $this->authorize('create', Category::class);
        return view('admin.categories.create');
    }

    public function store(CategoryStoreRequest $request)
    {
        // $this->authorize('create', Category::class);

        $data = $request->validated();

        // uploads
        $data['logo'] = $this->saveImageToPublicAssets($request, 'logo', 'categories');
        // icon (small)
        $data['icon'] = $this->saveImageToPublicAssets($request, 'icon', 'categories/icons');

        $data['is_active'] = (bool) ($request->boolean('is_active', true));

        if (empty($data['sort_order'])) {
            $data['sort_order'] = null;
        }

        Category::create($data);

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Category created successfully.');
    }

    public function edit(Category $category)
    {
        // $this->authorize('update', $category);
        return view('admin.categories.edit', compact('category'));
    }

    public function update(CategoryUpdateRequest $request, Category $category)
    {
        // $this->authorize('update', $category);

        $data = $request->validated();

        // replace logo if uploaded
        $newLogo = $this->saveImageToPublicAssets($request, 'logo', 'categories');
        if ($newLogo) {
            $this->deletePublicAssetIfExists($category->logo);
            $data['logo'] = $newLogo;
        } else {
            unset($data['logo']);
        }

        // replace icon if uploaded
        $newIcon = $this->saveImageToPublicAssets($request, 'icon', 'categories/icons');
        if ($newIcon) {
            $this->deletePublicAssetIfExists($category->icon);
            $data['icon'] = $newIcon;
        } else {
            unset($data['icon']);
        }

        $data['is_active'] = (bool) ($request->boolean('is_active', false));

        if (array_key_exists('sort_order', $data) && $data['sort_order'] === '') {
            $data['sort_order'] = null;
        }

        $category->update($data);

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Category updated successfully.');
    }

    public function toggle(Category $category)
    {
        // $this->authorize('update', $category);

        $category->is_active = ! $category->is_active;
        $category->save();

        return back()->with('success', 'Category status updated.');
    }

    public function destroy(Category $category)
    {
        // $this->authorize('delete', $category);

        $this->deletePublicAssetIfExists($category->logo);
        $this->deletePublicAssetIfExists($category->icon);
        $category->delete();

        return back()->with('success', 'Category deleted.');
    }

    /**
     * Save uploaded file into: public/assets/images/<folder>/
     * Returns path like: assets/images/categories/xxx.png
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

        // only delete inside public/
        $full = public_path($path);
        if (File::exists($full)) {
            @File::delete($full);
        }
    }
}
