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

Place your firebase service account file in storage folder and add file name in .env file

FIREBASE_CREDENTIALS=file_name.json

Run following commands

composer dump-autoload

php artisan vendor:publish --tag=laravel-assets

php artisan migrate

Available Methods

1. Send Notification

$notificationObject = [
'sender_id' => 1,
'receiver_ids' => [
1, 2, 3
],
'channel' => 'web', // web, mobile
'body' => [
'title' => 'Title',
'body' => 'Detail'
],
'type' => 'admin' // admin, user
];

NotificationController::sendNotification($notificationObject);

2. Get All Notification

$type = web/user

NotificationController::getAllNotifications($type, $userId)

3. Read All Notifications

$data = objects of all notifications

NotificationController::readAll($data)
