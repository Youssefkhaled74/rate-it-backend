<?php

namespace Database\Seeders;

use App\Models\Review;
use App\Models\ReviewAnswer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DemoReviewsSeeder extends Seeder
{
    public function run()
    {
        $count = config('seeding.demo.reviews', 800);
        $users = DB::table('users')->pluck('id')->toArray();
        $branches = DB::table('branches')->pluck('id')->toArray();
        $criteria = DB::table('rating_criteria')->get();

        for ($i = 0; $i < $count; $i++) {
            $user_id = $users[array_rand($users)];
            $branch_id = $branches[array_rand($branches)];
            $place_id = DB::table('branches')->where('id',$branch_id)->value('place_id');

            $reviewId = DB::table('reviews')->insertGetId([
                'user_id' => $user_id,
                'place_id' => $place_id,
                'branch_id' => $branch_id,
                'overall_rating' => $overall,
                'comment' => (mt_rand(0,1) ? 'Demo review comment' : null),
                'status' => 'ACTIVE',
                'review_score' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $overall = round(mt_rand(10,50)/10,1);

            // answers for criteria in this place's subcategory set (approx)
            $sampleCriteria = $criteria->random(min(5, $criteria->count()));
            $sum = 0; $cnt = 0;
            foreach ($sampleCriteria as $c) {
                // create answer row
                $rating = null; $yesno = null; $choice = null;
                if ($c->type === 'RATING') { $rating = mt_rand(1,5); $sum += $rating; $cnt++; }
                if ($c->type === 'YES_NO') { $yesno = (bool) mt_rand(0,1); }
                if ($c->type === 'MULTIPLE_CHOICE') {
                    $choiceRow = DB::table('rating_criteria_choices')->where('criteria_id', $c->id)->inRandomOrder()->first();
                    if ($choiceRow) { $choice = $choiceRow->id; $sum += ($choiceRow->value ?? 0); $cnt++; }
                }

                DB::table('review_answers')->insert([
                    'review_id' => $reviewId,
                    'criteria_id' => $c->id,
                    'rating_value' => $rating,
                    'yes_no_value' => $yesno,
                    'choice_id' => $choice,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            if ($cnt > 0) {
                $avg = round($sum / $cnt, 2);
                DB::table('reviews')->where('id', $reviewId)->update(['review_score' => $avg]);
            }
        }
    }
}
