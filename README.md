Open composer.json and add following line

"autoload": {

        ....
        
        "psr-4": {
        
            ....
            
            "Sementechs\\Notification\\": "vendor/semantechs/notification/src/"
            
        }
        
    },
    

Open app.php in config folder and add following line


'providers' => ServiceProvider::defaultProviders()->merge([

        ....
        
        Sementechs\Notification\NotificationServiceProvider::class,
    
    ])->toArray(),

Define your route

Route::apiResource('/notifications', \Sementechs\Notification\Controllers\NotificationController::class);


Add your pusher credentials in .env file

BROADCAST_DRIVER=puhser

PUSHER_APP_ID=

PUSHER_APP_KEY=

PUSHER_APP_SECRET=

PUSHER_HOST=

PUSHER_PORT=443

PUSHER_SCHEME=https

PUSHER_APP_CLUSTER=mt1


Run following commands

composer dump-autoload

php artisan vendor:publish --tag=notification-migrations

php artisan migrate

