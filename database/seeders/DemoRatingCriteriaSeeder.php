<?php

namespace Database\Seeders;

use App\Models\RatingCriteria;
use App\Models\RatingCriteriaChoice;
use App\Models\Subcategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DemoRatingCriteriaSeeder extends Seeder
{
    public function run()
    {
        $subcategories = DB::table('subcategories')->get();

        foreach ($subcategories as $sub) {
            for ($i = 0; $i < 5; $i++) {
                $type = ['RATING','YES_NO','MULTIPLE_CHOICE'][array_rand([0,1,2])];
                $criteriaId = DB::table('rating_criteria')->insertGetId([
                    'subcategory_id' => $sub->id,
                    'question_text' => 'Question ' . ($i+1) . ' for ' . $sub->name_en,
                    'type' => $type,
                    'is_required' => $i < 3,
                    'sort_order' => $i,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                if ($type === 'MULTIPLE_CHOICE') {
                    for ($c = 0; $c < 4; $c++) {
                        DB::table('rating_criteria_choices')->insert([
                            'criteria_id' => $criteriaId,
                            'choice_text' => 'Choice ' . ($c+1),
                            'value' => $c+1,
                            'sort_order' => $c,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }
        }
    }
}
