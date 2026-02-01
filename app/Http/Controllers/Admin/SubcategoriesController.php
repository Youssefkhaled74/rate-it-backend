<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Http\Request;

class SubcategoriesController extends Controller
{
    public function store(Request $request, Category $category)
    {
        // لو عندك Policies استخدم authorize هنا
        // $this->authorize('update', $category);

        $data = $request->validate([
            'name_en'    => ['required','string','max:255'],
            'name_ar'    => ['required','string','max:255'],
            'image'      => ['nullable','image','mimes:jpg,jpeg,png,webp','max:2048'],
            'sort_order' => ['nullable','integer','min:0'],
            'is_active'  => ['nullable','boolean'],
        ]);

        $data['category_id'] = $category->id;
        $data['is_active'] = (bool)($data['is_active'] ?? true);

        if ($request->hasFile('image')) {
            $data['image'] = $this->savePublicImage($request->file('image'), 'assets/images/subcategories');
        }

        Subcategory::create($data);

        return back()->with('success', 'Subcategory created.');
    }

    public function update(Request $request, Subcategory $subcategory)
    {
        // $this->authorize('update', $subcategory);

        $data = $request->validate([
            'name_en'    => ['required','string','max:255'],
            'name_ar'    => ['required','string','max:255'],
            'image'      => ['nullable','image','mimes:jpg,jpeg,png,webp','max:2048'],
            'sort_order' => ['nullable','integer','min:0'],
            'is_active'  => ['nullable','boolean'],
        ]);

        $data['is_active'] = (bool)($data['is_active'] ?? $subcategory->is_active);

        if ($request->hasFile('image')) {
            // delete old
            if (!empty($subcategory->image) && file_exists(public_path($subcategory->image))) {
                @unlink(public_path($subcategory->image));
            }
            $data['image'] = $this->savePublicImage($request->file('image'), 'assets/images/subcategories');
        }

        $subcategory->update($data);

        return back()->with('success', 'Subcategory updated.');
    }

    public function toggle(Subcategory $subcategory)
    {
        // $this->authorize('update', $subcategory);

        $subcategory->update([
            'is_active' => ! (bool)$subcategory->is_active,
        ]);

        return back()->with('success', 'Subcategory status updated.');
    }

    public function destroy(Subcategory $subcategory)
    {
        // $this->authorize('delete', $subcategory);

        if (!empty($subcategory->image) && file_exists(public_path($subcategory->image))) {
            @unlink(public_path($subcategory->image));
        }

        $subcategory->delete();

        return back()->with('success', 'Subcategory deleted.');
    }

    private function savePublicImage($file, string $folder): string
    {
        $folder = trim($folder, '/');
        $name = uniqid('img_', true) . '.' . $file->getClientOriginalExtension();
        $targetDir = public_path($folder);

        if (!is_dir($targetDir)) {
            @mkdir($targetDir, 0777, true);
        }

        $file->move($targetDir, $name);

        return $folder . '/' . $name; // ex: assets/images/subcategories/img_xxx.png
    }
}
