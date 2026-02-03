<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\User\Lookups\Models\Gender;
use Illuminate\Http\Request;

class GendersController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->get('q', ''));
        $status = $request->get('status');

        $base = Gender::query()
            ->when($q !== '', function ($qq) use ($q) {
                $qq->where('name_en', 'like', "%{$q}%")
                   ->orWhere('name_ar', 'like', "%{$q}%")
                   ->orWhere('code', 'like', "%{$q}%");
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

        return view('admin.lookups.genders.index', compact('items','q','status','total','active','inactive'));
    }

    public function create()
    {
        return view('admin.lookups.genders.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code' => ['required','string','max:50'],
            'name_en' => ['required','string','max:255'],
            'name_ar' => ['nullable','string','max:255'],
            'is_active' => ['nullable','boolean'],
        ]);
        $data['is_active'] = (bool) $request->boolean('is_active', true);

        Gender::create($data);
        return redirect()->route('admin.lookups.genders.index')->with('success', 'Gender created.');
    }

    public function edit(Gender $gender)
    {
        return view('admin.lookups.genders.edit', compact('gender'));
    }

    public function update(Request $request, Gender $gender)
    {
        $data = $request->validate([
            'code' => ['required','string','max:50'],
            'name_en' => ['required','string','max:255'],
            'name_ar' => ['nullable','string','max:255'],
            'is_active' => ['nullable','boolean'],
        ]);
        $data['is_active'] = (bool) $request->boolean('is_active', false);

        $gender->update($data);
        return redirect()->route('admin.lookups.genders.index')->with('success', 'Gender updated.');
    }

    public function toggle(Gender $gender)
    {
        $gender->is_active = ! $gender->is_active;
        $gender->save();
        return back()->with('success', 'Gender status updated.');
    }

    public function destroy(Gender $gender)
    {
        $gender->delete();
        return back()->with('success', 'Gender deleted.');
    }
}
