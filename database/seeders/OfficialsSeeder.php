<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use Carbon\Carbon;

class OfficialsSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        // $barangays = [
        //     'Bayas',
        //     'Bayuyan',
        //     'Botongon',
        //     'Bulaqueña',
        //     'Calapdan',
        //     'Cano-an',
        //     'Daan Banua',
        //     'Daculan',
        //     'Gogo',
        //     'Jolog',
        //     'Loguingot',
        //     'Lonoy',
        //     'Lumbia',
        //     'Malbog',
        //     'Manipulon',
        //     'Pa-on',
        //     'Poblacion Zone I',
        //     'Poblacion Zone II',
        //     'Poblacion Zone III',
        //     'San Roque',
        //     'Santa Ana',
        //     'Tabu-an',
        //     'Tacbuyan',
        //     'Tanza',
        //     'Villa Pani-an'
        // ];

        $barangays = [
            'Abong',
            'Alipata',
            'Asluman',
            'Bancal',
            'Barangcalan',
            'Barosbos',
            'Punta Batuanan',
            'Binuluangan',
            'Bito-on',
            'Bolo',
            'Buaya',
            'Buenavista',
            'Isla De Cana',
            'Cabilao Grande',
            'Cabilao Pequeño',
            'Cabuguana',
            'Cawayan',
            'Dayhagan',
            'Gabi',
            'Granada',
            'Guinticgan',
            'Lantangan',
            'Manlot',
            'Nalumsan',
            'Pantalan',
            'Poblacion',
            'Punta',
            'San Fernando',
            'Tabugon',
            'Talingting',
            'Tarong',
            'Tinigban',
            'Tupaz'
        ];

        $committees = [
            'Peace and Order',
            'Health',
            'Education',
            'Infrastructure',
            'Environment',
            'Budget & Finance',
            'Social Services',
            'Tourism / Sports'
        ];

        $positions = [
            'Barangay Captain',
            'Barangay Secretary',
            'Barangay Treasurer',
            'Kagawad',
            'Kagawad',
            'Kagawad',
            'Kagawad',
            'Kagawad',
            'Kagawad',
            'Kagawad',
            'Kagawad',
            'SK Chairperson'
        ];

        $officials = [];

        foreach ($barangays as $barangay) {

            foreach ($positions as $index => $position) {

                $officials[] = [
                    'barangay' => $barangay,
                    'full_name' => $faker->name,
                    'gender' => $faker->randomElement(['Male', 'Female']),
                    'position' => $position,

                    'committee' => match ($position) {
                        'SK Chairperson' => 'Youth Development',
                        'Kagawad' => $committees[$index - 3] ?? null,
                        default => null
                    },

                    'address' => "{$barangay}, Carles, Iloilo",
                    'contact_number' => '09' . $faker->numerify('#########'),
                    'email' => $faker->unique()->safeEmail(),

                    'term_start' => Carbon::parse('2023-01-01'),
                    'term_end' => Carbon::parse('2026-12-31'),

                    'status' => 'active',
                    'photo' => null,
                    'remarks' => null,

                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        DB::table('officials')->insert($officials);
    }
}
