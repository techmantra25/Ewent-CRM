<div class="row mb-4">
    <style>
        .btn-inactive {
            background: #fff;
            color: #000;
        }
    </style>
    <div class="col-lg-12 d-flex justify-content-between">
        <div>
            <h5 class="mb-0">Organization Payments</h5>
            <div>
                <small class="text-dark fw-medium">Dashboard</small>
                <small class="text-success fw-medium arrow">Organizations</small>
                <small class="text-success fw-medium arrow">Payments</small>
            </div>
        </div>
    </div>

    <div class="col-lg-12 col-md-6 mb-md-0 mb-4">
        <div class="row">
            <div class="col-12">
                <div class="card my-4">
                    <div class="card-header pb-0">
                        <div class="row">
                            @if(session()->has('message'))
                                <div class="alert alert-success" id="flashMessage">
                                    {{ session('message') }}
                                </div>
                            @endif

                            @if(session()->has('error'))
                                <div class="alert alert-danger">
                                    {{ session('error') }}
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="card-body mt-2">
                        <div class="row g-3 align-items-end justify-content-end mb-2">
                            <!-- Start Date -->
                            <div class="col-12 col-sm-6 col-md-2">
                                <label class="form-label text-uppercase small fw-bold">Start Date</label>
                                <input type="date" 
                                    wire:model="start_date" 
                                    wire:change="updateFilters('start_date', $event.target.value)" 
                                    class="form-control form-control-sm border-2">
                            </div>

                            <!-- End Date -->
                            <div class="col-12 col-sm-6 col-md-2">
                                <label class="form-label text-uppercase small fw-bold">End Date</label>
                                <input type="date" 
                                    wire:model="end_date" 
                                    wire:change="updateFilters('end_date', $event.target.value)" 
                                    class="form-control form-control-sm border-2">
                            </div>

                            <!-- Status Filter -->
                            <div class="col-12 col-sm-6 col-md-2">
                                <label class="form-label text-uppercase small fw-bold">Status</label>
                                <select wire:model="status" 
                                        class="form-select form-select-sm border-2" 
                                        wire:change="statusFilter">
                                    <option value="">All</option>
                                    <option value="pending">Pending</option>
                                    <option value="success">Success</option>
                                    <option value="failed">Failed</option>
                                    <option value="refunded">Refunded</option>
                                </select>
                            </div>

                            <!-- Search -->
                            <div class="col-12 col-sm-6 col-md-3">
                                <label class="form-label text-uppercase small fw-bold">Search</label>
                                <input type="text" 
                                    wire:model="search" 
                                    class="form-control form-control-sm border-2"
                                    placeholder="Search here.." 
                                    wire:keyup="FilterRider($event.target.value)">
                            </div>

                            <!-- Reset Button -->
                            <div class="col-12 col-sm-6 col-md-1">
                                <button type="button" 
                                        class="btn btn-danger btn-sm w-100" 
                                        wire:click="resetPageField">
                                    <i class="ri-restart-line"></i>
                                </button>
                            </div>
                            <div class="col-12 col-sm-6 col-md-1">
                                <button wire:click="exportAll" class="btn btn-primary btn-sm waves-effect waves-light">
                                    <i class="ri-download-line"></i> Export
                                </button>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table align-middle">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Organization</th>
                                        <th>Invoice</th>
                                        <th>Payment Method</th>
                                        <th>Status</th>
                                        <th>ICICI Txn No</th>
                                        <th>Amount</th>
                                        <th>NEFT Captured</th>
                                        <th>Payment Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($payments as $index => $payment)
                                        <tr style="font-size: 12px;">
                                            <td>{{ $payments->firstItem() + $index }}</td>

                                            <td>
                                                @if($payment->organization)
                                                    <a href="{{ route('admin.customer.details', $payment->organization->id) }}" class="fw-medium text-truncate">
                                                        {{ $payment->organization->name }}
                                                    </a><br>
                                                    <small>{{ $payment->organization->email ?? 'N/A' }} | {{ $payment->organization->mobile ?? 'N/A' }}</small>
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>

                                            <td>
                                                @if($payment->invoice)
                                                    {{ $payment->invoice->invoice_number }}
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>

                                            <td>{{ ucfirst($payment->payment_method ?? 'N/A') }}</td>

                                            <td>
                                                @php
                                                    $statusColors = [
                                                        'pending' => 'bg-warning',
                                                        'success' => 'bg-success',
                                                        'failed' => 'bg-danger',
                                                        'refunded' => 'bg-primary',
                                                    ];
                                                    $status = strtolower($payment->payment_status ?? 'pending');
                                                    $badgeClass = $statusColors[$status] ?? 'bg-secondary';
                                                @endphp
                                                <span class="badge {{ $badgeClass }}">{{ ucfirst($status) }}</span>
                                            </td>

                                            <td>{{ $payment->icici_txnID ?? $payment->icici_merchantTxnNo ?? '—' }}</td>
                                            <td>{{ env('APP_CURRENCY') }}{{ number_format($payment->amount, 2) }}</td>
                                            <td>
                                                @if($payment->receipt_upload)
                                                    <button 
                                                        class="badge btn btn-secondary"
                                                        wire:click="viewReceipt({{ $payment->id }})">
                                                        View
                                                    </button>
                                                @else   
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                            <td>
                                                {{ $payment->payment_date 
                                                    ? \Carbon\Carbon::parse($payment->payment_date)->format('d M Y h:i A') 
                                                    : '—' 
                                                }}
                                            </td>

                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center">No payments found</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                <tfoot>
                                    <tr class="fw-bold">
                                        <th colspan="1" class="text-end"></th>
                                        <th><span class="text-end">Total Pending:</span> {{ env('APP_CURRENCY') }}{{ number_format($totals['pending'], 2) }}</th>

                                        <th class="text-end">Total Paid:</th>
                                        <th>{{ env('APP_CURRENCY') }}{{ number_format($totals['paid'], 2) }}</th>

                                        <th class="text-end">Total Failed:</th>
                                        <th>{{ env('APP_CURRENCY') }}{{ number_format($totals['failed'], 2) }}</th>

                                        <th class="text-end">Grand Total:</th>
                                        <th>{{ env('APP_CURRENCY') }}{{ number_format($totals['grand'], 2) }}</th>
                                    </tr>
                                </tfoot>

                            </table>

                            <div class="mt-2">
                                {{ $payments->links() }}
                            </div>

                            {{-- captured modal --}}
                                <div wire:ignore.self class="modal fade" id="receiptModal" tabindex="-1">
                                    <div class="modal-dialog modal-lg modal-dialog-centered">
                                        <div class="modal-content">

                                            <div class="modal-header">
                                                <h5 class="modal-title">Payment Captured Details</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>

                                            <div class="modal-body">

                                                @if($selectedPayment)

                                                    <div class="row mb-3">
                                                        <div class="col-md-6">
                                                            <strong>UTR No:</strong><br>
                                                            {{ $selectedPayment->utr_no ?? '—' }}
                                                        </div>

                                                        <div class="col-md-6">
                                                            <strong>Payment Date:</strong><br>
                                                            {{ $selectedPayment->payment_date 
                                                                ? \Carbon\Carbon::parse($selectedPayment->payment_date)->format('d M Y h:i A') 
                                                                : '—' }}
                                                        </div>
                                                    </div>

                                                    <div class="row mb-3">
                                                        <div class="col-md-6">
                                                            <strong>Captured By:</strong><br>
                                                            {{ $selectedPayment->capturedByAdmin->name ?? '' }}
                                                        </div>
                                                    </div>

                                                    <hr>

                                                    <div class="text-center">

                                                        @if($selectedPayment->receipt_upload)

                                                            @if($isPdf)
                                                                <a href="{{ asset($selectedPayment->receipt_upload) }}" 
                                                                target="_blank" 
                                                                class="btn btn-danger">
                                                                Receipt PDF
                                                                </a>
                                                            @else
                                                                <img src="{{ asset($selectedPayment->receipt_upload) }}" 
                                                                    class="img-fluid rounded shadow">
                                                            @endif

                                                        @else
                                                            <p>No receipt uploaded.</p>
                                                        @endif

                                                    </div>

                                                @endif

                                            </div>

                                        </div>
                                    </div>
                                </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="loader-container" wire:loading>
        <div class="loader"></div>
    </div>
</div>
@section('page-script')
<script>
    document.addEventListener('livewire:init', () => {
        Livewire.on('openReceiptModal', () => {
            let modal = new bootstrap.Modal(document.getElementById('receiptModal'));
            modal.show();
        });
    });
</script>
@endsection

