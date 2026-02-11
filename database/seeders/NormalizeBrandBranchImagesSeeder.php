<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NormalizeBrandBranchImagesSeeder extends Seeder
{
    public function run(): void
    {
        // Use a public image path so logos/covers render in the UI.
        // NOTE: A path under .git/objects is not publicly accessible.
        $imagePath = 'assets/images/Vector.png';

        DB::table('brands')->update([
            'logo' => $imagePath,
            'cover_image' => $imagePath,
            'logo_url' => $imagePath,
            'updated_at' => now(),
        ]);

        DB::table('branches')->update([
            'logo' => $imagePath,
            'cover_image' => $imagePath,
            'updated_at' => now(),
        ]);
    }
}

