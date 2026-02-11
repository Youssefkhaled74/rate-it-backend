<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class EgyptianArabicReviewsDatasetSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        try {
            DB::table('review_answer_photos')->truncate();
            DB::table('review_photos')->truncate();
            DB::table('review_answers')->truncate();
            DB::table('reviews')->truncate();
            DB::table('rating_criteria_choices')->truncate();
            DB::table('rating_criteria')->truncate();
        } finally {
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        }

        $subcategoryIds = DB::table('subcategories')->pluck('id')->all();
        if (empty($subcategoryIds)) {
            $this->command?->warn('No subcategories found. Seeder stopped.');
            return;
        }

        $criteriaBySub = $this->seedArabicCriteria($subcategoryIds, $now);

        $users = DB::table('users')->select('id')->pluck('id')->all();
        $branches = DB::table('branches')->select('id', 'brand_id')->where('is_active', 1)->get();
        $brandToSub = DB::table('brands')->pluck('subcategory_id', 'id')->all();

        if (empty($users) || $branches->isEmpty()) {
            $this->command?->warn('Users or active branches not found. Reviews not generated.');
            return;
        }

        $comments = [
            'الخدمة ممتازة والتعامل راقٍ جدًا.',
            'تجربة جيدة جدًا وسأكرر الزيارة.',
            'المكان نظيف لكن وقت الانتظار كان طويلًا.',
            'الخدمة مقبولة وتحتاج تحسين بسيط.',
            'تجربة ممتازة والأسعار مناسبة.',
            'الموظفون محترمون جدًا والتعامل سريع.',
            'الجودة عالية لكن السعر مرتفع قليلًا.',
            'لم تكن التجربة مرضية بالكامل.',
        ];

        $reviewRows = [];
        $answerRows = [];
        $reviewPhotoRows = [];
        $answerPhotoRows = [];
        $reviewId = 1;
        $answerId = 1;

        foreach ($users as $userId) {
            $count = random_int(1, 3);

            for ($i = 0; $i < $count; $i++) {
                $branch = $branches[random_int(0, $branches->count() - 1)];
                $subId = (int) ($brandToSub[$branch->brand_id] ?? 0);
                if ($subId <= 0 || empty($criteriaBySub[$subId])) {
                    $subId = (int) $subcategoryIds[array_rand($subcategoryIds)];
                }

                $criteria = $criteriaBySub[$subId] ?? [];
                if (empty($criteria)) {
                    continue;
                }

                $createdAt = Carbon::now()->subDays(random_int(0, 120))->subMinutes(random_int(0, 720));

                $ratingParts = [];
                $reviewRows[] = [
                    'id' => $reviewId,
                    'user_id' => $userId,
                    'place_id' => null,
                    'branch_id' => $branch->id,
                    'overall_rating' => 0,
                    'comment' => $comments[array_rand($comments)],
                    'status' => 'ACTIVE',
                    'is_hidden' => false,
                    'hidden_reason' => null,
                    'hidden_at' => null,
                    'hidden_by_admin_id' => null,
                    'admin_reply_text' => null,
                    'replied_at' => null,
                    'replied_by_admin_id' => null,
                    'is_featured' => (bool) random_int(0, 5) === 1,
                    'featured_at' => null,
                    'featured_by_admin_id' => null,
                    'review_score' => null,
                    'created_at' => $createdAt,
                    'updated_at' => $now,
                ];

                foreach ($criteria as $c) {
                    $ratingValue = null;
                    $yesNoValue = null;
                    $choiceId = null;
                    $textValue = null;

                    if ($c['type'] === 'RATING') {
                        $ratingValue = random_int(2, 5);
                        $ratingParts[] = $ratingValue;
                    } elseif ($c['type'] === 'YES_NO') {
                        $yesNoValue = (bool) random_int(0, 1);
                        $ratingParts[] = $yesNoValue ? 5 : 2;
                    } else {
                        $choices = $c['choices'] ?? [];
                        if (!empty($choices)) {
                            $choice = $choices[array_rand($choices)];
                            $choiceId = $choice['id'];
                            $ratingParts[] = max(1, min(5, (int) round((float) $choice['weight'])));
                        }
                    }

                    $answerRows[] = [
                        'id' => $answerId,
                        'review_id' => $reviewId,
                        'criteria_id' => $c['id'],
                        'rating_value' => $ratingValue,
                        'yes_no_value' => $yesNoValue,
                        'choice_id' => $choiceId,
                        'text_value' => $textValue,
                        'created_at' => $createdAt,
                        'updated_at' => $now,
                    ];

                    if (random_int(1, 100) <= 12) {
                        $answerPhotoRows[] = [
                            'review_answer_id' => $answerId,
                            'storage_path' => 'assets/images/Vector.png',
                            'encrypted' => false,
                            'created_at' => $createdAt,
                            'updated_at' => $now,
                        ];
                    }

                    $answerId++;
                }

                $overall = !empty($ratingParts) ? round(array_sum($ratingParts) / count($ratingParts), 1) : (random_int(2, 5));
                $reviewRows[count($reviewRows) - 1]['overall_rating'] = $overall;
                $reviewRows[count($reviewRows) - 1]['review_score'] = $overall;

                if (random_int(1, 100) <= 35) {
                    $reviewPhotoRows[] = [
                        'review_id' => $reviewId,
                        'storage_path' => 'assets/images/Vector.png',
                        'encrypted' => false,
                        'created_at' => $createdAt,
                        'updated_at' => $now,
                        'deleted_at' => null,
                    ];
                }

                $reviewId++;
            }
        }

        foreach (array_chunk($reviewRows, 500) as $chunk) {
            DB::table('reviews')->insert($chunk);
        }
        foreach (array_chunk($answerRows, 1000) as $chunk) {
            DB::table('review_answers')->insert($chunk);
        }
        foreach (array_chunk($reviewPhotoRows, 500) as $chunk) {
            DB::table('review_photos')->insert($chunk);
        }
        foreach (array_chunk($answerPhotoRows, 500) as $chunk) {
            DB::table('review_answer_photos')->insert($chunk);
        }

        $this->command?->info('EgyptianArabicReviewsDatasetSeeder completed.');
        $this->command?->info('Reviews: ' . count($reviewRows));
        $this->command?->info('Answers: ' . count($answerRows));
    }

    private function seedArabicCriteria(array $subcategoryIds, Carbon $now): array
    {
        $criteriaRows = [];
        $criteriaChoicesRows = [];
        $criteriaBySub = [];
        $criteriaId = 1;
        $choiceId = 1;

        foreach ($subcategoryIds as $subId) {
            $items = [
                [
                    'type' => 'RATING',
                    'question_ar' => 'ما تقييمك العام لجودة الخدمة؟',
                    'weight' => 5,
                    'points' => 5,
                ],
                [
                    'type' => 'YES_NO',
                    'question_ar' => 'هل تنصح الآخرين بزيارة هذا المكان؟',
                    'weight' => 3,
                    'points' => 3,
                    'yes_weight' => 5,
                    'no_weight' => 2,
                ],
                [
                    'type' => 'MULTIPLE_CHOICE',
                    'question_ar' => 'كيف كان وقت الانتظار؟',
                    'weight' => 4,
                    'points' => 4,
                    'choices' => [
                        ['text' => 'قصير جدًا', 'value' => 5, 'weight' => 5],
                        ['text' => 'متوسط', 'value' => 3, 'weight' => 3],
                        ['text' => 'طويل', 'value' => 1, 'weight' => 1],
                    ],
                ],
            ];

            $sort = 1;
            foreach ($items as $item) {
                $criteriaRows[] = [
                    'id' => $criteriaId,
                    'subcategory_id' => $subId,
                    'question_text' => $item['question_ar'],
                    'question_en' => $item['question_ar'],
                    'question_ar' => $item['question_ar'],
                    'type' => $item['type'],
                    'weight' => $item['weight'],
                    'points' => $item['points'],
                    'yes_value' => 1,
                    'no_value' => 0,
                    'yes_weight' => $item['yes_weight'] ?? 1,
                    'no_weight' => $item['no_weight'] ?? 0,
                    'is_required' => true,
                    'is_active' => true,
                    'sort_order' => $sort++,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];

                $entry = [
                    'id' => $criteriaId,
                    'type' => $item['type'],
                    'choices' => [],
                ];

                if ($item['type'] === 'MULTIPLE_CHOICE') {
                    $choiceSort = 1;
                    foreach ($item['choices'] as $choice) {
                        $criteriaChoicesRows[] = [
                            'id' => $choiceId,
                            'criteria_id' => $criteriaId,
                            'choice_text' => $choice['text'],
                            'choice_en' => $choice['text'],
                            'choice_ar' => $choice['text'],
                            'value' => $choice['value'],
                            'weight' => $choice['weight'],
                            'sort_order' => $choiceSort++,
                            'is_active' => true,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ];
                        $entry['choices'][] = ['id' => $choiceId, 'weight' => $choice['weight']];
                        $choiceId++;
                    }
                }

                $criteriaBySub[$subId][] = $entry;
                $criteriaId++;
            }
        }

        foreach (array_chunk($criteriaRows, 500) as $chunk) {
            DB::table('rating_criteria')->insert($chunk);
        }
        foreach (array_chunk($criteriaChoicesRows, 500) as $chunk) {
            DB::table('rating_criteria_choices')->insert($chunk);
        }

        return $criteriaBySub;
    }
}
