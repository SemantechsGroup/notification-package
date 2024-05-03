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
    public function getAllNotifications($type, $userId)
    {
        try {
            $notifications = Notification::where('type', $type)->latest()->get();
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
            return response($data);
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
                self::sendMobileNotification($data);
            } else if (empty($data['channel'])) {
                self::sendWebNotification($data);
                self::sendMobileNotification($data);
            }

            Notification::create($data);
            return 'success';
        } catch (Exception $ex) {
            return response($ex->getMessage(), 500);
        }
    }

    private function sendWebNotification($data)
    {
        foreach ($data['receiver_ids'] as $receiverId) {
            event(new NotificationEvent($data['sender_id'], $receiverId, $data['channel'], $data['body'], $data['type']));
        }
        $data['receiver_ids'] = json_encode($data['receiver_ids']);
        $data['body'] = json_encode($data['body']);
    }

    private function sendMobileNotification($data)
    {
        $conditions = "";
        $index = 1;
        $length = count($data['receiver_ids']);

        foreach ($data['receiver_ids'] as $receiverId) {
            $conditions += "user." . $receiverId . " in topics";
            if ($length > 1 && $index == $length - 1) {
                $conditions += " || ";
            }
            $index++;
        }

        LaravelFcm::fromRaw([
            "condition" => $conditions,
            "notification" => [
                "title" => $data['body']['title'],
                "body" => $data['body']['detail']
            ],
        ])->send();
    }

    public function update(Request $request)
    {
        try {
            $data = $request->all();
            foreach ($data as $notification) {
                $not = Notification::find($notification['id']);
                $not->fill(['is_read' => 1])->save();
            }
            return response('success');
        } catch (Exception $ex) {
            return response($ex->getMessage(), 500);
        }
    }
}
