<?php

namespace Database\Seeders;

use App\Models\ShoppingCart;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::factory(15)->create();
        $users->each(fn($user) => ShoppingCart::factory()->create(['user_id' => $user->id]));
    }
}
