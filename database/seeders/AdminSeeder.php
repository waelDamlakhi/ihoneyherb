<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Admin::create(
            [
                'name' => 'Administrator',
                'userName' => 'Admin',
                'Password' => '$2y$10$AAmrvNHhMRIo3ED2pNYTHu2icHfyYxyPE/OJf18zQNH...',
                'email' => 'admin@ihoneyherb.ae'
            ]
        );
    }
}
