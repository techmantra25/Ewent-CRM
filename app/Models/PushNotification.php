<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PushNotification extends Model
{
    use HasFactory;
    protected $table = 'push_notifications';
    protected $fillable = [
        'message',
        'recipient_count',
        'rider_type',
        'status',
        'recipients',
    ];

    protected $casts = [
        'recipients' => 'array',
    ];
}
