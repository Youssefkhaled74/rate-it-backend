<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Branch;
use App\Models\Category;
use App\Models\Place;
use App\Models\Review;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GlobalSearchController extends Controller
{
    public function suggest(Request $request)
    {
        $this->authorize('viewAny', User::class);

        $q = trim((string) $request->get('q', ''));
        if (mb_strlen($q) < 2) {
            return response()->json(['items' => []]);
        }

        $limit = 5;
        $items = [];

        // Users
        $userSelect = ['id', 'name', 'email'];
        foreach (['avatar_path','avatar','photo_path','picture'] as $col) {
            if (Schema::hasColumn('users', $col)) $userSelect[] = $col;
        }

        $users = User::query()
            ->select(array_unique($userSelect))
            ->when($q !== '', function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('phone', 'like', "%{$q}%");
            })
            ->limit($limit)
            ->get();

        foreach ($users as $u) {
            $items[] = [
                'type' => 'User',
                'label' => $u->name ?? 'User',
                'sub' => $u->email ?? '',
                'url' => route('admin.users.show', $u),
                'image' => $this->resolveUserAvatar($u) ?: asset('assets/images/userdefultphoto.png'),
            ];
        }

        // Brands
        $brands = Brand::query()
            ->select(['id', 'name_en', 'name_ar', 'logo'])
            ->when($q !== '', function ($query) use ($q) {
                $query->where('name_en', 'like', "%{$q}%")
                    ->orWhere('name_ar', 'like', "%{$q}%");
            })
            ->limit($limit)
            ->get();

        foreach ($brands as $b) {
            $items[] = [
                'type' => 'Brand',
                'label' => $b->name_en ?: ($b->name_ar ?? 'Brand'),
                'sub' => $b->name_ar ?: '',
                'url' => route('admin.brands.show', $b),
                'image' => $b->logo ? asset($b->logo) : null,
            ];
        }

        // Places
        $places = Place::query()
            ->select(['id', 'name_en', 'name_ar', 'title_en', 'title_ar', 'name', 'title', 'logo'])
            ->when($q !== '', function ($query) use ($q) {
                $query->where('name_en', 'like', "%{$q}%")
                    ->orWhere('name_ar', 'like', "%{$q}%")
                    ->orWhere('title_en', 'like', "%{$q}%")
                    ->orWhere('title_ar', 'like', "%{$q}%")
                    ->orWhere('name', 'like', "%{$q}%")
                    ->orWhere('title', 'like', "%{$q}%");
            })
            ->limit($limit)
            ->get();

        foreach ($places as $p) {
            $items[] = [
                'type' => 'Place',
                'label' => $p->display_name,
                'sub' => '',
                'url' => route('admin.places.show', $p),
                'image' => $p->logo ? asset($p->logo) : null,
            ];
        }

        // Branches
        $branches = Branch::query()
            ->select(['id', 'name', 'address'])
            ->when($q !== '', function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                    ->orWhere('address', 'like', "%{$q}%");
            })
            ->limit($limit)
            ->get();

        foreach ($branches as $br) {
            $items[] = [
                'type' => 'Branch',
                'label' => $br->name ?: 'Branch',
                'sub' => $br->address ?? '',
                'url' => route('admin.branches.show', $br),
                'image' => null,
            ];
        }

        // Categories
        if (Schema::hasTable('categories')) {
            $categories = Category::query()
                ->select(['id', 'name_en', 'name_ar'])
                ->when($q !== '', function ($query) use ($q) {
                    $query->where('name_en', 'like', "%{$q}%")
                        ->orWhere('name_ar', 'like', "%{$q}%");
                })
                ->limit($limit)
                ->get();

            foreach ($categories as $c) {
                $items[] = [
                    'type' => 'Category',
                    'label' => $c->name_en ?: ($c->name_ar ?? 'Category'),
                    'sub' => $c->name_ar ?: '',
                    'url' => route('admin.categories.show', $c),
                    'image' => null,
                ];
            }
        }

        // Reviews (if a show route exists, otherwise skip)
        if (Route::has('admin.reviews.show')) {
            $reviews = Review::query()
                ->select(['id', 'comment', 'overall_rating'])
                ->when($q !== '', function ($query) use ($q) {
                    $query->where('comment', 'like', "%{$q}%");
                })
                ->limit($limit)
                ->get();

            foreach ($reviews as $r) {
                $items[] = [
                    'type' => 'Review',
                    'label' => Str::limit($r->comment ?? 'Review', 40),
                    'sub' => $r->overall_rating ? ('Rating: ' . $r->overall_rating) : '',
                    'url' => route('admin.reviews.show', $r),
                    'image' => null,
                ];
            }
        }

        return response()->json(['items' => $items]);
    }

    protected function resolveUserAvatar(User $user): ?string
    {
        foreach (['avatar_path','avatar','photo_path','picture'] as $col) {
            if (!empty($user->{$col})) {
                $path = $user->{$col};
                if (file_exists(public_path($path))) return asset($path);
                if (Storage::disk('public')->exists($path)) return route('storage.proxy', ['path' => $path]);
            }
        }
        return null;
    }
}
