<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MonthlyVariable;
use App\Models\MonthlyVariableUsage;

class MonthlyVariableUsageSeeder extends Seeder
{
    public function run(): void
    {
        // Create variable types
        $variableTypes = [
            'Ambient Temperature',
            'Humidity',
        ];

        foreach ($variableTypes as $type) {
            MonthlyVariable::firstOrCreate(['variable_name' => $type]);
        }

        $variables = MonthlyVariable::all();
        $years = range(2022, 2025);

        // Monthly average temperatures for Malaysia (tropical climate, °C)
        $tempByMonth = [
            1 => 27.0, 2 => 27.5, 3 => 28.0, 4 => 28.5,
            5 => 28.8, 6 => 28.5, 7 => 28.2, 8 => 28.0,
            9 => 27.8, 10 => 27.5, 11 => 27.2, 12 => 27.0,
        ];

        // Monthly average humidity (%)
        $humidityByMonth = [
            1 => 82.0, 2 => 80.0, 3 => 79.0, 4 => 80.0,
            5 => 81.0, 6 => 80.0, 7 => 79.5, 8 => 80.0,
            9 => 81.0, 10 => 82.0, 11 => 83.0, 12 => 83.5,
        ];

        // Slight warming trend year-over-year
        $yearTempOffset = [2022 => 0.0, 2023 => 0.1, 2024 => 0.2, 2025 => 0.3];

        foreach ($years as $year) {
            foreach (range(1, 12) as $month) {
                $monthKey = $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT);

                foreach ($variables as $var) {
                    if (str_contains($var->variable_name, 'Temperature')) {
                        $base = $tempByMonth[$month] + ($yearTempOffset[$year] ?? 0);
                        $noise = mt_rand(-50, 50) / 100; // +/- 0.5°C
                        $value = round($base + $noise, 3);
                        $unit = '°C';
                    } else {
                        // Humidity
                        $base = $humidityByMonth[$month];
                        $noise = mt_rand(-200, 200) / 100; // +/- 2%
                        $value = round($base + $noise, 3);
                        $unit = '%';
                    }

                    MonthlyVariableUsage::updateOrCreate(
                        ['monthly_variable_id' => $var->id, 'month' => $monthKey],
                        [
                            'variable_value' => $value,
                            'variable_unit' => $unit,
                        ]
                    );
                }
            }
        }
    }
}
