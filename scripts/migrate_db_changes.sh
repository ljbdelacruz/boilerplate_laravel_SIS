


#!/bin/bash

# Fresh migration with seeders
php artisan migrate:fresh --seed

# Run specific seeders if needed
php artisan db:seed --class=UserSeeder
php artisan db:seed --class=SchoolYearSeeder

echo "Database migration and seeding completed successfully!"