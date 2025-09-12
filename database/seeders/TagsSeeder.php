<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TagsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tags = ['concerts','conferences','hackathons','ateliers','festivals','expositions','sport','familial','culturel','tech'];
        foreach($tags as $nom) {
            DB::table('tags')->insert(['nom' => $nom]);
        }
    }
}
