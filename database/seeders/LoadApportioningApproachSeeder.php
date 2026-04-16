<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LoadApportioningApproach;

class LoadApportioningApproachSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            'Equipment Types',
            'Building / Blocks',
            'Process Plants',
            'Department',
            'Others',
        ];

        foreach ($defaults as $name) {
            LoadApportioningApproach::updateOrCreate(
                ['name' => $name],
                ['is_default' => true]
            );
        }
    }
}
