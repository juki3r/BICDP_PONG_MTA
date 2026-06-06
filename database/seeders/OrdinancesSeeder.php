<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OrdinancesSeeder extends Seeder
{
    public function run(): void
    {
        $year = date('Y');
        $count = 1;

        $makeNumber = function () use (&$year, &$count) {
            return 'ORD-' . $year . '-' . str_pad($count++, 4, '0', STR_PAD_LEFT);
        };

        $barangays = [
            'Bayas',
            'Bayuyan',
            'Botongon',
            'Bulaqueña',
            'Calapdan',
            'Cano-an',
            'Daan Banua',
            'Daculan',
            'Gogo',
            'Jolog',
            'Loguingot',
            'Lonoy',
            'Lumbia',
            'Malbog',
            'Manipulon',
            'Pa-on',
            'Poblacion Zone I',
            'Poblacion Zone II',
            'Poblacion Zone III',
            'San Roque',
            'Santa Ana',
            'Tabu-an',
            'Tacbuyan',
            'Tanza',
            'Villa Pani-an'
        ];

        $templates = [
            [
                'title' => 'Proper Waste Segregation Ordinance',
                'description' => 'All households are required to segregate biodegradable, recyclable, and residual waste.',
                'category' => 'Environment',
                'penalties' => '₱500 fine and community service for violations',
            ],
            [
                'title' => 'Anti-Littering Ordinance',
                'description' => 'Littering in streets, canals, and public areas is strictly prohibited.',
                'category' => 'Environment',
                'penalties' => '₱300 fine per offense',
            ],
            [
                'title' => 'Curfew Ordinance for Minors',
                'description' => 'Minors are prohibited from loitering outside after 10:00 PM without guardian.',
                'category' => 'Safety',
                'penalties' => 'Warning for first offense, sanctions for guardians',
            ],
            [
                'title' => 'Anti-Loitering Ordinance',
                'description' => 'Loitering in dark or restricted areas is prohibited for safety reasons.',
                'category' => 'Safety',
                'penalties' => 'Warning or community service',
            ],
            [
                'title' => 'Noise Control Ordinance',
                'description' => 'Excessive noise after 9:00 PM is prohibited in residential areas.',
                'category' => 'Public Order',
                'penalties' => '₱300 fine or confiscation of sound system',
            ],
            [
                'title' => 'Videoke Time Regulation',
                'description' => 'Videoke use allowed only until 10:00 PM.',
                'category' => 'Public Order',
                'penalties' => 'Confiscation of equipment for repeat violations',
            ],
            [
                'title' => 'Anti-Smoking Ordinance',
                'description' => 'Smoking is prohibited in public places including streets and parks.',
                'category' => 'Health',
                'penalties' => '₱500 fine per violation',
            ],
            [
                'title' => 'Anti-Dengue Cleanup Ordinance',
                'description' => 'Mandatory weekly cleaning of surroundings to prevent dengue breeding sites.',
                'category' => 'Health',
                'penalties' => '₱200 fine or community cleanup duty',
            ],
            [
                'title' => 'Stray Animals Control Ordinance',
                'description' => 'Pet owners must secure their animals; stray animals will be impounded.',
                'category' => 'Animal Control',
                'penalties' => 'Impound fee + penalties for negligence',
            ],
            [
                'title' => 'No Parking in Narrow Streets',
                'description' => 'Parking in designated narrow roads and intersections is prohibited.',
                'category' => 'Traffic',
                'penalties' => '₱300 fine or towing coordination',
            ],
        ];

        $records = [];

        foreach ($barangays as $barangay) {
            foreach ($templates as $template) {
                $records[] = [
                    'barangay' => $barangay,
                    'ordinance_number' => $makeNumber(),
                    'title' => $template['title'],
                    'description' => $template['description'],
                    'category' => $template['category'],
                    'status' => 'active',
                    'effectivity_date' => now(),
                    'approved_date' => now()->subMonth(),
                    'penalties' => $template['penalties'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        DB::table('ordinances')->insert($records);
    }
}
