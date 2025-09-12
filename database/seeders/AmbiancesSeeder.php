<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AmbiancesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ambiances = ['festif', 'detente', 'culturel', 'professionnel', 'familial', 'spirituel'];
        foreach($ambiances as $nom) {
            DB::table('ambiances')->insert(['nom' => $nom]);
        }
    }
}
