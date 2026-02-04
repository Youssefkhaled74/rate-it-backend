<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\\Models\\Place;
use App\\Models\\City;
use App\\Models\\Area;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BranchesController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->get('q', ''));
        $status = $request->get('status');

        $base = Branch::query()
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($qq) use ($q) {
                    $qq->where('name', 'like', "%{$q}%")
                       ->orWhere('address', 'like', "%{$q}%");
                })->orWhereHas('place', function ($p) use ($q) {
                    $p->where('name_en', 'like', "%{$q}%")
                      ->orWhere('name_ar', 'like', "%{$q}%")
                      ->orWhere('title_en', 'like', "%{$q}%")
                      ->orWhere('title_ar', 'like', "%{$q}%");
                });
            });

        $totalBranches = (clone $base)->count();
        $activeBranches = (clone $base)->where('is_active', 1)->count();
        $inactiveBranches = (clone $base)->where('is_active', 0)->count();

        $branches = (clone $base)
            ->with(['place.brand'])
            ->when($status === 'active', fn ($q) => $q->where('is_active', 1))
            ->when($status === 'inactive', fn ($q) => $q->where('is_active', 0))
            ->orderBy('id', 'desc')
            ->paginate(12)
            ->withQueryString();

        return view('admin.branches.index', compact(
            'branches',
            'q',
            'status',
            'totalBranches',
            'activeBranches',
            'inactiveBranches'
        ));
    }

    public function create()
    {
        $places = Place::query()->orderBy('name_en')->get();
        $cities = City::query()->orderBy('name_en')->get();
        $areas = Area::query()->orderBy('name_en')->get();
        return view('admin.branches.create', compact('places','cities','areas'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'place_id' => ['required', 'exists:places,id'],
            'name' => ['nullable', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:1000'],
            'city_id' => ['nullable','exists:cities,id'],
            'area_id' => ['nullable','exists:areas,id'],
            'city_id' => ['nullable','exists:cities,id'],
            'area_id' => ['nullable','exists:areas,id'],
            'lat' => ['nullable', 'numeric'],
            'lng' => ['nullable', 'numeric'],
            'review_cooldown_days' => ['nullable', 'integer', 'min:0'],
            'working_hours' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        if (! empty($data['working_hours'])) {
            $decoded = json_decode($data['working_hours'], true);
            $data['working_hours'] = json_last_error() === JSON_ERROR_NONE ? $decoded : null;
        } else {
            $data['working_hours'] = null;
        }

        $data['is_active'] = (bool) $request->boolean('is_active', true);
        $data['qr_code_value'] = (string) Str::uuid();
        $data['qr_generated_at'] = now();

        Branch::create($data);

        return redirect()
            ->route('admin.branches.index')
            ->with('success', 'Branch created successfully.');
    }

    public function show(Branch $branch)
    {
        $branch->load(['place.brand']);
        return view('admin.branches.show', compact('branch'));
    }

    public function edit(Branch $branch)
    {
        $places = Place::query()->orderBy('name_en')->get();
        $cities = City::query()->orderBy('name_en')->get();
        $areas = Area::query()->orderBy('name_en')->get();
        return view('admin.branches.edit', compact('branch','places','cities','areas'));
    }

    public function update(Request $request, Branch $branch)
    {
        $data = $request->validate([
            'place_id' => ['required', 'exists:places,id'],
            'name' => ['nullable', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:1000'],
            'city_id' => ['nullable','exists:cities,id'],
            'area_id' => ['nullable','exists:areas,id'],
            'city_id' => ['nullable','exists:cities,id'],
            'area_id' => ['nullable','exists:areas,id'],
            'lat' => ['nullable', 'numeric'],
            'lng' => ['nullable', 'numeric'],
            'review_cooldown_days' => ['nullable', 'integer', 'min:0'],
            'working_hours' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        if (! empty($data['working_hours'])) {
            $decoded = json_decode($data['working_hours'], true);
            $data['working_hours'] = json_last_error() === JSON_ERROR_NONE ? $decoded : null;
        } else {
            $data['working_hours'] = null;
        }

        $data['is_active'] = (bool) $request->boolean('is_active', false);

        $branch->update($data);

        return redirect()
            ->route('admin.branches.index')
            ->with('success', 'Branch updated successfully.');
    }

    public function toggle(Branch $branch)
    {
        $branch->is_active = ! $branch->is_active;
        $branch->save();

        return back()->with('success', 'Branch status updated.');
    }

    public function destroy(Branch $branch)
    {
        $branch->delete();

        return back()->with('success', 'Branch deleted.');
    }
}


