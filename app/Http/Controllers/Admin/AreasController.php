<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\City;
use Illuminate\Http\Request;

class AreasController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->get('q', ''));
        $status = $request->get('status');

        $base = Area::query()
            ->with('city')
            ->when($q !== '', function ($qq) use ($q) {
                $qq->where('name_en', 'like', "%{$q}%")
                   ->orWhere('name_ar', 'like', "%{$q}%")
                   ->orWhereHas('city', function ($c) use ($q) {
                       $c->where('name_en', 'like', "%{$q}%")
                         ->orWhere('name_ar', 'like', "%{$q}%");
                   });
            });

        $total = (clone $base)->count();
        $active = (clone $base)->where('is_active', true)->count();
        $inactive = (clone $base)->where('is_active', false)->count();

        $items = (clone $base)
            ->when($status === 'active', fn ($q) => $q->where('is_active', true))
            ->when($status === 'inactive', fn ($q) => $q->where('is_active', false))
            ->orderBy('id', 'desc')
            ->paginate(12)
            ->withQueryString();

        return view('admin.lookups.areas.index', compact('items','q','status','total','active','inactive'));
    }

    public function create()
    {
        $cities = City::orderBy('name_en')->get();
        return view('admin.lookups.areas.create', compact('cities'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'city_id' => ['required','exists:cities,id'],
            'name_en' => ['required','string','max:255'],
            'name_ar' => ['nullable','string','max:255'],
            'is_active' => ['nullable','boolean'],
        ]);
        $data['is_active'] = (bool) $request->boolean('is_active', true);

        Area::create($data);
        return redirect()->route('admin.lookups.areas.index')->with('success', 'Area created.');
    }

    public function edit(Area $area)
    {
        $cities = City::orderBy('name_en')->get();
        return view('admin.lookups.areas.edit', compact('area','cities'));
    }

    public function update(Request $request, Area $area)
    {
        $data = $request->validate([
            'city_id' => ['required','exists:cities,id'],
            'name_en' => ['required','string','max:255'],
            'name_ar' => ['nullable','string','max:255'],
            'is_active' => ['nullable','boolean'],
        ]);
        $data['is_active'] = (bool) $request->boolean('is_active', false);

        $area->update($data);
        return redirect()->route('admin.lookups.areas.index')->with('success', 'Area updated.');
    }

    public function toggle(Area $area)
    {
        $area->is_active = ! $area->is_active;
        $area->save();
        return back()->with('success', 'Area status updated.');
    }

    public function destroy(Area $area)
    {
        $area->delete();
        return back()->with('success', 'Area deleted.');
    }
}
