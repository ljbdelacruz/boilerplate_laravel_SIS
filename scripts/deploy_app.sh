#!/bin/bash

# build and run the containers
docker-compose up -d --build

# Wait for MySQL to be ready
echo "Waiting for MySQL to be ready..."

# generate application key inside the container
docker-compose exec app php artisan key:generate

# run migrations and seeders
docker-compose exec app php artisan migrate:fresh --seed

