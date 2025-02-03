<?php

namespace Database\Seeders;

use Database\Factories\ApplicationFactory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ApplicationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('applications')->insert(ApplicationFactory::new()->make()->toArray());
    }
}
