<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    protected $table = "branches";
    protected $fillable = [
        'name',
        'branch_code',
        'address',
        'city_id',
        'status'
    ];
    public function city()
    {
        return $this->belongsTo(\App\Models\City::class);
    }
}
