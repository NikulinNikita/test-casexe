<?php

use App\Models\GiftType;
use Faker\Factory;
use Illuminate\Database\Seeder;

class GiftTypesTableSeeder extends Seeder
{
    /**
     * Seed DepartmentsTable.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        GiftType::truncate();
        Schema::enableForeignKeyConstraints();

        $faker = Factory::create();
        $types = config('selectOptions.gift_types.types');

        foreach ($types as $type) {
            $giftType              = new GiftType();
            $giftType->name        = $type;
            $giftType->description = $faker->realText(rand(20, 60));
            $giftType->status      = 'active';
            $giftType->save();
        }
    }
}
