<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            SchoolYearSeeder::class,
            CourseSeeder::class,
            SectionSeeder::class,
            GradeLevelSeeder::class,
        ]);
    }
}
