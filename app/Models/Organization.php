<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Organization extends Authenticatable
{
    use Notifiable;
    protected $table = 'organizations';
    protected $fillable = [
       'name', 'organization_id', 'subscription_type', 'renewal_day', 'renewal_day_of_month','renewal_interval_days', 'email', 'mobile', 'password', 'image', 'gst_number', 'gst_file', 'pan_number', 'pan_file', 'status', 'rider_visibility_percentage', 'discount_percentage', 'street_address', 'city', 'state', 'pincode'
    ];

    protected $hidden = [
        'password',
    ];

    public function logs()
    {
        return $this->hasMany(OrganizationLog::class);
    }
    public function user()
    {
        return $this->hasMany(User::class,'organization_id','id');
    }
}
