<?php

namespace Sementechs\Notification\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Sementechs\Notification\Events\NotificationEvent;
use Sementechs\Notification\Models\Notification;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FcmNotification;

class NotificationController extends Controller
{
    public static function allNotifications()
    {
        try {
            $notifications = Notification::with('sender.profilePic')->latest()->get();
            return $notifications;
        } catch (Exception $ex) {
            return response($ex->getMessage(), 500);
        }
    }

    public static function getAllNotifications($channel, $type, $userId)
    {
        try {
            $notifications = Notification::with('sender.profilePic')->where('channel', $channel)->where('type', $type)->latest()->get();
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


    /*********** Private functions **********/

    // Send web notifications
    private static function sendWebNotification($data)
    {
        foreach ($data['receiver_ids'] as $receiverId) {
            event(new NotificationEvent($data['sender_id'], $receiverId, $data['channel'], $data['type'], $data['body']));
        }
    }

    // Send mobile notification via firebase
    private static function sendMobileNotification($data)
    {
        $firebase = (new Factory)->withServiceAccount(storage_path(env('FIREBASE_CREDENTIALS')));

        $messaging = $firebase->createMessaging();

        $notification = FcmNotification::create($data['body']['title'], $data['body']['detail']);

        $notification = CloudMessage::fromArray([
            'notification' => ["title" => $data['body']['title'], "body" => $data['body']['detail']]
        ]);

        try {
            $messaging->sendMulticast($notification, $data['device_tokens']);
            return response()->json(['message' => 'Successfully sent message']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error sending message: ' . $e->getMessage()], 500);
        }
    }  
}
