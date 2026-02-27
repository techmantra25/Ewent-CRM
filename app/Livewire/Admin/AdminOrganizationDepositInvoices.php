<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\OrganizationDepositInvoice;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\OrganizationDepositPayment;
use Illuminate\Support\Facades\DB;

class AdminOrganizationDepositInvoices extends Component
{
    use WithPagination, WithFileUploads;
    protected $paginationTheme = 'bootstrap';
    public $search = '';
    public $page = 1;
    public $status = 'pending';
    public $totals = [];
    public $selectedInvoiceId;
    public $utr_number;
    public $payment_date;
    public $receipt;

    public function gotoPage($value, $pageName = 'page')
    {
        $this->setPage($value, $pageName);
        $this->page = $value;
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
    public function resetPageField(){
        $this->reset(['search','status']);
    }

    public function openPaymentModal($invoiceId)
    {
        $this->reset(['utr_number','payment_date','receipt']);
        $this->selectedInvoiceId = $invoiceId;

        $this->dispatch('openPaymentModal');
    }

    public function savePayment()
    {
        $this->validate([
            'utr_number'   => 'required|string|max:100',
            'payment_date' => 'required|date',
            'receipt'      => 'nullable|file|max:2048',
        ]);

        DB::beginTransaction();

        try {

            $invoice = OrganizationDepositInvoice::findOrFail($this->selectedInvoiceId);

            if ($invoice->status === 'paid') {
                DB::rollBack();
                $this->addError('modal-err', 'This invoice is already marked as paid.');
                return;
            }

            // Upload receipt if exists
            $receiptPath = null;
            if ($this->receipt) {
                $receiptPath = storeFileWithCustomName(
                    $this->receipt,
                    'uploads/payment-receipts'
                );
            }

            $payment = OrganizationDepositPayment::firstOrNew([
                'deposit_invoice_id' => $invoice->id,
            ]);

            if ($payment->exists && $payment->payment_status === 'success') {
                DB::rollBack();
                $this->addError('modal-err', 'This payment has already been processed.');
                return;
            }

            $payment->organization_id    = $invoice->organization_id;
            $payment->deposit_invoice_id = $invoice->id;
            $payment->invoice_type       = $invoice->type;
            $payment->payment_method     = 'NEFT'; 
            $payment->utr_no             = $this->utr_number;
            $payment->payment_status     = 'success';
            $payment->amount             = $invoice->total_amount;
            $payment->currency           = 'INR';
            $payment->receipt_upload     = $receiptPath;
            $payment->captured_by        = auth()->id();
            $payment->payment_date       = $this->payment_date;
            $payment->save();

            $invoice->update([
                'status'       => 'paid',
                'payment_date' => $this->payment_date,
            ]);

            DB::commit();

            $this->dispatch('closePaymentModal');

            session()->flash('message', 'Payment captured successfully and marked as Paid.');

        } catch (\Exception $e) {

            DB::rollBack();
            
            $this->addError('modal-err', 'Something went wrong: ' . $e->getMessage());
        }
    }

    public function render()
    {
        // Base query
        $query = OrganizationDepositInvoice::with([
            'organization'
        ])
        ->when($this->search, function ($query) {
            $searchTerm = '%' . $this->search . '%';

            $query->where(function ($q) use ($searchTerm) {
                // Invoice fields
                $q->where('invoice_number', 'like', $searchTerm)
                ->orWhere('type', 'like', $searchTerm)
                ->orWhere('billing_start_date', 'like', $searchTerm)
                ->orWhere('billing_end_date', 'like', $searchTerm)
                ->orWhere('status', 'like', $searchTerm)
                ->orWhere('total_amount', 'like', $searchTerm)
                ->orWhere('payment_date', 'like', $searchTerm)
                ->orWhere('due_date', 'like', $searchTerm);

                // Related organization fields
                $q->orWhereHas('organization', function ($orgQuery) use ($searchTerm) {
                    $orgQuery->where('name', 'like', $searchTerm)
                        ->orWhere('email', 'like', $searchTerm)
                        ->orWhere('mobile', 'like', $searchTerm)
                        ->orWhere('organization_id', 'like', $searchTerm)
                        ->orWhere('street_address', 'like', $searchTerm)
                        ->orWhere('pincode', 'like', $searchTerm)
                        ->orWhere('city', 'like', $searchTerm)
                        ->orWhere('state', 'like', $searchTerm);
                });
            });
        })
        ->when($this->status, function($query) {
            $query->where('status', $this->status);
        });

        // Clone for totals
        $totalsQuery = clone $query;

        $this->totals = [
            'pending' => (clone $totalsQuery)->where('status', 'pending')->sum('total_amount'),
            'paid'    => (clone $totalsQuery)->where('status', 'paid')->sum('total_amount'),
            'overdue' => (clone $totalsQuery)->where('status', 'overdue')->sum('total_amount'),
            'grand'   => (clone $totalsQuery)->sum('total_amount'),
        ];

        // Paginated invoices for table
        $invoices = $query->orderByDesc('id')->paginate(20, ['*'], 'depositInvoice');

        return view('livewire.admin.admin-organization-deposit-invoices', [
            'invoices' => $invoices,
            'totals'   => $this->totals,
        ]);
    }
}
