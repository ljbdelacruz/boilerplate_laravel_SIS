## Capstone 2 Laravel
> 

## Setup DB Docker Locally
> run this everytime you update your swagger docs
```
docker-compose up -d 
```

## Clean Routes
```
php artisan route:clear && php artisan route:cache && php artisan optimize
# checks if your routes are in the list
php artisan route:list 
```

## Migrate DB Changes to your Docker
```
php artisan cache:table && php artisan migrate
```

## Running Application

```
# if you have api changes run this to update swagger docs
php artisan l5-swagger:generate

# else serve and run the application
php artisan serve

# how to access your swagger docs endpoint
http://127.0.0.1:8000/api/documentation
```
