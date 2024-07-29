<?php

namespace Sementechs\Notification\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Sementechs\Notification\Events\NotificationEvent;
use Sementechs\Notification\Models\Notification;
use SmirlTech\LaravelFcm\Facades\LaravelFcm;

class NotificationController extends Controller
{
    public static function allNotifications()
    {
        try {
            $notifications = Notification::latest()->get();
            return $notifications;
        } catch (Exception $ex) {
            return response($ex->getMessage(), 500);
        }
    }

    public static function getAllNotifications($channel, $type, $userId)
    {
        try {
            $notifications = Notification::where('channel', $channel)->where('type', $type)->latest()->get();
            $newNotifications = [];
            $notificationCount = 0;
            foreach ($notifications as $notification) {
                if (in_array($userId, json_decode($notification['receiver_ids']))) {
                    $newNotifications[] = $notification;
                    if ($notification['is_read'] == 0) {
                        $notificationCount += 1;
                    }
                }
            }
            $data = [
                'notifications' => $newNotifications,
                'count' => $notificationCount
            ];
            return $data;
        } catch (Exception $ex) {
            return response($ex->getMessage(), 500);
        }
    }

    public static function sendNotification($data)
    {
        try {
            if ($data['channel'] == 'web') {
                self::sendWebNotification($data);
            } else if ($data['channel'] == 'mobile') {
                self::sendWebNotification($data);
                self::sendMobileNotification($data);
            }

            $data['receiver_ids'] = json_encode($data['receiver_ids']);
            $data['body'] = json_encode($data['body']);
            Notification::create($data);

            return 'done';
        } catch (Exception $ex) {
            return response($ex->getMessage(), 500);
        }
    }

    private static function sendWebNotification($data)
    {
        foreach ($data['receiver_ids'] as $receiverId) {
            event(new NotificationEvent($data['sender_id'], $receiverId, $data['channel'], $data['type'], $data['body']));
        }
    }

    private static function sendMobileNotification($data)
    {
        LaravelFcm::fromArray([
            "title" => $data['body']['title'],
            "body" => $data['body']['detail']
        ])->sendNotification($data['device_tokens']);
    }

    public static function readAll($data)
    {
        try {
            foreach ($data as $notification) {
                $not = Notification::find($notification['id']);
                $not->fill(['is_read' => 1])->save();
            }
            return 'success';
        } catch (Exception $ex) {
            return response($ex->getMessage(), 500);
        }
    }
}
