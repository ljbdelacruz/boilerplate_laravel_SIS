#!/bin/bash
# build and run the containers
docker-compose up -d --build
# generate application key inside the container
docker-compose exec app php artisan key:generate
# run migrations
docker-compose exec app php artisan migrate

