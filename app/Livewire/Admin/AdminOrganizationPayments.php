<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\OrganizationPayment;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\OrganizationPaymentExport;

class AdminOrganizationPayments extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '', $start_date,$end_date;
    public $page = 1;
    public $status = 'success';
    public $totals = [];


    public function gotoPage($value, $pageName = 'page')
    {
        $this->setPage($value, $pageName);
        $this->page = $value;
    }

    public function updateFilters($value,$field){
        $this->reset(['page']);
        $this->$field = $value;
        $this->resetPage();
    }
    public function FilterRider($value)
    {
        $this->reset(['page']);
        $this->search = $value;
        $this->resetPage();
    }

    public function statusFilter()
    {
        $this->reset(['page']);
        $this->resetPage();
    }

    public function resetPageField()
    {
        $this->reset(['search', 'status','start_date','end_date']);
    }

    public function exportAll(){
        return Excel::download(new OrganizationPaymentExport($this->search, $this->status, $this->start_date, $this->end_date), 'organization_payment_history.xlsx');
    }
    public function render()
    {
        // Build the base query
        $query = OrganizationPayment::with('organization')
            ->when($this->search, function ($query) {
                $searchTerm = '%' . $this->search . '%';
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('invoice_id', 'like', $searchTerm)
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

                    $q->orWhereHas('invoice', function ($invoiceQuery) use ($searchTerm) {
                        $invoiceQuery->where('invoice_number', 'like', "%{$searchTerm}%")
                            ->orWhere('status', 'like', "%{$searchTerm}%")
                            ->orWhere('type', 'like', "%{$searchTerm}%")
                            ->orWhere('amount', 'like', "%{$searchTerm}%");
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
            });

        // Clone query for totals before pagination
        $allPaymentsQuery = clone $query;

        $this->totals = [
            'pending' => (clone $allPaymentsQuery)->where('payment_status', 'pending')->sum('amount'),
            'paid'    => (clone $allPaymentsQuery)->where('payment_status', 'success')->sum('amount'),
            'failed'  => (clone $allPaymentsQuery)->where('payment_status', 'failed')->sum('amount'),
            'grand'   => (clone $allPaymentsQuery)->sum('amount'),
        ];

        // Paginated result for table
        $payments = $query->orderByDesc('id')->paginate(20, ['*'], 'payments');

        return view('livewire.admin.admin-organization-payments', [
            'payments' => $payments,
            'totals'   => $this->totals,
        ]);
    }
}
