<?php

namespace Database\Seeders;

use App\Models\Banner;
use Illuminate\Database\Seeder;

class BannerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i = 1; $i <= 2; $i++) 
            Banner::create(
                [
                    'name' => 'banner ' . $i,
                    'count' => 5
                ]
            );
    }
}
