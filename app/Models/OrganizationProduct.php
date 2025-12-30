<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrganizationProduct extends Model
{
    protected $table = 'organization_products';

    protected $fillable = [
        'organization_id',
        'product_id',
    ];

    public function organization(){
        return $this->belongsTo(Organization::class, 'organization_id', 'id');
    }

    public function product(){
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }
}
