<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            ZoneSeeder::class,
            PositionSeeder::class,
            ChairmanshipSeeder::class,
            BarangayInformationSeeder::class,
            CertificationSeeder::class,
            UserSeeder::class,
        ]);
    }
}
