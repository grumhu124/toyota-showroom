<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(UserSeeder::class);
        // TV3 and TV5 add catalog/service seeders with explicit demo data provenance.
    }
}
