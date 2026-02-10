<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SearchSuggestion;
use Illuminate\Http\Request;

class SearchSuggestionsController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->get('q', ''));
        $active = $request->get('active', '');

        $suggestions = SearchSuggestion::query()
            ->when($q !== '', function ($qq) use ($q) {
                $qq->where('term_en', 'like', "%{$q}%")
                   ->orWhere('term_ar', 'like', "%{$q}%");
            })
            ->when($active !== '', fn ($qq) => $qq->where('is_active', (bool) $active))
            ->orderBy('sort_order')
            ->orderBy('id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.search-suggestions.index', compact('suggestions', 'q', 'active'));
    }

    public function create()
    {
        return view('admin.search-suggestions.create');
    }

    public function store(Request $request)
    {
        $data = $this->validateSuggestion($request);
        SearchSuggestion::create($data);

        return redirect()
            ->route('admin.search-suggestions.index')
            ->with('success', __('admin.suggestion_created'));
    }

    public function edit(SearchSuggestion $suggestion)
    {
        return view('admin.search-suggestions.edit', compact('suggestion'));
    }

    public function update(Request $request, SearchSuggestion $suggestion)
    {
        $data = $this->validateSuggestion($request);
        $suggestion->update($data);

        return redirect()
            ->route('admin.search-suggestions.index')
            ->with('success', __('admin.suggestion_updated'));
    }

    public function toggle(SearchSuggestion $suggestion)
    {
        $suggestion->is_active = ! $suggestion->is_active;
        $suggestion->save();

        return back()->with('success', __('admin.suggestion_updated'));
    }

    public function destroy(SearchSuggestion $suggestion)
    {
        $suggestion->delete();
        return back()->with('success', __('admin.suggestion_deleted'));
    }

    private function validateSuggestion(Request $request): array
    {
        $data = $request->validate([
            'term_en' => ['nullable', 'string', 'max:255'],
            'term_ar' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);
        $data['is_active'] = (bool) $request->boolean('is_active', true);

        return $data;
    }
}
