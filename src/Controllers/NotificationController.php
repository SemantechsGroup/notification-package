<?php

namespace Sementechs\Notification\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Sementechs\Notification\Events\NotificationEvent;
use Sementechs\Notification\Models\Notification;

class NotificationController extends Controller
{
    public function index($type, $userId)
    {
        try {
            $notifications = Notification::where('type', $type)->latest()->get();
            $newNotifications = [];
            $notificationCount = 0;
            foreach ($notifications as $notification) {
                if (in_array($userId, json_decode($notification['receiver_ids']))) {
                    $newNotifications[] = $notification;
                    $notificationCount += 1;
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

    public function store(Request $request)
    {
        try {
            $data = $request->all();
            foreach ($data['receiver_ids'] as $receiverId) {
                event(new NotificationEvent($data['sender_id'], $receiverId, $data['type'], $data['body']));
            }
            $data['receiver_ids'] = json_encode($data['receiver_ids']);
            $data['body'] = json_encode($data['body']);
            Notification::create($data);
            return response('success');
        } catch (Exception $ex) {
            return response($ex->getMessage(), 500);
        }
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
