<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\User\Lookups\Models\Nationality;
use Illuminate\Http\Request;

class NationalitiesController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->get('q', ''));
        $status = $request->get('status');

        $base = Nationality::query()
            ->when($q !== '', function ($qq) use ($q) {
                $qq->where('name_en', 'like', "%{$q}%")
                   ->orWhere('name_ar', 'like', "%{$q}%")
                   ->orWhere('country_code', 'like', "%{$q}%");
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

        return view('admin.lookups.nationalities.index', compact('items','q','status','total','active','inactive'));
    }

    public function create()
    {
        return view('admin.lookups.nationalities.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'country_code' => ['nullable','string','max:5'],
            'name_en' => ['required','string','max:255'],
            'name_ar' => ['nullable','string','max:255'],
            'flag_style' => ['nullable','string','max:50'],
            'flag_size' => ['nullable','integer','min:16','max:256'],
            'is_active' => ['nullable','boolean'],
        ]);
        $data['is_active'] = (bool) $request->boolean('is_active', true);

        Nationality::create($data);
        return redirect()->route('admin.lookups.nationalities.index')->with('success', 'Nationality created.');
    }

    public function edit(Nationality $nationality)
    {
        return view('admin.lookups.nationalities.edit', compact('nationality'));
    }

    public function update(Request $request, Nationality $nationality)
    {
        $data = $request->validate([
            'country_code' => ['nullable','string','max:5'],
            'name_en' => ['required','string','max:255'],
            'name_ar' => ['nullable','string','max:255'],
            'flag_style' => ['nullable','string','max:50'],
            'flag_size' => ['nullable','integer','min:16','max:256'],
            'is_active' => ['nullable','boolean'],
        ]);
        $data['is_active'] = (bool) $request->boolean('is_active', false);

        $nationality->update($data);
        return redirect()->route('admin.lookups.nationalities.index')->with('success', 'Nationality updated.');
    }

    public function toggle(Nationality $nationality)
    {
        $nationality->is_active = ! $nationality->is_active;
        $nationality->save();
        return back()->with('success', 'Nationality status updated.');
    }

    public function destroy(Nationality $nationality)
    {
        $nationality->delete();
        return back()->with('success', 'Nationality deleted.');
    }
}
