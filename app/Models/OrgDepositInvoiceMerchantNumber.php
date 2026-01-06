<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrgDepositInvoiceMerchantNumber extends Model
{
    use HasFactory;

    protected $table = 'org_deposit_invoice_merchant_numbers';

    protected $fillable = [
        'organization_id',
        'deposit_invoice_id',
        'merchantTxnNo',
        'redirect_url',
        'secureHash',
        'tranCtx',
        'amount',
    ];

    /* =====================
     | Relationships
     ===================== */

    public function organization()
    {
        return $this->belongsTo(Organization::class,
            'organization_id',
            'id'
        );
    }

    public function depositInvoice()
    {
        return $this->belongsTo(OrganizationDepositInvoice::class,
            'deposit_invoice_id',
            'id'
        );
    }
}
