<?php

use App\Models\PrizeType;
use Faker\Factory;
use Illuminate\Database\Seeder;

class PrizeTypesTableSeeder extends Seeder
{
    /**
     * Seed DepartmentsTable.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        PrizeType::truncate();
        Schema::enableForeignKeyConstraints();

        $faker = Factory::create();
        $types = config('selectOptions.prize_types.types');

        foreach ($types as $type) {
            $prizeType              = new PrizeType();
            $prizeType->name        = $type;
            $prizeType->description = $faker->realText(rand(20, 60));
            $prizeType->status      = 'active';
            $prizeType->save();
        }
    }
}
