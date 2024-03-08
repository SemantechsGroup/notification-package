<?php

namespace Sementechs\Notification\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'user_id', 'user_ids', 'type', 'body'
    ];
}
