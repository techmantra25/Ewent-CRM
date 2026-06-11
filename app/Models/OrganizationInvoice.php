<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrganizationInvoice extends Model
{
    protected $table = 'organization_invoices';
    protected $fillable = [
        'branch_id', 'organization_id', 'invoice_number', 'type', 'billing_start_date', 'billing_end_date', 'status', 'amount', 'payment_date', 'due_date'
    ];

    public function organization(){
        return $this->belongsTo(Organization::class, 'organization_id', 'id');
    }
    public function items()
    {
        return $this->hasMany(OrganizationInvoiceItem::class, 'invoice_id');
    }
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
