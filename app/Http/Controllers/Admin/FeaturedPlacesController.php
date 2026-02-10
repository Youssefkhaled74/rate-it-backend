<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Place;
use Illuminate\Http\Request;

class FeaturedPlacesController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->get('q', ''));
        $brandId = (int) $request->get('brand_id', 0);
        $featured = $request->get('featured', '');

        $query = Place::query()
            ->with(['brand'])
            ->when($q !== '', function ($qq) use ($q) {
                $qq->where('name_en', 'like', "%{$q}%")
                    ->orWhere('name_ar', 'like', "%{$q}%")
                    ->orWhere('name', 'like', "%{$q}%");
            })
            ->when($brandId > 0, fn ($qq) => $qq->where('brand_id', $brandId))
            ->when($featured !== '', fn ($qq) => $qq->where('is_featured', (bool) $featured))
            ->orderByDesc('is_featured')
            ->orderByDesc('id');

        $places = $query->paginate(12)->withQueryString();
        $brands = Brand::query()->orderBy('name_en')->get();

        $stats = [
            'total' => (int) Place::count(),
            'featured' => (int) Place::where('is_featured', true)->count(),
        ];

        return view('admin.featured-places.index', compact('places', 'q', 'brandId', 'featured', 'brands', 'stats'));
    }

    public function toggle(Place $place)
    {
        $place->is_featured = ! $place->is_featured;
        $place->save();

        return back()->with('success', $place->is_featured ? __('admin.place_featured') : __('admin.place_unfeatured'));
    }
}
