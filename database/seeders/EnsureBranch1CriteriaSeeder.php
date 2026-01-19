<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Branch;
use App\Models\RatingCriteria;
use App\Models\RatingCriteriaChoice;

class EnsureBranch1CriteriaSeeder extends Seeder
{
    public function run(): void
    {
        $branch = Branch::find(1);
        if (! $branch) {
            $this->command->error('Branch id=1 not found; cannot ensure criteria.');
            return;
        }

        $place = $branch->place;
        if (! $place) {
            $this->command->info('Branch 1 has no place; creating demo place');
            $place = $branch->place()->create(['name_en' => 'Demo Place for Branch 1', 'name_ar' => 'موقع تجريبي']);
        }

        $subcatId = $place->subcategory_id;
        if (! $subcatId) {
            $this->command->info('Place has no subcategory; creating demo Clinics subcategory');
            // Create or find Clinics subcategory under first category
            $categoryId = \App\Models\Category::first()?->id ?? null;
            $subcat = \App\Models\Subcategory::updateOrCreate(
                ['category_id' => $categoryId, 'name_en' => 'Clinics'],
                ['name_ar' => 'عيادات', 'is_active' => true]
            );
            $place->subcategory_id = $subcat->id;
            $place->save();
            $subcatId = $subcat->id;
        }

        // Ensure at least 5 criteria for this subcategory
        $existing = RatingCriteria::where('subcategory_id', $subcatId)->count();
        if ($existing >= 5) {
            $this->command->info("Subcategory {$subcatId} already has {$existing} criteria");
            return;
        }

        $items = [
            ['question_text' => 'How do you rate the service quality?', 'type' => 'RATING', 'is_required' => true, 'sort_order' => 1],
            ['question_text' => 'Was the staff friendly?', 'type' => 'YES_NO', 'is_required' => true, 'sort_order' => 2],
            ['question_text' => 'How clean was the place?', 'type' => 'RATING', 'is_required' => true, 'sort_order' => 3],
            ['question_text' => 'Would you recommend this place?', 'type' => 'YES_NO', 'is_required' => false, 'sort_order' => 4],
            ['question_text' => 'What was the waiting time?', 'type' => 'MULTIPLE_CHOICE', 'is_required' => true, 'sort_order' => 5],
        ];

        foreach ($items as $data) {
            $criteria = RatingCriteria::create([
                'subcategory_id' => $subcatId,
                'question_text' => $data['question_text'],
                'type' => $data['type'],
                'is_required' => $data['is_required'],
                'sort_order' => $data['sort_order'],
            ]);

            if ($data['type'] === 'MULTIPLE_CHOICE') {
                $choices = [
                    ['choice_text' => 'Less than 10 minutes', 'value' => 1, 'sort_order' => 1],
                    ['choice_text' => '10-30 minutes', 'value' => 2, 'sort_order' => 2],
                    ['choice_text' => '30-60 minutes', 'value' => 3, 'sort_order' => 3],
                    ['choice_text' => 'More than 60 minutes', 'value' => 4, 'sort_order' => 4],
                ];
                foreach ($choices as $c) {
                    RatingCriteriaChoice::create([
                        'criteria_id' => $criteria->id,
                        'choice_text' => $c['choice_text'],
                        'value' => $c['value'],
                        'sort_order' => $c['sort_order'],
                    ]);
                }
            }
        }

        $this->command->info('Ensured demo criteria for branch 1 (subcategory ' . $subcatId . ')');
    }
}
