<?php

namespace Sementechs\Notification\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'sender_id',
        'receiver_ids',
        'type',
        'body',
        'is_read',
        'channel',
        'created_by',
        'to'
    ];

    protected $appends = ['sent_at'];

    public function getSentAtAttribute()
    {
        $endDate = Carbon::createFromDate($this->created_at);
        return $endDate->diffForHumans();
    }

    public function sender()
    {
        return $this->belongsTo(\App\Models\UserProfile::class, 'sender_id', 'user_id');
    }
}
