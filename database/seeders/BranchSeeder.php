<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $branch = DB::table('branches')
            ->where('branch_code', 'MAIN001')
            ->first();

        if (!$branch) {
            $branchId = DB::table('branches')->insertGetId([
                'name' => 'Main Branch',
                'branch_code' => 'MAIN001',
                'address' => 'Head Office',
                'city_id' => null,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $branchId = $branch->id;
        }

        DB::table('admins')->update([
            'branch_id' => $branchId
        ]);
    }
}
