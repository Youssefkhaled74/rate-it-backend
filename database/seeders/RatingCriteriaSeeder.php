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
        // Beauty/Salon Criteria
        $beauty = Subcategory::where('name_en', 'Beauty')->first();
        if ($beauty) {
            $this->seedBeautyCriteria($beauty->id);
        }

        // Automotive Criteria
        $automotive = Subcategory::where('name_en', 'Automotive')->first();
        if ($automotive) {
            $this->seedAutomotiveCriteria($automotive->id);
        }

        // Clinics Criteria
        $clinics = Subcategory::where('name_en', 'Clinics')->first();
        if ($clinics) {
            $this->seedClinicsCriteria($clinics->id);
        }

        $this->command->info('Rating criteria seeded successfully!');
    }

    private function seedBeautyCriteria($subcategoryId)
    {
        $items = [
            [
                'question_text' => 'Cleanliness of the salon',
                'question_en' => 'Cleanliness of the salon',
                'question_ar' => 'نظافة الصالون',
                'type' => 'RATING',
                'is_required' => true,
                'sort_order' => 0
            ],
            [
                'question_text' => 'Staff professionalism',
                'question_en' => 'Staff professionalism',
                'question_ar' => 'احترافية الموظفين',
                'type' => 'RATING',
                'is_required' => true,
                'sort_order' => 1
            ],
            [
                'question_text' => 'Quality of service',
                'question_en' => 'Quality of service',
                'question_ar' => 'جودة الخدمة',
                'type' => 'RATING',
                'is_required' => true,
                'sort_order' => 2
            ],
            [
                'question_text' => 'Was the staff friendly?',
                'question_en' => 'Was the staff friendly?',
                'question_ar' => 'هل كان الموظفون ودودين؟',
                'type' => 'YES_NO',
                'is_required' => false,
                'sort_order' => 3
            ],
            [
                'question_text' => 'Would you recommend this salon?',
                'question_en' => 'Would you recommend this salon?',
                'question_ar' => 'هل ستوصي بهذا الصالون؟',
                'type' => 'YES_NO',
                'is_required' => false,
                'sort_order' => 4
            ],
        ];

        foreach ($items as $data) {
            $this->createCriteria($subcategoryId, $data);
        }
    }

    private function seedAutomotiveCriteria($subcategoryId)
    {
        $items = [
            [
                'question_text' => 'Mechanical condition',
                'question_en' => 'Mechanical condition',
                'question_ar' => 'الحالة الميكانيكية',
                'type' => 'RATING',
                'is_required' => true,
                'sort_order' => 0
            ],
            [
                'question_text' => 'Interior cleanliness',
                'question_en' => 'Interior cleanliness',
                'question_ar' => 'نظافة الداخل',
                'type' => 'RATING',
                'is_required' => true,
                'sort_order' => 1
            ],
            [
                'question_text' => 'Exterior condition',
                'question_en' => 'Exterior condition',
                'question_ar' => 'حالة الخارج',
                'type' => 'RATING',
                'is_required' => true,
                'sort_order' => 2
            ],
            [
                'question_text' => 'Service quality',
                'question_en' => 'Service quality',
                'question_ar' => 'جودة الخدمة',
                'type' => 'RATING',
                'is_required' => false,
                'sort_order' => 3
            ],
            [
                'question_text' => 'Type of service issue',
                'question_en' => 'Type of service issue',
                'question_ar' => 'نوع مشكلة الخدمة',
                'type' => 'MULTIPLE_CHOICE',
                'is_required' => false,
                'sort_order' => 4,
                'choices' => [
                    ['choice_text' => 'Engine Problems', 'choice_en' => 'Engine Problems', 'choice_ar' => 'مشاكل المحرك', 'value' => 1, 'sort_order' => 0],
                    ['choice_text' => 'Transmission Issues', 'choice_en' => 'Transmission Issues', 'choice_ar' => 'مشاكل الناقل', 'value' => 2, 'sort_order' => 1],
                    ['choice_text' => 'Electrical Problems', 'choice_en' => 'Electrical Problems', 'choice_ar' => 'مشاكل كهربائية', 'value' => 3, 'sort_order' => 2],
                    ['choice_text' => 'Body Damage', 'choice_en' => 'Body Damage', 'choice_ar' => 'تلف الهيكل', 'value' => 4, 'sort_order' => 3],
                    ['choice_text' => 'Interior Issues', 'choice_en' => 'Interior Issues', 'choice_ar' => 'مشاكل داخلية', 'value' => 5, 'sort_order' => 4],
                ]
            ],
        ];

        foreach ($items as $data) {
            $this->createCriteria($subcategoryId, $data);
        }
    }

    private function seedClinicsCriteria($subcategoryId)
    {
        $items = [
            [
                'question_text' => 'Doctor professionalism',
                'question_en' => 'Doctor professionalism',
                'question_ar' => 'احترافية الطبيب',
                'type' => 'RATING',
                'is_required' => true,
                'sort_order' => 0
            ],
            [
                'question_text' => 'Clinic cleanliness',
                'question_en' => 'Clinic cleanliness',
                'question_ar' => 'نظافة العيادة',
                'type' => 'RATING',
                'is_required' => true,
                'sort_order' => 1
            ],
            [
                'question_text' => 'Service quality',
                'question_en' => 'Service quality',
                'question_ar' => 'جودة الخدمة',
                'type' => 'RATING',
                'is_required' => true,
                'sort_order' => 2
            ],
            [
                'question_text' => 'Was the wait time reasonable?',
                'question_en' => 'Was the wait time reasonable?',
                'question_ar' => 'هل كان وقت الانتظار معقولاً؟',
                'type' => 'YES_NO',
                'is_required' => false,
                'sort_order' => 3
            ],
            [
                'question_text' => 'Staff friendliness',
                'question_en' => 'Staff friendliness',
                'question_ar' => 'ودية الموظفين',
                'type' => 'RATING',
                'is_required' => false,
                'sort_order' => 4
            ],
            [
                'question_text' => 'Waiting time duration',
                'question_en' => 'Waiting time duration',
                'question_ar' => 'مدة وقت الانتظار',
                'type' => 'MULTIPLE_CHOICE',
                'is_required' => false,
                'sort_order' => 5,
                'choices' => [
                    ['choice_text' => 'Less than 10 minutes', 'choice_en' => 'Less than 10 minutes', 'choice_ar' => 'أقل من 10 دقائق', 'value' => 1, 'sort_order' => 0],
                    ['choice_text' => '10-30 minutes', 'choice_en' => '10-30 minutes', 'choice_ar' => '10-30 دقيقة', 'value' => 2, 'sort_order' => 1],
                    ['choice_text' => '30-60 minutes', 'choice_en' => '30-60 minutes', 'choice_ar' => '30-60 دقيقة', 'value' => 3, 'sort_order' => 2],
                    ['choice_text' => 'More than 60 minutes', 'choice_en' => 'More than 60 minutes', 'choice_ar' => 'أكثر من 60 دقيقة', 'value' => 4, 'sort_order' => 3],
                ]
            ],
        ];

        foreach ($items as $data) {
            $this->createCriteria($subcategoryId, $data);
        }
    }

    private function createCriteria($subcategoryId, $data)
    {
        $choices = $data['choices'] ?? [];
        unset($data['choices']);

        $data['subcategory_id'] = $subcategoryId;
        $data['is_active'] = true;

        $criteria = RatingCriteria::updateOrCreate(
            ['subcategory_id' => $subcategoryId, 'question_text' => $data['question_text']],
            $data
        );

        // Create choices for multiple choice type
        if ($criteria->type === 'MULTIPLE_CHOICE' && !empty($choices)) {
            foreach ($choices as $choice) {
                RatingCriteriaChoice::updateOrCreate(
                    ['criteria_id' => $criteria->id, 'choice_text' => $choice['choice_text']],
                    [
                        'value' => $choice['value'] ?? null,
                        'sort_order' => $choice['sort_order'] ?? 0,
                        'choice_en' => $choice['choice_en'] ?? $choice['choice_text'],
                        'choice_ar' => $choice['choice_ar'] ?? null,
                        'is_active' => true,
                    ]
                );
            }
        }
    }
}
