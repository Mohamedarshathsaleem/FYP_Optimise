<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\EnergyType;

class EnergyTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $energyTypes = [
            ['Hard Coal', '29.3076 GJ/tonne'],
            ['Coke/oven coke', '26.3768 GJ/tonne'],
            ['Gas coke', '26.3768 GJ/tonne'],
            ['Brown coal coke', '19.6361 GJ/tonne'],
            // ... tambahkan data lainnya
        ];

        foreach ($energyTypes as $energy) {
            EnergyType::updateOrCreate(
                ['name' => $energy[0]],
                ['conversion_coefficient' => $energy[1]]
            );
        }
    }
}
