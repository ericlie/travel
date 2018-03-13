<?php

use App\Hotel;
use Illuminate\Database\Seeder;

class HotelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i=0; $i < 1; $i++) {
            $this->command->info('for i = '. $i);
            factory(Hotel::class, 500)->create();
        }
    }
}
