<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\GradingPeriod;

class GradingPeriodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $periods = [
            [
                'name' => '1st Quarter',
                'start_date' => '2026-06-01',
                'end_date' => '2026-08-31',
                'is_active' => true,
            ],
            [
                'name' => '2nd Quarter',
                'start_date' => '2026-09-01',
                'end_date' => '2026-11-30',
                'is_active' => true,
            ],
            [
                'name' => '3rd Quarter',
                'start_date' => '2026-12-01',
                'end_date' => '2027-02-28',
                'is_active' => true,
            ],
            [
                'name' => '4th Quarter',
                'start_date' => '2027-03-01',
                'end_date' => '2027-05-31',
                'is_active' => true,
            ],
        ];

        foreach ($periods as $period) {
            GradingPeriod::updateOrCreate(
                ['name' => $period['name']],
                $period
            );
        }
    }
}
