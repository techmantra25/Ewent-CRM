<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrganizationPayment extends Model
{
    protected $table = 'organization_payments';

    protected $fillable = [
        'organization_id',
        'invoice_id',
        'invoice_type',
        'payment_method',
        'payment_status',
        'transaction_id',
        'amount',
        'currency',
        'icici_merchantTxnNo',
        'icici_txnID',
        'utr_no',
        'receipt_upload',
        'captured_by',
        'payment_date',
    ];
    protected $casts = [
        'payment_date' => 'datetime',
        'amount' => 'decimal:2',
    ];
   public function organization()
    {
        return $this->belongsTo(\App\Models\Organization::class, 'organization_id');
    }

    public function invoice()
    {
        return $this->belongsTo(\App\Models\OrganizationInvoice::class, 'invoice_id');
    }
    public function capturedByAdmin()
    {
        return $this->belongsTo(\App\Models\Admin::class, 'captured_by');
    }
}
