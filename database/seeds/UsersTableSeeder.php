<?php

use App\Models\User;
use Faker\Factory;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Seed UsersTable.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        User::truncate();
        Schema::enableForeignKeyConstraints();

        for ($i = 1; $i <= 10; $i++) {
            $department                    = new User();
            $department->name              = "user{$i}";
            $department->email             = "user{$i}@mail.ru";
            $department->money_balance     = 0;
            $department->bonus_balance     = 0;
            $department->email_verified_at = now();
            $department->password          = Hash::make("password{$i}");
            $department->remember_token    = Str::random(10);
            $department->save();
        }
    }
}
