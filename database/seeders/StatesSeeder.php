<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StatesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         $states = [
            ['name' => 'Andhra Pradesh', 'country' => 'India', 'status' => 1],
            ['name' => 'Arunachal Pradesh', 'country' => 'India', 'status' => 1],
            ['name' => 'Assam', 'country' => 'India', 'status' => 1],
            ['name' => 'Bihar', 'country' => 'India', 'status' => 1],
            ['name' => 'Chhattisgarh', 'country' => 'India', 'status' => 1],
            ['name' => 'Goa', 'country' => 'India', 'status' => 1],
            ['name' => 'Gujarat', 'country' => 'India', 'status' => 1],
            ['name' => 'Haryana', 'country' => 'India', 'status' => 1],
            ['name' => 'Himachal Pradesh', 'country' => 'India', 'status' => 1],
            ['name' => 'Jharkhand', 'country' => 'India', 'status' => 1],
            ['name' => 'Karnataka', 'country' => 'India', 'status' => 1],
            ['name' => 'Kerala', 'country' => 'India', 'status' => 1],
            ['name' => 'Madhya Pradesh', 'country' => 'India', 'status' => 1],
            ['name' => 'Maharashtra', 'country' => 'India', 'status' => 1],
            ['name' => 'Manipur', 'country' => 'India', 'status' => 1],
            ['name' => 'Meghalaya', 'country' => 'India', 'status' => 1],
            ['name' => 'Mizoram', 'country' => 'India', 'status' => 1],
            ['name' => 'Nagaland', 'country' => 'India', 'status' => 1],
            ['name' => 'Odisha', 'country' => 'India', 'status' => 1],
            ['name' => 'Punjab', 'country' => 'India', 'status' => 1],
            ['name' => 'Rajasthan', 'country' => 'India', 'status' => 1],
            ['name' => 'Sikkim', 'country' => 'India', 'status' => 1],
            ['name' => 'Tamil Nadu', 'country' => 'India', 'status' => 1],
            ['name' => 'Telangana', 'country' => 'India', 'status' => 1],
            ['name' => 'Tripura', 'country' => 'India', 'status' => 1],
            ['name' => 'Uttar Pradesh', 'country' => 'India', 'status' => 1],
            ['name' => 'Uttarakhand', 'country' => 'India', 'status' => 1],
            ['name' => 'West Bengal', 'country' => 'India', 'status' => 1],
        ];

        foreach ($states as $state) {

            DB::table('states')->updateOrInsert(
                [
                    'name' => trim($state['name'])
                ],
                [
                    'country'    => $state['country'],
                    'status'     => $state['status'],
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }
}
