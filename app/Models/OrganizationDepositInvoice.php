<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrganizationDepositInvoice extends Model
{
     use HasFactory;
    protected $table = 'organization_deposit_invoices';

    protected $fillable = [
        'organization_id',
        'invoice_number',
        'type',
        'status',
        'number_of_vehicle',
        'vehicle_price_per_piece',
        'total_amount',
        'payment_date',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'total_amount' => 'decimal:2',
        'vehicle_price_per_piece' => 'decimal:2',
    ];

    /* =====================
     | Relationships
     ===================== */
    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }
    public function payments()
    {
        return $this->hasMany(OrganizationDepositPayment::class,
            'deposit_invoice_id'
        );
    }
}
