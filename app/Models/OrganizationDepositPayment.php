<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrganizationDepositPayment extends Model
{
    protected $table = 'organization_deposit_payments';

    protected $fillable = [
        'organization_id',
        'deposit_invoice_id',
        'invoice_type',
        'payment_method',
        'payment_status',
        'transaction_id',
        'amount',
        'currency',
        'icici_merchantTxnNo',
        'icici_txnID',
        'payment_date',
    ];

    protected $casts = [
        'payment_date' => 'datetime',
        'amount' => 'decimal:2',
    ];

    /* =====================
     | Relationships
     ===================== */

    public function organization()
    {
        return $this->belongsTo(Organization::class, 'organization_id');
    }

    public function depositInvoice()
    {
        return $this->belongsTo(OrganizationDepositInvoice::class,
            'deposit_invoice_id'
        );
    }
}
