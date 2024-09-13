<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\UserSeeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // way1 (using factory): User::factory(10)->create();

        // way2 directly (using create):

        User::create([
            'first_name' => "admin",
            'last_name' => "admin",
            'phone' => "0987070814",
            'type' => "admin",
            'register_accepted' => true,
            'email' => "a@gmail.com",
            'password' => Hash::make('password'),
        ]);

        // way3 :
        $this->call(class: UserSeeder::class);

        //  way 4 : using Specific & Multiple seeders

        /* $this->call([
            Seeder1::class,
            Seeder2::class,
        ]); */
    }
}
