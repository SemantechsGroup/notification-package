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
            $notifications = Notification::where('type', $type)->latest();
            $newNotifications = [];
            foreach ($notifications as $notification) {
                if (is_array($userId, json_decode($notification['receiver_ids']))) {
                    $newNotifications[] = $notification;
                }
            }
            return response($newNotifications);
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
}
