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
        $banners = [
            [
                'name' => 'Homepage banner middle',
                'count' => 5
            ],
            [
                'name' => 'Homepage banner bottom',
                'count' => 5
            ]
        ];
        for ($i = 0; $i < COUNT($banners); $i++) 
            Banner::create($banners[$i]);
    }
}
