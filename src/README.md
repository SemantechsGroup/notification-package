open composer.json and add following line

"autoload": {

        ....
        
        "psr-4": {
        
            ....
            
            "Sementechs\\Notification\\": "vendor/semantechs/notification/src/"
            
        }
        
    },
    

open app.php in config folder and add following line


'providers' => ServiceProvider::defaultProviders()->merge([

        ....
        
        Sementechs\Notification\NotificationServiceProvider::class,
    
    ])->toArray(),


add your pusher credentials in .env file


PUSHER_APP_ID=

PUSHER_APP_KEY=

PUSHER_APP_SECRET=

PUSHER_HOST=

PUSHER_PORT=443

PUSHER_SCHEME=https

PUSHER_APP_CLUSTER=mt1


run following commands


composer dump-autload

php artisan migrate

