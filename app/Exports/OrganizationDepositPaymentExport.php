<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Models\OrganizationDepositPayment;

class OrganizationDepositPaymentExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $search,$status,$start_date,$end_date;
    public function __construct($search,$status,$start_date,$end_date){
        $this->search = $search;
        $this->status = $status;
        $this->start_date = $start_date;
        $this->end_date = $end_date;
    }
    public function collection()
    {
         $query = OrganizationDepositPayment::with(['organization', 'depositInvoice'])
            ->when($this->search, function ($query) {
                $searchTerm = '%' . $this->search . '%';
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('deposit_invoice_id', 'like', $searchTerm)
                        ->orWhere('invoice_type', 'like', $searchTerm)
                        ->orWhere('payment_method', 'like', $searchTerm)
                        ->orWhere('transaction_id', 'like', $searchTerm)
                        ->orWhere('icici_merchantTxnNo', 'like', $searchTerm)
                        ->orWhere('icici_txnID', 'like', $searchTerm)
                        ->orWhere('currency', 'like', $searchTerm)
                        ->orWhere('amount', 'like', $searchTerm)
                        ->orWhere('payment_date', 'like', $searchTerm)
                        ->orWhere('payment_status', 'like', $searchTerm);

                    $q->orWhereHas('organization', function ($orgQuery) use ($searchTerm) {
                        $orgQuery->where('name', 'like', $searchTerm)
                            ->orWhere('email', 'like', $searchTerm)
                            ->orWhere('mobile', 'like', $searchTerm)
                            ->orWhere('organization_id', 'like', $searchTerm);
                    });

                    $q->orWhereHas('depositInvoice', function ($invoiceQuery) use ($searchTerm) {
                        $invoiceQuery->where('invoice_number', 'like', $searchTerm)
                            ->orWhere('status', 'like', $searchTerm)
                            ->orWhere('type', 'like', $searchTerm)
                            ->orWhere('total_amount', 'like', $searchTerm);
                    });
                });
            })
            ->when($this->start_date && $this->end_date, function ($query) {
                $query->whereBetween('payment_date', [$this->start_date . ' 00:00:00', $this->end_date . ' 23:59:59']);
            })
            ->when($this->start_date && !$this->end_date, function ($query) {
                $query->whereDate('payment_date', '>=', $this->start_date);
            })
            ->when(!$this->start_date && $this->end_date, function ($query) {
                $query->whereDate('payment_date', '<=', $this->end_date);
            })
            ->when($this->status, function ($query) {
                $query->where('payment_status', $this->status);
            })
            ->orderByDesc('id')
            ->get();

        // Map data to match the headings
        return $query->map(function ($payment) {
            return [
                $payment->organization->name ?? '',  // Organization Name
                $payment->invoice->invoice_number ?? '', // Invoice Number
                $payment->amount ?? 0,              // Amount
                $payment->transaction_id ?? '',     // Transaction ID
                ucfirst($payment->payment_status),  // Status
                optional($payment->payment_date)->format('Y-m-d H:i:s'), // Payment Date
            ];
        });
    }
    public function headings(): array{
        return [
            'Organization Name',
            'Invoice Number',
            'Amount',
            'Transaction ID',
            'Status',
            'Payment Date',
        ];
    }
}
