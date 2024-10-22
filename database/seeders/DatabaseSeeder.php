<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Category;
use App\Models\MembershipPlan;
use App\Models\Payment;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Database\Seeder;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        MembershipPlan::factory(1)->create();
        MembershipPlan::factory()->create([
            'name' => 'pro',
            'max_books_allowed' => 1000,
            'borrow_duration_days' => 365,
            'price' => 1000000
        ]);

        User::factory()->create([
            'firstname' => 'alireza',
            'lastname' => 'ezati',
            'username' => 'ownen',
            'email' => 'alireza.ezzatiy@gmail.com',
            'role' => 'admin',
            'membership_plan_id' => 2,
//            'membership_plan_id' => MembershipPlan::all()->random()->id,
            'password' => 'amirreza1',
        ]);

        User::factory(10)->create();

        Category::factory(1)->create();

        Book::factory(90)->create();

        Payment::factory(8)->create();

        Reservation::factory(100)->create();
    }
}
