<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RatingCriteria;
use App\Models\RatingCriteriaChoice;
use App\Models\Subcategory;
use Illuminate\Http\Request;

class RatingQuestionsController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->get('q', ''));
        $type = strtoupper((string) $request->get('type', ''));
        $subcategoryId = (int) $request->get('subcategory_id', 0);

        $query = RatingCriteria::query()
            ->with(['subcategory.category', 'choices'])
            ->when($q !== '', function ($qq) use ($q) {
                $qq->where(function ($q2) use ($q) {
                    $q2->where('question_text', 'like', "%{$q}%")
                       ->orWhere('question_en', 'like', "%{$q}%")
                       ->orWhere('question_ar', 'like', "%{$q}%");
                });
            })
            ->when(in_array($type, ['RATING','YES_NO','MULTIPLE_CHOICE'], true), function ($qq) use ($type) {
                $qq->where('type', $type);
            })
            ->when($subcategoryId > 0, function ($qq) use ($subcategoryId) {
                $qq->where('subcategory_id', $subcategoryId);
            })
            ->orderBy('id', 'desc');

        $questions = $query->paginate(12)->withQueryString();

        $totalRating = RatingCriteria::where('type', 'RATING')->count();

        $subcategories = Subcategory::with('category')->orderBy('name_en')->get();

        return view('admin.rating-questions.index', compact(
            'questions',
            'q',
            'type',
            'totalRating',
            'subcategories',
            'subcategoryId'
        ));
    }

    public function create()
    {
        $subcategories = Subcategory::with('category')->orderBy('name_en')->get();
        return view('admin.rating-questions.create', compact('subcategories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'question_text' => ['required', 'string'],
            'question_ar' => ['nullable', 'string'],
            'type' => ['required', 'in:RATING,YES_NO,MULTIPLE_CHOICE'],
            'subcategory_id' => ['required', 'exists:subcategories,id'],
            'is_required' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer'],
            'choices_en' => ['nullable', 'array'],
            'choices_en.*' => ['nullable', 'string'],
            'choices_ar' => ['nullable', 'array'],
            'choices_ar.*' => ['nullable', 'string'],
        ]);

        $type = strtoupper($data['type']);
        $data['is_required'] = (bool) $request->boolean('is_required', false);
        $data['is_active'] = (bool) $request->boolean('is_active', true);
        $data['question_en'] = $data['question_text'];

        $criteria = RatingCriteria::create($data);

        if ($type === 'MULTIPLE_CHOICE') {
            $choicesEn = $this->normalizeChoices($data['choices_en'] ?? []);
            $choicesAr = $this->normalizeChoices($data['choices_ar'] ?? []);
            if (count($choicesEn) < 2) {
                $criteria->delete();
                return back()->withErrors(['choices_en' => 'Please add at least 2 choices.'])->withInput();
            }

            foreach ($choicesEn as $i => $choiceEn) {
                RatingCriteriaChoice::create([
                    'criteria_id' => $criteria->id,
                    'choice_text' => $choiceEn,
                    'choice_en' => $choiceEn,
                    'choice_ar' => $choicesAr[$i] ?? null,
                    'value' => $i + 1,
                    'sort_order' => $i + 1,
                    'is_active' => true,
                ]);
            }
        }

        return redirect()
            ->route('admin.rating-questions.index')
            ->with('success', 'Question created successfully.');
    }

    public function edit(RatingCriteria $question)
    {
        $subcategories = Subcategory::with('category')->orderBy('name_en')->get();
        $question->load('choices');
        return view('admin.rating-questions.edit', compact('question', 'subcategories'));
    }

    public function update(Request $request, RatingCriteria $question)
    {
        $data = $request->validate([
            'question_text' => ['required', 'string'],
            'question_ar' => ['nullable', 'string'],
            'type' => ['required', 'in:RATING,YES_NO,MULTIPLE_CHOICE'],
            'subcategory_id' => ['required', 'exists:subcategories,id'],
            'is_required' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer'],
            'choices_en' => ['nullable', 'array'],
            'choices_en.*' => ['nullable', 'string'],
            'choices_ar' => ['nullable', 'array'],
            'choices_ar.*' => ['nullable', 'string'],
        ]);

        $type = strtoupper($data['type']);
        $data['is_required'] = (bool) $request->boolean('is_required', false);
        $data['is_active'] = (bool) $request->boolean('is_active', false);
        $data['question_en'] = $data['question_text'];

        $question->update($data);

        if ($type !== 'MULTIPLE_CHOICE') {
            $question->choices()->delete();
        } else {
            $choicesEn = $this->normalizeChoices($data['choices_en'] ?? []);
            $choicesAr = $this->normalizeChoices($data['choices_ar'] ?? []);
            if (count($choicesEn) < 2) {
                return back()->withErrors(['choices_en' => 'Please add at least 2 choices.'])->withInput();
            }

            $question->choices()->delete();
            foreach ($choicesEn as $i => $choiceEn) {
                RatingCriteriaChoice::create([
                    'criteria_id' => $question->id,
                    'choice_text' => $choiceEn,
                    'choice_en' => $choiceEn,
                    'choice_ar' => $choicesAr[$i] ?? null,
                    'value' => $i + 1,
                    'sort_order' => $i + 1,
                    'is_active' => true,
                ]);
            }
        }

        return redirect()
            ->route('admin.rating-questions.index')
            ->with('success', 'Question updated successfully.');
    }

    public function toggle(RatingCriteria $question)
    {
        $question->is_active = ! $question->is_active;
        $question->save();

        return back()->with('success', 'Question status updated.');
    }

    public function destroy(RatingCriteria $question)
    {
        $question->delete();
        return back()->with('success', 'Question deleted.');
    }

    private function normalizeChoices(array $raw): array
    {
        $items = array_map(fn ($v) => is_string($v) ? trim($v) : '', $raw);
        return array_values(array_filter($items, fn ($v) => $v !== ''));
    }
}
