<?php

use Illuminate\Support\Facades\Route;

Route::apiResource('/notifications', \Sementechs\Notification\Controllers\NotificationController::class);