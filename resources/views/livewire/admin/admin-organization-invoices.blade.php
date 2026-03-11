<div class="row mb-4">
    <style>
        .btn-inactive{
            background: #fff;
            color: #000;
        }
    </style>
    <div class="col-lg-12 d-flex justify-content-between">
        <div>
            <h5 class="mb-0">Organization Invoices</h5>
            <div>
                 <small class="text-dark fw-medium">Dashboard</small>
                 <small class="text-success fw-medium arrow">Organizations</small>
                 <small class="text-success fw-medium arrow">Invoices</small>
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
                        <div class="d-flex align-items-center justify-content-end flex-wrap gap-2 mb-2">
                            <!-- Status Filter -->
                            <div style="max-width: 200px;">
                                <select wire:model="status" class="form-select form-select-sm border border-2" wire:change="statusFilter">
                                    <option value="">All Status</option>
                                    <option value="pending">Pending</option>
                                    <option value="paid">Paid</option>
                                    <option value="overdue">Overdue</option>
                                </select>
                            </div>
                            <div style="max-width: 350px;" class="text-start text-uppercase">
                                <input type="text" 
                                    wire:model="search" 
                                    class="form-control border border-2 p-2 custom-input-sm"
                                    placeholder="Search here..."
                                    wire:keyup="FilterRider($event.target.value)">
                            </div>

                            

                            <!-- Reset Button -->
                            <a href="javascript:void(0)" class="btn btn-danger text-white custom-input-sm" wire:click="resetPageField">
                                <i class="ri-restart-line"></i>
                            </a>
                        </div>

                        <div class="table-responsive">
                            <table class="table align-middle">
                                <thead class="">
                                    <tr class="">
                                        <th>#</th>
                                        <th>Invoice No/Type</th>
                                        <th>Organization</th>
                                        <th>Billing Period</th>
                                        <th>Status</th>
                                        <th>Amount</th>
                                        <th>Invoice Date</th>
                                        <th>Due Date</th>
                                        <th>Payment Date</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($invoices as $index => $invoice)
                                        <tr style="cursor:pointer;font-size: 12px;" data-bs-toggle="collapse" data-bs-target="#payment-invoice-{{ $invoice->id }}" aria-expanded="false" class="invoice-body-item">
                                            <td>{{ $invoices->firstItem() + $index }}</td>
                                            <td>
                                                <div>
                                                    <small class="badge bg-label-primary mb-0 cursor-pointer text-uppercase"> {{ ucfirst($invoice->type) }}</small>
                                                </div>
                                                {{ $invoice->invoice_number }}</td>
                                            <td>
                                                <div class="d-flex justify-content-start align-items-center customer-name">
                                                    @php
                                                        $org = $invoice->organization ?? null;
                                                        $orgName = $org->name ?? '';
                                                        $orgInitials = '';
                                                        if ($orgName) {
                                                            $parts = explode(' ', $orgName);
                                                            $first = strtoupper(substr($parts[0], 0, 1));
                                                            $last = count($parts) > 1 ? strtoupper(substr(end($parts), 0, 1)) : '';
                                                            $orgInitials = $first.$last;
                                                        }
                                                    @endphp
                                                    <div class="d-flex flex-column">
                                                        @if($org)
                                                            <a href="{{ route('admin.organization.dashboard', $org->id) }}" class="text-heading">
                                                                <span class="fw-medium text-truncate">{{ ucwords($orgName) }}</span>
                                                                @if(!empty($org->organization_id))
                                                                    <span class="badge rounded-pill bg-primary border border-white">
                                                                        {{ $org->organization_id }}
                                                                    </span>
                                                                @endif
                                                            </a>

                                                            <small class="text-truncate">
                                                                {{ $org->email ?? 'N/A' }}
                                                            </small>
                                                        @else
                                                            <span class="text-muted">No organization data</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>

                                            <td>
                                                <i class="ri-calendar-line text-primary"></i>
                                                {{ \Carbon\Carbon::parse($invoice->billing_start_date)->format('d M Y') }} <br>
                                                <i class="ri-calendar-line text-danger"></i>
                                                {{ \Carbon\Carbon::parse($invoice->billing_end_date)->format('d M Y') }}
                                                </td>
                                            <td>
                                                @php
                                                    $statusColors = [
                                                        'pending' => 'bg-warning',
                                                        'paid' => 'bg-success',
                                                        'overdue' => 'bg-danger',
                                                    ];
                                                    $status = strtolower($invoice->status ?? 'pending'); // fallback to pending
                                                    $badgeClass = $statusColors[$status] ?? 'bg-secondary';
                                                @endphp

                                                <span class="badge {{ $badgeClass }}">
                                                    {{ ucfirst($status) }}
                                                </span>
                                            </td>
                                            <td>{{env('APP_CURRENCY')}}{{ number_format($invoice->amount, 2) }}</td>
                                            <td>
                                                <span>
                                                    {{ $invoice->created_at 
                                                        ? \Carbon\Carbon::parse($invoice->created_at)->format('d M Y') 
                                                        : '—' 
                                                    }}
                                                </span>
                                            </td>
                                            <td>
                                                <span>
                                                    {{ $invoice->due_date 
                                                        ? \Carbon\Carbon::parse($invoice->due_date)->format('d M Y') 
                                                        : '—' 
                                                    }}
                                                </span>
                                            </td>
                                            <td>
                                                <span>
                                                    {{ $invoice->payment_date 
                                                        ? \Carbon\Carbon::parse($invoice->payment_date)->format('d M Y') 
                                                        : '—' 
                                                    }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex justify-content-between">
                                                    <a href="javascript:void(0);" data-bs-toggle="collapse" data-bs-target="#payment-invoice-{{ $invoice->id }}" aria-expanded="false">
                                                        <span class="control">
                                                            <i class="bi bi-chevron-down"></i> <!-- Bootstrap icon example -->
                                                        </span>
                                                    </a>
                                                    @if($status!= 'paid')
                                                        <a href="javascript:void(0);" class="badge bg-info" wire:click="openPaymentModal({{ $invoice->id }})">
                                                            Capture
                                                        </a>
                                                    @endif
                                                </div>
                                            </td>

                                        </tr>
                                        <!-- Child: Invoice Items (Collapsed) -->
                                         <tr class="collapse" id="payment-invoice-{{ $invoice->id }}">
                                            <td colspan="9" class="p-0">
                                                <table class="table mb-0">
                                                    @foreach($invoice->items as $item)
                                                    @php
                                                        // Unique collapse id per rider (invoice + item)
                                                        $collapseId = "invoice-details-{$invoice->id}-{$item->id}";
                                                    @endphp
                                                        <tr class="table-light invoice-items cursor-pointer" data-bs-toggle="collapse" 
                                                                data-bs-target="#{{$collapseId}}">
                                                            <td colspan="3"><strong>Rider:</strong> {{ $item->user?->name ?? 'N/A' }}</td>
                                                            <td colspan="3"><strong>Total Days:</strong> {{ $item->total_day }}</td>
                                                            <td colspan="3" width="20%" class="text-end"><strong>Total Price:</strong> {{env('APP_CURRENCY')}}{{ number_format($item->total_price, 2) }}
                                                            <a href="javascript:void(0);" 
                                                                data-bs-toggle="collapse" 
                                                                data-bs-target="#{{$collapseId}}" 
                                                                aria-expanded="false" 
                                                                class="toggle-arrow">
                                                                <i class="ri-arrow-down-s-line ri-24px"></i>
                                                            </a>
                                                            </td>
                                                        </tr>
                                                        @php
                                                            $details = [];
                                                            foreach ($item->details as $key => $value) {
                                                                $details[$value->day_amount]['dates'][] = $value->date;
                                                                $details[$value->day_amount]['days'][] = $key+1;
                                                                $details[$value->day_amount]['amounts'][] = $value->day_amount;
                                                            }
                                                        @endphp

                                                        @foreach($details as $day_amount => $detail)
                                                            @php
                                                                // Sort dates
                                                                $dates = collect($detail['dates'])->sort()->values()->all();

                                                                // Group consecutive dates
                                                                $groups = [];
                                                                $group = [];
                                                                foreach ($dates as $date) {
                                                                    if (empty($group)) {
                                                                        $group[] = $date;
                                                                    } else {
                                                                        $prevDate = end($group);
                                                                        $expectedNext = date('Y-m-d', strtotime($prevDate . ' +1 day'));
                                                                        if ($date === $expectedNext) {
                                                                            $group[] = $date;
                                                                        } else {
                                                                            $groups[] = $group; // save previous group
                                                                            $group = [$date]; // start new group
                                                                        }
                                                                    }
                                                                }
                                                                if (!empty($group)) {
                                                                    $groups[] = $group; // add last group
                                                                }
                                                                // dd($group,$groups);
                                                            @endphp
                                                            @foreach($groups as $datesGroup)
                                                                @php
                                                                    $startDate = \Carbon\Carbon::parse($datesGroup[0])->format('d M Y');
                                                                    $endDate   = \Carbon\Carbon::parse(end($datesGroup))->format('d M Y');
                                                                    $totalDays = count($datesGroup);
                                                                    $amountPerDay = (float)$day_amount;
                                                                    $totalAmount = $amountPerDay * $totalDays;
                                                                @endphp

                                                                <tr class="table-sm invoice-details collapse" id="{{ $collapseId }}">
                                                                    <td colspan="3">
                                                                        {{ $startDate }} @if($totalDays > 1) to {{ $endDate }} @endif
                                                                    </td>
                                                                    <td colspan="3">
                                                                        {{ $totalDays }} {{ Str::plural('day', $totalDays) }}
                                                                    </td>
                                                                    <td colspan="3" class="text-end">
                                                                        {{ env('APP_CURRENCY') }}{{ number_format($amountPerDay, 2) }}
                                                                        × {{ $totalDays }} = 
                                                                        <strong>{{ env('APP_CURRENCY') }}{{ number_format($totalAmount, 2) }}</strong>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        @endforeach


                                                    @endforeach
                                                </table>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center">No invoices found</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                <tfoot>
                                    <tr class="fw-bold">
                                        <th colspan="2" class="text-end">Total Pending:</th>
                                        <th>{{ env('APP_CURRENCY') }}{{ number_format($totals['pending'], 2) }}</th>

                                        <th class="text-end">Total Paid:</th>
                                        <th>{{ env('APP_CURRENCY') }}{{ number_format($totals['paid'], 2) }}</th>

                                        <th class="text-end">Total Overdue:</th>
                                        <th>{{ env('APP_CURRENCY') }}{{ number_format($totals['overdue'], 2) }}</th>

                                        <th class="text-end">Grand Total:</th>
                                        <th>{{ env('APP_CURRENCY') }}{{ number_format($totals['grand'], 2) }}</th>
                                    </tr>
                                </tfoot>

                                <!-- Payment Capture Modal -->
                                <div wire:ignore.self class="modal fade" id="paymentCaptureModal" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form wire:submit.prevent="savePayment">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">NEFT Payment Captured By UTR No.</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>

                                                <div class="modal-body">
                                                    @error('modal-err')
                                                        <div class="alert alert-danger">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                    <!-- UTR Number -->
                                                    <div class="mb-3">
                                                        <label class="form-label">UTR Number</label>
                                                        <input type="text" class="form-control" wire:model="utr_number">
                                                        @error('utr_number') <small class="text-danger">{{ $message }}</small> @enderror
                                                    </div>

                                                    <!-- Payment Date -->
                                                    <div class="mb-3">
                                                        <label class="form-label">Actual Payment Date</label>
                                                        <input type="date" class="form-control" wire:model="payment_date">
                                                        @error('payment_date') <small class="text-danger">{{ $message }}</small> @enderror
                                                    </div>

                                                    <!-- Receipt Upload -->
                                                    <div class="mb-3">
                                                        <label class="form-label">Upload Receipt</label>
                                                        <input type="file" class="form-control" wire:model="receipt">
                                                        @error('receipt') <small class="text-danger">{{ $message }}</small> @enderror
                                                    </div>

                                                    <div wire:loading wire:target="receipt" class="text-info">
                                                        Uploading...
                                                    </div>

                                                </div>

                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-primary">Submit</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                            </table>
                            <div class="mt-2">
                                {{ $invoices->links() }}
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

        Livewire.on('openPaymentModal', () => {
            let modal = new bootstrap.Modal(document.getElementById('paymentCaptureModal'));
            modal.show();
        });

        Livewire.on('closePaymentModal', () => {
            let modalEl = document.getElementById('paymentCaptureModal');
            let modal = bootstrap.Modal.getInstance(modalEl);
            modal.hide();
        });

    });
</script>
@endsection

