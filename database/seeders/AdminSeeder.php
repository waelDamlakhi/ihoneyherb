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
                'Password' => '$2y$10$7IY9dtxr7PHX3B8wdE8KM.jfDdNegG17baeGGObqCB7c2bsrl6fGa',
                'email' => 'admin@ihoneyherb.ae'
            ]
        );
    }
}
