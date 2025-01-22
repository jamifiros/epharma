<?php

namespace Database\Seeders;

use App\Models\Prescription;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\UserDetails;

class DataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        UserDetails::create([
            'userid' => 2, // Assuming this corresponds to the user's ID
            'guardian_name' => 'test Guardian',
            'guardian_email' => 'jamifirosk@gmail.com',
            'idproof' => 'assets/idproofs/id1.png',
            'place' => 'test place',
            'district' => 'test District',
            'mobile_no' => '1234567890',
        ]);



        // User 5 details
        Prescription::create([
            'userid' => 2, // Assuming this corresponds to the user's ID
            'image' => 'assets/prescriptions/p1.jpg',
        ]);
        
    }
}
