<?php

namespace Sementechs\Notification\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'sender_id', 'receiver_ids', 'type', 'body', 'is_read', 'channel', 'created_by', 'to'
    ];
}
