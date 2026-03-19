<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VehicleDailyEarningB2c extends Model
{
    protected $table = 'vehicle_daily_earnings_b2c';

    protected $fillable = [
        'order_id',
        'rider_id',
        'vehicle_id',
        'amount',
        'date',
    ];
}
