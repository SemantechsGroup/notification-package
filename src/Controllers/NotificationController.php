<?php

namespace Sementechs\Notification\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Sementechs\Notification\Events\NotificationEvent;
use Sementechs\Notification\Models\Notification;

class NotificationController extends Controller
{
    public function store(Request $request)
    {
        try {
            $data = $request->all();
            foreach ($data['user_ids'] as $receiverId) {
                event(new NotificationEvent($data['user_id'], $receiverId, $data['body']));
            }
            $data['user_ids'] = json_encode($data['user_ids']);
            $data['body'] = json_encode($data['body']);
            Notification::create($data);
            return response('success');
        } catch (Exception $ex) {
            return response($ex->getMessage(), 500);
        }
    }
}
