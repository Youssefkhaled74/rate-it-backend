<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use Illuminate\Http\Request;

class CitiesController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->get('q', ''));
        $status = $request->get('status');

        $base = City::query()
            ->when($q !== '', function ($qq) use ($q) {
                $qq->where('name_en', 'like', "%{$q}%")
                   ->orWhere('name_ar', 'like', "%{$q}%");
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

        return view('admin.lookups.cities.index', compact('items','q','status','total','active','inactive'));
    }

    public function create()
    {
        return view('admin.lookups.cities.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name_en' => ['required','string','max:255'],
            'name_ar' => ['nullable','string','max:255'],
            'is_active' => ['nullable','boolean'],
        ]);
        $data['is_active'] = (bool) $request->boolean('is_active', true);

        City::create($data);
        return redirect()->route('admin.lookups.cities.index')->with('success', 'City created.');
    }

    public function edit(City $city)
    {
        return view('admin.lookups.cities.edit', compact('city'));
    }

    public function update(Request $request, City $city)
    {
        $data = $request->validate([
            'name_en' => ['required','string','max:255'],
            'name_ar' => ['nullable','string','max:255'],
            'is_active' => ['nullable','boolean'],
        ]);
        $data['is_active'] = (bool) $request->boolean('is_active', false);

        $city->update($data);
        return redirect()->route('admin.lookups.cities.index')->with('success', 'City updated.');
    }

    public function toggle(City $city)
    {
        $city->is_active = ! $city->is_active;
        $city->save();
        return back()->with('success', 'City status updated.');
    }

    public function destroy(City $city)
    {
        $city->delete();
        return back()->with('success', 'City deleted.');
    }
}
