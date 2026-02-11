<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Department::firstOrCreate(['name' => 'IT']);
        \App\Models\Department::firstOrCreate(['name' => 'HRD']);
        \App\Models\Department::firstOrCreate(['name' => 'IT Project']);
        \App\Models\Department::firstOrCreate(['name' => 'GA']);
        \App\Models\Department::firstOrCreate(['name' => 'WMM']);
        \App\Models\Department::firstOrCreate(['name' => 'Sipil']);
        \App\Models\Department::firstOrCreate(['name' => 'ADM-PM']);
        \App\Models\Department::firstOrCreate(['name' => 'ADM-Produksi 5A']);
        \App\Models\Department::firstOrCreate(['name' => 'ADM-Produksi 5B']);
        \App\Models\Department::firstOrCreate(['name' => 'ADM-Produksi 5C']);
        \App\Models\Department::firstOrCreate(['name' => 'ADM MTC']);
        \App\Models\Department::firstOrCreate(['name' => 'MTC']);
        \App\Models\Department::firstOrCreate(['name' => 'ADM QC']);
        \App\Models\Department::firstOrCreate(['name' => 'QC']);
        \App\Models\Department::firstOrCreate(['name' => 'ADM QA']);
        \App\Models\Department::firstOrCreate(['name' => 'QA']);
        \App\Models\Department::firstOrCreate(['name' => 'ADM Procurement GBB']);
        \App\Models\Department::firstOrCreate(['name' => 'ADM Procurement Sparepart']);
        \App\Models\Department::firstOrCreate(['name' => 'Procurement']);
        \App\Models\Department::firstOrCreate(['name' => 'Sortir-Packing 5A']);
        \App\Models\Department::firstOrCreate(['name' => 'Sortir-Packing 5B']);
        \App\Models\Department::firstOrCreate(['name' => 'Sortir-Packing 5C']);
        \App\Models\Department::firstOrCreate(['name' => 'Kiln-5A']);
        \App\Models\Department::firstOrCreate(['name' => 'Kiln 5B']);
        \App\Models\Department::firstOrCreate(['name' => 'Kiln 5C']);
        \App\Models\Department::firstOrCreate(['name' => 'Glazing Line 5A']);
        \App\Models\Department::firstOrCreate(['name' => 'Glazing Line 5B']);
        \App\Models\Department::firstOrCreate(['name' => 'Glazing Line 5C']);
        \App\Models\Department::firstOrCreate(['name' => 'GSP']);
        \App\Models\Department::firstOrCreate(['name' => 'ADM Gsp']);
        \App\Models\Department::firstOrCreate(['name' => 'PPIC']);
        \App\Models\Department::firstOrCreate(['name' => 'ADM GBJ']);
        \App\Models\Department::firstOrCreate(['name' => 'GBJ']);
        \App\Models\Department::firstOrCreate(['name' => 'ADM GBB']);
        \App\Models\Department::firstOrCreate(['name' => 'GBB']);
        \App\Models\Department::firstOrCreate(['name' => 'Press 5A']);
        \App\Models\Department::firstOrCreate(['name' => 'Press 5B']);
        \App\Models\Department::firstOrCreate(['name' => 'Press 5C']);
        \App\Models\Department::firstOrCreate(['name' => 'PGK GBJ']);
        \App\Models\Department::firstOrCreate(['name' => 'PGK']);
        

        
    }
}
