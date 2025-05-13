<?php
namespace Database\Seeders;
use App\Models\Curriculum;
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
            CurriculumSeeder::class,
        ]);
    }
}
