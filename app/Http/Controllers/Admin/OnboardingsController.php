<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Onboarding;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class OnboardingsController extends Controller
{
    public function create()
    {
        return view('admin.onboardings.create');
    }

    public function store(Request $request)
    {
        if (Onboarding::count() >= 3) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['limit' => 'You can only create exactly 3 onboarding screens.']);
        }

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'max:6144'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['image'] = $this->saveImageToPublicAssets($request, 'image', 'onboardings');
        $data['is_active'] = (bool) $request->boolean('is_active', true);

        Onboarding::create($data);

        return redirect()
            ->route('admin.banners.index')
            ->with('success', 'Onboarding created successfully.');
    }

    public function show(Onboarding $onboarding)
    {
        return view('admin.onboardings.show', compact('onboarding'));
    }

    public function edit(Onboarding $onboarding)
    {
        return view('admin.onboardings.edit', compact('onboarding'));
    }

    public function update(Request $request, Onboarding $onboarding)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'max:6144'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $newImage = $this->saveImageToPublicAssets($request, 'image', 'onboardings');
        if ($newImage) {
            $this->deletePublicAssetIfExists($onboarding->image);
            $data['image'] = $newImage;
        } else {
            unset($data['image']);
        }

        $data['is_active'] = (bool) $request->boolean('is_active', false);

        $onboarding->update($data);

        return redirect()
            ->route('admin.banners.index')
            ->with('success', 'Onboarding updated successfully.');
    }

    public function toggle(Onboarding $onboarding)
    {
        $onboarding->is_active = ! $onboarding->is_active;
        $onboarding->save();

        return back()->with('success', 'Onboarding status updated.');
    }

    public function destroy(Onboarding $onboarding)
    {
        if (Onboarding::count() <= 3) {
            return back()->with('error', 'Onboarding must have exactly 3 screens.');
        }

        $this->deletePublicAssetIfExists($onboarding->image);
        $onboarding->delete();

        return back()->with('success', 'Onboarding deleted.');
    }

    /**
     * Save uploaded file into: public/assets/images/<folder>/
     * Returns path like: assets/images/onboardings/xxx.png
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
