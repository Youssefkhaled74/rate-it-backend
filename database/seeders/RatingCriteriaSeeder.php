<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RatingCriteria;
use App\Models\RatingCriteriaChoice;
use App\Models\Subcategory;

class RatingCriteriaSeeder extends Seeder
{
    public function run(): void
    {
        $subcategory = Subcategory::where('name_en', 'Clinics')->first();
        if (! $subcategory) {
            $this->command->error('Clinics subcategory not found. Run DemoMedicalSubcategorySeeder first.');
            return;
        }

        $subId = $subcategory->id;

        $items = [
            ['question_text' => 'How do you rate the service quality?', 'type' => 'RATING', 'is_required' => true, 'sort_order' => 1],
            ['question_text' => 'Was the staff friendly?', 'type' => 'YES_NO', 'is_required' => true, 'sort_order' => 2],
            ['question_text' => 'How clean was the place?', 'type' => 'RATING', 'is_required' => true, 'sort_order' => 3],
            ['question_text' => 'Would you recommend this place?', 'type' => 'YES_NO', 'is_required' => false, 'sort_order' => 4],
            ['question_text' => 'What was the waiting time?', 'type' => 'MULTIPLE_CHOICE', 'is_required' => true, 'sort_order' => 5],
        ];

        foreach ($items as $data) {
            $criteria = RatingCriteria::updateOrCreate(
                ['subcategory_id' => $subId, 'question_text' => $data['question_text']],
                [
                    'type' => $data['type'],
                    'is_required' => $data['is_required'],
                    'sort_order' => $data['sort_order'],
                ]
            );

            // If multiple choice, seed choices
            if ($data['type'] === 'MULTIPLE_CHOICE') {
                $choices = [
                    ['choice_text' => 'Less than 10 minutes', 'value' => 1, 'sort_order' => 1],
                    ['choice_text' => '10-30 minutes', 'value' => 2, 'sort_order' => 2],
                    ['choice_text' => '30-60 minutes', 'value' => 3, 'sort_order' => 3],
                    ['choice_text' => 'More than 60 minutes', 'value' => 4, 'sort_order' => 4],
                ];

                foreach ($choices as $c) {
                    RatingCriteriaChoice::updateOrCreate(
                        ['criteria_id' => $criteria->id, 'choice_text' => $c['choice_text']],
                        ['value' => $c['value'], 'sort_order' => $c['sort_order']]
                    );
                }
            }
        }

        $this->command->info('Rating criteria seeded for Clinics subcategory id=' . $subId);
    }
}
