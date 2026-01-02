
<div>


<div class="row mb-4">
  <style>
    .avatar {
        position: relative;
        width: 1.5rem !important;
        height: 1.5rem !important;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
    }
    .bg-gradient-success {
        background: linear-gradient(135deg, #28c76f 0%, #9be15d 100%);
    }
    .card h5.card-title {
        font-weight: 600;
        font-size: 13px;
    }
    .invoice-head-item th {
        font-size: 10px;
        letter-spacing: 0.5px;
    }
     .side-modal {
        position: fixed;
        top: 0;
        right: -400px; /* Initially hidden */
        width: 500px;
        height: 690px;
        background: #fff;
        box-shadow: -2px 0 10px rgba(0, 0, 0, 0.1);
        padding: 20px;
        transition: right 0.3s ease-in-out;
        z-index: 10000;
    }

    .side-modal.open {
        right: 0;
    }

    .side-modal-content {
        display: flex;
        flex-direction: column;
        max-height: -webkit-fill-available;
        overflow-y: auto;
    }

    .close-btn {
        position: absolute;
        top: 10px;
        right: 10px;
        font-size: 20px;
        border: none;
        background: none;
        cursor: pointer;
    }

    .overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1000;
    }

    /* 17-03-2025 */
    .side-modal {
        height: 100vh;
    }
    .side-modal-content {
        height: calc(100vh - 110px);
    }
   
  </style>
    <div class="col-lg-12 col-md-6 mb-md-0 mb-4">
        <div class="row mb-4">
            <div class="col-lg-12 d-flex justify-content-between">
                <div>
                    <div>
                        <small class="text-dark fw-medium">Dashboard</small>
                        <small class="text-success fw-medium arrow">{{$organization->name}}</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            @if(session()->has('message'))
                <div class="alert alert-success" id="flashMessage">
                    {{ session('message') }}
                </div>
            @endif

            @if(session()->has('error'))
                <div class="alert alert-danger" id="flashMessage">
                    {{ session('error') }}
                </div>
            @endif
        </div>
        <div class="row" style="font-size: 11px !important;">
            <div class="col-12">
                <div class="row text-nowrap">
                    <div class="nav-align-top">
                        <ul class="nav nav-pills flex-column flex-md-row flex-wrap mb-6 row-gap-2">
                            <li class="nav-item">
                                <a class="nav-link waves-effect waves-light {{$activeTab=="overview"?"active":""}}"
                                href="javascript:void(0)" wire:click="changeTab('overview')">
                                <i class="icon-base ri ri-group-line icon-sm me-1_5"></i>Overview
                                </a>
                            </li>
                            <!-- Models -->
                            <li class="nav-item">
                                <a class="nav-link waves-effect waves-light {{ $activeTab=='models' ? 'active' : '' }}"
                                href="javascript:void(0)" wire:click="changeTab('models')">
                                <i class="icon-base ri ri-motorbike-fill icon-sm me-1_5"></i> Models
                                </a>
                            </li>
                            <!-- Riders -->
                            <li class="nav-item">
                                <a class="nav-link waves-effect waves-light {{ $activeTab=='riders' ? 'active' : '' }}"
                                href="javascript:void(0)" wire:click="changeTab('riders')">
                                <i class="icon-base ri ri-user-line icon-sm me-1_5"></i> Riders
                                </a>
                            </li>

                           <!-- Invoice History -->
                            <li class="nav-item">
                                <a class="nav-link waves-effect waves-light {{ $activeTab=='invoice' ? 'active' : '' }}"
                                href="javascript:void(0)" wire:click="changeTab('invoice')">
                                    <i class="icon-base ri ri-file-list-3-line icon-sm me-1_5"></i> Invoice History
                                </a>
                            </li>

                            <!-- Payment History -->
                            <li class="nav-item">
                                <a class="nav-link waves-effect waves-light {{ $activeTab=='payment' ? 'active' : '' }}"
                                href="javascript:void(0)" wire:click="changeTab('payment')">
                                    <i class="icon-base ri ri-wallet-3-line icon-sm me-1_5"></i> Payment History
                                </a>
                            </li>

                        </ul>
                    </div>
                </div>
                @if($activeTab=="overview")
                    <div class="row">
                        <div class="col-md-9">
                            <div class="row text-nowrap">
                            <div class="col-md-6 mb-6">
                                <div class="card mb-2">
                                    <div class="card-body d-flex align-items-center justify-content-between">

                                        <!-- Left Section -->
                                        <div class="d-flex align-items-center">
                                            <div class="card-icon me-3">
                                                <div class="avatar" style="width: 2.5rem !important; height: 2.5rem !important;">
                                                    <div class="avatar-initial rounded bg-label-danger">
                                                        <i class="ri-eye-line ri-24px"></i>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-info">
                                                <h5 class="card-title mb-1">Rider Visibility</h5>
                                                <p class="mb-0 text-muted">Visibility markup applied</p>
                                            </div>
                                        </div>

                                        <!-- Right Section -->
                                        <div class="text-end">
                                            <span class="fw-bold display-6 
                                                {{ ($organization->rider_visibility_percentage ?? 0) > 0 ? 'text-danger' : 'text-secondary' }}">
                                                {{ ($organization->rider_visibility_percentage ?? 0) > 0 
                                                    ? '+' . $organization->rider_visibility_percentage . '%' 
                                                    : '0%' }}
                                            </span>
                                        </div>

                                    </div>
                                </div>

                                <div class="card mb-2">
                                    <div class="card-body d-flex align-items-center justify-content-between">

                                        <!-- Left Section -->
                                        <div class="d-flex align-items-center">
                                        <div class="card-icon me-3">
                                            <div class="avatar" style="width: 2.5rem !important; height: 2.5rem !important;">
                                            <div class="avatar-initial rounded bg-label-info">
                                                <i class="ri-bookmark-line ri-24px"></i>
                                            </div>
                                            </div>
                                        </div>
                                        <div class="card-info">
                                            <h5 class="card-title mb-1">Subscription Type</h5>
                                            <p class="mb-0 text-muted">Current plan type</p>
                                        </div>
                                        </div>

                                        <!-- Right Section -->
                                        <div class="text-end">
                                        <span class="fw-bold display-6 
                                            {{ strtolower($organization->subscription_type) == 'monthly' ? 'text-success' : 'text-primary' }}">
                                            {{ ucwords($organization->subscription_type ?? '-') }}
                                        </span>
                                        </div>

                                    </div>
                                </div>
                                <div class="card mb-2">
                                    <div class="card-body d-flex align-items-center justify-content-between">

                                        <!-- Left Section -->
                                        <div class="d-flex align-items-center">
                                        <div class="card-icon me-3">
                                            <div class="avatar" style="width: 2.5rem !important; height: 2.5rem !important;">
                                            <div class="avatar-initial rounded bg-label-warning">
                                                <i class="ri-calendar-event-line ri-24px"></i>
                                            </div>
                                            </div>
                                        </div>
                                        <div class="card-info">
                                            <h5 class="card-title mb-1">
                                            {{ strtolower($organization->subscription_type) == 'monthly' ? 'Billing Date' : 'Billing Day' }}
                                            </h5>
                                            <p class="mb-0 text-muted">Next cycle billing</p>
                                        </div>
                                        </div>

                                        <!-- Right Section -->
                                        <div class="text-end">
                                            @if(strtolower($organization->subscription_type) == "monthly")
                                               @php
                                                    $day = (int) $organization->renewal_day_of_month; // make sure it's numeric
                                                    $today = now();
                                                    $renewalDate = $today->copy()->day($day);

                                                    if ($renewalDate->lt($today)) {
                                                        $renewalDate->addMonth();
                                                    }
                                                @endphp

                                                <span class="fw-bold display-6 text-warning">
                                                    {{ $renewalDate->format('jS M Y') }}
                                                </span>
                                            @else
                                                <span class="fw-bold display-6 text-warning">
                                                    {{ ucwords($organization->renewal_day) }}
                                                </span>
                                            @endif
                                        </div>


                                        {{-- <div class="text-end">
                                            @if(strtolower($organization->subscription_type) == "monthly")
                                                <span class="fw-bold display-6 text-warning">
                                                {{ $organization->renewal_day_of_month }}<sup>th</sup>
                                                {{ now()->format('M Y') }}
                                                </span>
                                            @else
                                                <span class="fw-bold display-6 text-warning">
                                                {{ ucwords($organization->renewal_day) }}
                                                </span>
                                            @endif
                                        </div> --}}

                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 mb-6">
                                <div class="card mb-2">
                                    <div class="card-body d-flex align-items-center justify-content-between">

                                        <!-- Left Section -->
                                        <div class="d-flex align-items-center">
                                            <div class="card-icon me-3">
                                                <div class="avatar" style="width: 2.5rem !important; height: 2.5rem !important;">
                                                    <div class="avatar-initial rounded bg-label-success">
                                                        <i class="ri-percent-line ri-24px"></i>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-info">
                                                <h5 class="card-title mb-1">Organization Discount</h5>
                                                <p class="mb-0 text-muted">Discount on subscriptions</p>
                                            </div>
                                        </div>

                                        <!-- Right Section -->
                                        <div class="text-end">
                                            <span class="fw-bold display-6 
                                                {{ ($organization->discount_percentage ?? 0) > 0 ? 'text-success' : 'text-secondary' }}">
                                                {{ ($organization->discount_percentage ?? 0) > 0 
                                                    ? '-' . $organization->discount_percentage . '%' 
                                                    : '0%' }}
                                            </span>
                                        </div>

                                    </div>
                                </div>

                                <!-- Riders Count Card -->
                                <div class="card mb-2">
                                    <div class="card-body d-flex align-items-center justify-content-between">

                                    <!-- Left Section -->
                                    <div class="d-flex align-items-center">
                                        <div class="card-icon me-3">
                                        <div class="avatar" style="width: 2.5rem !important; height: 2.5rem !important;">
                                            <div class="avatar-initial rounded bg-label-primary">
                                            <i class="ri-user-line ri-24px"></i>
                                            </div>
                                        </div>
                                        </div>
                                        <div class="card-info">
                                        <h5 class="card-title mb-1">All Riders</h5>
                                        <p class="mb-0 text-muted">
                                            @if($allRidersCount > 0)
                                            Total registered riders
                                            @else
                                            🚴 No riders registered yet
                                            @endif
                                        </p>
                                        </div>
                                    </div>

                                    <!-- Right Section -->
                                    <div class="text-end">
                                        @if($allRidersCount > 0)
                                        <span class="fw-bold display-5 text-primary">
                                            {{ $allRidersCount }}
                                        </span>
                                        @else
                                        <span class="fw-bold display-6 text-muted">0</span>
                                        <p class="mb-0 small text-muted">Empty list</p>
                                        @endif
                                    </div>

                                    </div>
                                </div>

                                <!-- Assigned Vehicles Count Card -->
                                <div class="card mb-2">
                                    <div class="card-body d-flex align-items-center justify-content-between">

                                    <!-- Left Section -->
                                    <div class="d-flex align-items-center">
                                        <div class="card-icon me-3">
                                        <div class="avatar" style="width: 2.5rem !important; height: 2.5rem !important;">
                                            <div class="avatar-initial rounded bg-label-success">
                                            <i class="ri-car-line ri-24px"></i>
                                            </div>
                                        </div>
                                        </div>
                                        <div class="card-info">
                                        <h5 class="card-title mb-1">Assigned Vehicles</h5>
                                        <p class="mb-0 text-muted">
                                            @if($assignedVehiclesCount > 0)
                                            Currently assigned vehicles
                                            @else
                                            🚴 No vehicles assigned yet
                                            @endif
                                        </p>
                                        </div>
                                    </div>

                                    <!-- Right Section -->
                                    <div class="text-end">
                                        @if($assignedVehiclesCount > 0)
                                        <span class="fw-bold display-5 text-success">
                                            {{ $assignedVehiclesCount }}
                                        </span>
                                        @else
                                        <span class="fw-bold display-6 text-muted">0</span>
                                        <p class="mb-0 small text-muted">Empty list</p>
                                        @endif
                                    </div>

                                    </div>
                                </div>

                                </div>

                            <div class="col-md-12 mb-6">
                                <div class="card">
                                    <div class="card-body mt-3">
                                        <div class="table-responsive">
                                            <table class="table align-middle">
                                                <thead class="table-dark">
                                                    <tr class="invoice-head-item">
                                                        <th>Invoice No</th>
                                                        <th>Type</th>
                                                        <th>Billing Period</th>
                                                        <th>Status</th>
                                                        <th>Amount</th>
                                                        <th>Invoice Date</th>
                                                        <th>Due Date</th>
                                                        <th>Payment Date</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse($invoices->sortByDesc('created_at')->take(5) as $index => $invoice)
                                                        <tr style="cursor:pointer;" data-bs-toggle="collapse" data-bs-target="#invoice-{{ $invoice->id }}" aria-expanded="false" class="invoice-body-item">
                                                            <td>{{ $invoice->invoice_number }}</td>
                                                            <td>{{ ucfirst($invoice->type) }}</td>
                                                            <td>
                                                                <i class="ri-calendar-line text-primary"></i>
                                                                {{ \Carbon\Carbon::parse($invoice->billing_start_date)->format('d M Y') }} <br>
                                                                <i class="ri-calendar-line text-danger"></i>
                                                                {{ \Carbon\Carbon::parse($invoice->billing_end_date)->format('d M Y') }}
                                                            </td>
                                                            <td>
                                                                <span class="badge 
                                                                    {{ $invoice->status == 'paid' ? 'bg-success' : ($invoice->status == 'pending' ? 'bg-warning' : 'bg-danger') }}">
                                                                    {{ ucfirst($invoice->status) }}
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
                                                                <div class="d-flex justify-content-between">
                                                                    <span>
                                                                        {{ $invoice->payment_date 
                                                                            ? \Carbon\Carbon::parse($invoice->payment_date)->format('d M Y') 
                                                                            : '—' 
                                                                        }}
                                                                    </span>
                                                                    <a href="javascript:void(0);" data-bs-toggle="collapse" data-bs-target="#invoice-{{ $invoice->id }}" aria-expanded="false">
                                                                        <span class="control">
                                                                            <i class="bi bi-chevron-down"></i> <!-- Bootstrap icon example -->
                                                                        </span>
                                                                    </a>
                                                                </div>
                                                            </td>

                                                        </tr>

                                                        <!-- Child: Invoice Items (Collapsed) -->
                                                        <tr class="collapse" id="invoice-{{ $invoice->id }}">
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
                                                                            <td colspan="3" width="15%">
                                                                                <strong>Total Price:</strong> {{env('APP_CURRENCY')}}{{ number_format($item->total_price, 2) }}
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
                                                                                // Sort dates to ensure correct range
                                                                                $dates = collect($detail['dates'])->sort()->values();
                                                                                $startDate = \Carbon\Carbon::parse($dates->first())->format('d M Y');
                                                                                $endDate   = \Carbon\Carbon::parse($dates->last())->format('d M Y');

                                                                                $totalDays = count($dates);
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
                                                                                <td colspan="3">
                                                                                    {{ env('APP_CURRENCY') }}{{ number_format($amountPerDay, 2) }}
                                                                                    × {{ $totalDays }} = 
                                                                                    <strong>{{ env('APP_CURRENCY') }}{{ number_format($totalAmount, 2) }}</strong>
                                                                                </td>
                                                                            </tr>
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
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            </div>
                        </div>
                            <div class="col-md-3">
                                <div class="card shadow-lg p-4 hover-shadow transition">
                                    <div class="card-body">

                                        <!-- User Avatar & Info -->
                                        <div class="customer-avatar-section text-center mb-6">
                                            <div class="position-relative d-inline-block">
                                                <img class="img-fluid rounded-circle border border-3 border-white shadow-sm"
                                                    src="{{ $organization->image ? asset($organization->image) : asset('assets/img/organization.png') }}"
                                                    height="100" width="100" alt="Organization avatar">

                                                <!-- Status Badge -->
                                                <span
                                                    class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-success border border-white">
                                                    {{ $organization->organization_id}}
                                                </span>
                                            </div>

                                            <h5 class="mt-3 mb-1">{{ $organization->name }}</h5>
                                            <p class="text-primary mb-0">{{ $organization->email }}</p>
                                        </div>

                                        {{-- Pending Invoice / Spent Section --}}
                                        @if(isset($pendingInvoice) && in_array($pendingInvoice->status, ['pending','overdue']))
                                            <div class="alert alert-warning text-center mt-3 shadow-sm rounded">
                                                <h5 class="fw-bold mb-2 text-danger">{{ucwords($pendingInvoice->status)}} Invoice</h5>
                                                <p class="mb-1 text-dark">Invoice #{{ $pendingInvoice->invoice_number }}</p>
                                                <h4 class="fw-bold text-primary mb-3">
                                                    {{ env('APP_CURRENCY', '₹') }}{{ number_format($pendingInvoice->amount, 2) }}
                                                </h4>
                                            </div>
                                        @else
                                            <div class="d-flex justify-content-around flex-wrap gap-3 mb-6">
                                                <div class="d-flex align-items-center gap-3 p-3 rounded bg-light shadow-sm flex-grow-1">
                                                    <div class="avatar bg-gradient-primary text-white rounded-circle p-2">
                                                        <i class="ri-money-rupee-circle-line ri-lg"></i>
                                                    </div>
                                                    <div>
                                                        <h5 class="mb-0">{{ env('APP_CURRENCY', '₹') }}{{ number_format($InvoicePaidAmount) }}</h5>
                                                        <small class="text-muted text-uppercase">Spent Amount</small>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        @if(!isset($pendingInvoice))
                                            <!-- Organization Address Info -->
                                            <div class="info-container mb-4">
                                                <h5 class="border-bottom pb-2 mb-3">Our Information</h5>
                                                <ul class="list-unstyled">
                                                    <li class="mb-2 d-flex align-items-center">
                                                        <i class="ri-calendar-line me-2 text-primary"></i>
                                                        <span>Reg. Date:</span>
                                                        <span class="ms-auto">{{ date('d M Y h:i A', strtotime($organization->created_at)) }}</span>
                                                    </li>
                                                    <li class="mb-2 d-flex align-items-center">
                                                        <i class="ri-phone-line me-2 text-primary"></i>
                                                        <span>Mobile:</span>
                                                        <span class="ms-auto">{{ env('APP_COUNTRY_CODE', 91) }} {{ $organization->mobile }}</span>
                                                    </li>
                                                    <li class="mb-2 d-flex align-items-start">
                                                        <i class="ri-map-pin-line me-2 text-primary mt-1"></i>
                                                        <div>
                                                            <div>{{ $organization->street_address }}</div>
                                                            <div>{{ $organization->city }}, {{ $organization->state }}, {{ $organization->pincode }}</div>
                                                        </div>
                                                    </li>
                                                </ul>
                                            </div>
                                        @endif
                                        <!-- Dynamic Bottom Button -->
                                        <div class="d-flex justify-content-center mt-4">
                                            @if(isset($pendingInvoice) && in_array($pendingInvoice->status, ['pending','overdue']))
                                               @if(
                                                    is_array($paymentMessage) && 
                                                    empty($paymentMessage['status']) && 
                                                    (empty($paymentMessage['redirect_url']) || empty($paymentMessage['redirect_url']['redirect_url']))
                                                )
                                                    <a href="javascript:void(0)"
                                                    wire:click="invoiceInitiatePayment({{ $pendingInvoice->id }})"
                                                    class="btn btn-warning w-100 rounded-pill shadow-sm">
                                                        Pay Now
                                                    </a>
                                                @endif

                                            @else
                                                <button class="btn {{ $organization->status == 1 ? 'btn-primary' : 'btn-danger' }} w-100 rounded-pill shadow-sm">
                                                    {{ $organization->status == 1 ? 'Active' : 'Inactive' }}
                                                </button>
                                            @endif
                                        </div>

                                        @if($paymentMessage)
                                            <div class="mt-2 alert {{ $paymentMessage['status'] ? 'alert-success' : 'alert-danger' }}">
                                                {{ $paymentMessage['response'] }}
                                            </div>
                                            {{-- @if($paymentMessage['status'] && $paymentMessage['redirect_url'])
                                                <div id="redirectNotice" class="mt-2">
                                                    <p class="text-info">
                                                        Redirecting in <span id="countdown">5</span> seconds...
                                                    </p>
                                                </div>
                                            @endif --}}
                                        @endif


                                    </div>
                                </div>
                            </div>


                    </div>
                @endif
                {{-- Model Tab --}}
                @if($activeTab=="models")
                    <div class="row">
                        <div class="col-12">
                            <div class="card h-100 shadow-sm">
                                <div class="card-body">
                                    <div class="table-responsive p-0 mt-2">
                                        <table class="table table-bordered align-middle mb-0">
                                            <thead class="table-dark">
                                                <tr>
                                                    <th class="text-start text-uppercase" style="font-size:11px;">Model</th>
                                                    <th class="text-center text-uppercase" style="font-size:11px;">Subscription Type</th>
                                                    <th class="text-end text-uppercase" style="font-size:11px;">Actual Price</th>
                                                    <th class="text-center text-uppercase" style="font-size:11px;">Rider Visibility</th>
                                                    <th class="text-end text-uppercase" style="font-size:11px;">Billing Price</th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                @forelse($OrganizationModels as $org_model)
                                                    @php
                                                        $prices  = $org_model->product?->rentalpriceB2B ?? collect();
                                                        $rowspan = $prices->count() ?: 1;
                                                    @endphp

                                                    @forelse($prices as $index => $sub_item)

                                                        @php
                                                            $actualPrice = $sub_item->rental_amount;

                                                            $riderVisibilityAmount = ($actualPrice * ($organization->rider_visibility_percentage ?? 0)) / 100;
                                                            $discountAmount        = ($actualPrice * ($organization->discount_percentage ?? 0)) / 100;
                                                        @endphp

                                                        <tr wire:key="{{ $org_model->id }}-{{ $sub_item->id }}">
                                                            {{-- Model --}}
                                                            @if($index === 0)
                                                                <td rowspan="{{ $rowspan }}" class="fw-semibold align-middle">
                                                                    {{ $org_model->product?->title ?? 'N/A' }}
                                                                </td>
                                                            @endif

                                                            {{-- Subscription Type --}}
                                                            <td class="text-center">
                                                                <span class="bg-label-primary px-2 py-1 rounded">
                                                                    {{ ucfirst($sub_item->subscription_type) }}
                                                                </span>
                                                            </td>

                                                            {{-- Actual Price --}}
                                                            <td class="text-end">
                                                                {{ env('APP_CURRENCY') }} {{ number_format($actualPrice, 2) }}
                                                            </td>

                                                            {{-- Rider Visibility --}}
                                                            <td class="text-center">
                                                                {{ env('APP_CURRENCY') }} {{ number_format(round($actualPrice + $riderVisibilityAmount), 2) }}
                                                            </td>

                                                            {{-- Subscription Price (Calculated) --}}
                                                            <td class="text-end fw-semibold">
                                                                {{ env('APP_CURRENCY') }} {{ number_format(round($actualPrice - $discountAmount), 2) }}
                                                            </td>
                                                        </tr>

                                                    @empty
                                                        <tr wire:key="{{ $org_model->id }}">
                                                            <td class="fw-semibold">
                                                                {{ $org_model->product?->title ?? 'N/A' }}
                                                            </td>
                                                            <td colspan="4" class="text-center text-muted">
                                                                No subscription prices found
                                                            </td>
                                                        </tr>
                                                    @endforelse

                                                @empty
                                                    <tr>
                                                        <td colspan="6" class="text-center text-muted py-4">
                                                            No models assigned
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>


                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                {{-- Riders Tab --}}
                @if($activeTab=="riders")
                    <div class="row">
                    <div class="col-12">
                        <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-end flex-wrap gap-2">
                            <div style="max-width: 350px;" class="text-start text-uppercase">
                                <input type="text" wire:model="search" class="form-control border border-2 p-2 custom-input-sm"
                                placeholder="search here.." wire:keyup="FilterRider($event.target.value)">
                            </div>
                            <!-- Reset Button -->
                            <a href="javascript:void(0)" class="btn btn-danger text-white custom-input-sm"
                                wire:click="resetPageField">
                                <i class="ri-restart-line"></i>
                            </a>
                            </div>
                            <div class="table-responsive p-0 mt-2">
                            <table class="table align-items-center mb-0">
                                <thead class="table-dark">
                                <tr class="invoice-head-item">
                                    <th
                                    class="text-center text-uppercase  text-xxs font-weight-bolder opacity-7 align-middle">
                                    SL</th>
                                    <th
                                    class="text-start text-uppercase  text-xxs font-weight-bolder opacity-7 align-middle">
                                    Riders</th>
                                    <th
                                    class="text-start text-uppercase  text-xxs font-weight-bolder opacity-7 align-middle">
                                    Vehicle Model</th>
                                    <th
                                    class="text-center text-uppercase  text-xxs font-weight-bolder opacity-7 align-middle">
                                    Status</th>
                                    <th
                                    class="text-center text-uppercase  text-xxs font-weight-bolder opacity-7 align-middle">
                                    KYC Status</th>
                                    <th
                                    class="text-center text-uppercase  text-xxs font-weight-bolder opacity-7 align-middle">
                                    Dashboard</th>
                                    <th class="text-center text-uppercase  text-xxs font-weight-bolder opacity-7 align-middle">Documents</th>
                                </tr>
                                </thead>
                                <tbody>

                                @foreach($riders as $k => $v_user)
                                @php
                                $colors = ['bg-label-primary', 'bg-label-success', 'bg-label-info', 'bg-label-secondary',
                                'bg-label-danger', 'bg-label-warning'];
                                $colorClass = $colors[$k % count($colors)]; // Rotate colors based on index
                                @endphp
                                <tr>
                                    <td class="align-middle text-center">{{ $riders->firstItem()+$k }}</td>
                                    <td class="sorting_1">
                                        <div class="d-flex justify-content-start align-items-center customer-name">
                                        <div class="avatar-wrapper me-3">
                                        <div class="avatar avatar-sm">
                                            @if ($v_user->image)
                                            <img src="{{ asset($v_user->image) }}" alt="Avatar" class="rounded-circle">
                                            @else
                                            <div class="avatar-initial rounded-circle {{$colorClass}}">
                                            {{ strtoupper(substr($v_user->name, 0, 1)) }}{{ strtoupper(substr(strrchr($v_user->name, ' '), 1, 1)) }}
                                            </div>
                                            @endif
                                        </div>
                                        </div>
                                        <div class="d-flex flex-column">
                                        <a href="{{ route('organization.rider.details', $v_user->id) }}"
                                            class="text-heading"><span
                                            class="fw-medium text-truncate">{{ ucwords($v_user->name) }}</span>
                                        </a>
                                        <small class="text-truncate">{{ $v_user->email }} </small>
                                        | {{$v_user->country_code}} {{ $v_user->mobile }}

                                        <div>
                                        </div>
                                    </td>
                                    <td class="align-middle text-start">
                                    @if(optional($v_user->active_vehicle)->stock && optional($v_user->active_order)->product)
                                    {{ ucwords(optional($v_user->active_vehicle->stock)->vehicle_number) }} <br>
                                    {{ ucwords(optional($v_user->active_order->product)->title) }}
                                    @else
                                    N/A
                                    @endif
                                    </td>
                                    <td class="align-middle text-sm text-center">
                                        @if($v_user->active_vehicle)
                                        <span class="badge bg-label-success mb-0 cursor-pointer">Assigned</span>
                                        @else
                                        <span class="badge bg-label-danger mb-0 cursor-pointer">Unassigned</span>
                                        @endif
                                    </td>
                                    <td class="align-middle text-sm text-center">
                                        @if($v_user->org_is_verified=="verified")
                                            <span class="badge bg-label-success mb-0 cursor-pointer">Verified</span>
                                        @elseif($v_user->org_is_verified=="unverified")
                                            <span class="badge bg-label-warning mb-0 cursor-pointer">Unverified</span>
                                        @else
                                            <span class="badge bg-label-danger mb-0 cursor-pointer">Rejected</span>
                                        @endif
                                    </td>
                                    <td class="align-middle text-sm text-center">
                                        <div class="dropdown cursor-pointer">
                                            <span class="badge px-2 rounded-pill bg-label-secondary dropdown-toggle"
                                            id="exploreDropdown_await_{{$v_user->id}}" data-bs-toggle="dropdown"
                                            aria-expanded="false">Explore</span>
                                            <ul class="dropdown-menu" aria-labelledby="exploreDropdown_await_{{$v_user->id}}">
                                            <li><a class="dropdown-item"
                                                href="{{ route('organization.rider.details', $v_user->id) }}">Rider Details</a></li>
                                                @if($v_user->active_vehicle)
                                                    <li><a class="dropdown-item" href="{{ route('organization.vehicle.detail', optional($v_user->active_vehicle->stock)->vehicle_track_id) }}">Vehicle Details</a></li>
                                                @endif
                                            </ul>
                                        </div>
                                    </td>
                                    <td class="align-middle text-sm text-center">
                                        <button class="btn btn-outline-success waves-effect mb-0 custom-input-sm ms-2"
                                                wire:click="showCustomerDetails({{ $v_user->id}})">
                                            View
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                                </tbody>
                            </table>
                            <div class="d-flex justify-content-end mt-3 paginator">
                                {{ $riders->links() }}
                                <!-- Pagination links -->
                            </div>
                            </div>
                        </div>
                        </div>
                    </div>
                    </div>
                @endif

                {{-- Payment History Tab --}}
                @if($activeTab=="invoice")
                    <div class="row">
                    <div class="col-12">
                        <div class="card h-100 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-end flex-wrap gap-2 mb-2">
                                    <div style="max-width: 350px;" class="text-start text-uppercase">
                                        <input type="text" wire:model="search" class="form-control border border-2 p-2 custom-input-sm"
                                            placeholder="search here.." wire:keyup="FilterRider($event.target.value)">
                                    </div>
                                    <!-- Reset Button -->
                                    <a href="javascript:void(0)" class="btn btn-danger text-white custom-input-sm" wire:click="resetPageField">
                                        <i class="ri-restart-line"></i>
                                    </a>
                                </div>
                                <div class="table-responsive">
                                    <table class="table align-middle">
                                        <thead class="table-dark">
                                            <tr class="invoice-head-item">
                                                <th>#</th>
                                                <th>Invoice No</th>
                                                <th>Type</th>
                                                <th>Billing Period</th>
                                                <th>Status</th>
                                                <th>Amount</th>
                                                <th>Invoice Date</th>
                                                <th>Due Date</th>
                                                <th>Payment Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($invoices as $index => $invoice)
                                                <tr style="cursor:pointer;" data-bs-toggle="collapse" data-bs-target="#payment-invoice-{{ $invoice->id }}" aria-expanded="false" class="invoice-body-item">
                                                    <td>{{ $invoices->firstItem() + $index }}</td>
                                                    <td>{{ $invoice->invoice_number }}</td>
                                                    <td>{{ ucfirst($invoice->type) }}</td>
                                                    <td>
                                                        <i class="ri-calendar-line text-primary"></i>
                                                        {{ \Carbon\Carbon::parse($invoice->billing_start_date)->format('d M Y') }} <br>
                                                        <i class="ri-calendar-line text-danger"></i>
                                                        {{ \Carbon\Carbon::parse($invoice->billing_end_date)->format('d M Y') }}
                                                        </td>
                                                     <td>
                                                        <span class="badge 
                                                            {{ $invoice->status == 'paid' ? 'bg-success' : ($invoice->status == 'pending' ? 'bg-warning' : 'bg-danger') }}">
                                                            {{ ucfirst($invoice->status) }}
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
                                                        <div class="d-flex justify-content-between">
                                                                <span>
                                                                    {{ $invoice->payment_date 
                                                                            ? \Carbon\Carbon::parse($invoice->payment_date)->format('d M Y') 
                                                                            : '—' 
                                                                        }}
                                                                    </span>
                                                                    <a href="javascript:void(0);" data-bs-toggle="collapse" data-bs-target="#payment-invoice-{{ $invoice->id }}" aria-expanded="false">
                                                                        <span class="control">
                                                                            <i class="bi bi-chevron-down"></i> <!-- Bootstrap icon example -->
                                                                        </span>
                                                                    </a>
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
                                                                    <td colspan="3" width="15%">
                                                                        <strong>Total Price:</strong> {{env('APP_CURRENCY')}}{{ number_format($item->total_price, 2) }}
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
                                                                        // Sort dates to ensure correct range
                                                                        $dates = collect($detail['dates'])->sort()->values();
                                                                        $startDate = \Carbon\Carbon::parse($dates->first())->format('d M Y');
                                                                        $endDate   = \Carbon\Carbon::parse($dates->last())->format('d M Y');

                                                                        $totalDays = count($dates);
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
                                                                        <td colspan="3">
                                                                            {{ env('APP_CURRENCY') }}{{ number_format($amountPerDay, 2) }}
                                                                            × {{ $totalDays }} = 
                                                                            <strong>{{ env('APP_CURRENCY') }}{{ number_format($totalAmount, 2) }}</strong>
                                                                        </td>
                                                                    </tr>
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
                                    </table>
                                    <div class="mt-2">
                                        {{ $invoices->links() }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    </div>
                @endif
                @if($activeTab=="payment")
                    <div class="row">
                    <div class="col-12">
                        <div class="card h-100 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-end flex-wrap gap-2 mb-2">
                                    <div style="max-width: 350px;" class="text-start text-uppercase">
                                        <input type="text" wire:model="search" class="form-control border border-2 p-2 custom-input-sm"
                                            placeholder="search here.." wire:keyup="FilterRider($event.target.value)">
                                    </div>
                                    <!-- Reset Button -->
                                    <a href="javascript:void(0)" class="btn btn-danger text-white custom-input-sm" wire:click="resetPageField">
                                        <i class="ri-restart-line"></i>
                                    </a>
                                </div>
                                <div class="table-responsive">
                                    <table class="table align-middle">
                                        <thead class="table-dark">
                                            <tr class="invoice-head-item">
                                                <th>#</th>
                                                <th>Organization</th>
                                                <th>Invoice</th>
                                                <th>Payment Method</th>
                                                <th>Status</th>
                                                <th>ICICI Txn No</th>
                                                <th>Amount</th>
                                                <th>Payment Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($payments as $index => $payment)
                                                <tr>
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
                                    </table>

                                    <div class="mt-2">
                                        {{ $payments->links() }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    </div>
                @endif

            </div>
           
        </div>
    </div>
    <div class="loader-container" wire:loading>
      <div class="loader"></div>
    </div>

    @if($isModalOpen)
        <div class="side-modal {{ $isModalOpen ? 'open' : '' }}">
            @if($selectedCustomer)
                <div class="m-0 lh-1 border-bottom template-customizer-header position-relative py-4">
                    <div class="d-flex justify-content-start align-items-center customer-name">
                        <div class="avatar-wrapper me-3">
                            <div class="avatar avatar-sm">
                            @if ($selectedCustomer->image)
                            <img src="{{ asset($selectedCustomer->image) }}" alt="Avatar" class="rounded-circle">
                            @else
                            <div class="avatar-initial rounded-circle {{$colorClass}}">
                                {{ strtoupper(substr($selectedCustomer->name, 0, 1)) }}{{ strtoupper(substr(strrchr($selectedCustomer->name, ' '), 1, 1)) }}
                            </div>
                            @endif
                            </div>
                        </div>
                        <div class="d-flex flex-column">
                            <a href="javascript:vid(0)" class="text-heading"><span
                                class="fw-medium text-truncate">{{ ucwords($selectedCustomer->name) }}</span>
                            </a>
                            <small class="text-truncate">{{ $selectedCustomer->email }} | {{$selectedCustomer->country_code}}
                            {{ $selectedCustomer->mobile }}</small>
                            <div>
                            </div>
                            <div class="d-flex align-items-center gap-2 position-absolute end-0 top-0 mt-6 me-5">
                            <a href="javascript:void(0)" wire:click="closeModal"
                                class="template-customizer-close-btn fw-light text-body" tabindex="-1">
                                <i class="ri-close-line ri-24px"></i>
                            </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="side-modal-content">
                    
                    <div class="nav-align-top">
                        <ul class="nav nav-tabs nav-fill" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button type="button" class="nav-link waves-effect modal-nav active" role="tab" data-bs-toggle="tab"
                            data-bs-target="#navs-justified-overview" aria-controls="navs-justified-overview" aria-selected="false"
                            tabindex="-1">
                            <span class="d-none d-sm-block">Overview
                                </span>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button type="button" class="nav-link waves-effect" role="tab" data-bs-toggle="tab"
                            data-bs-target="#navs-justified-history" aria-controls="navs-justified-history" aria-selected="false"
                            tabindex="-1">
                            <span class="d-none d-sm-block">
                                History
                                </span>
                            </button>
                        </li>
                        </ul>
                    </div>
                    <div class="tab-content p-0 mt-6">
                        <div class="tab-pane fade active show" id="navs-justified-overview" role="tabpanel">

                            {{-- Driving Licence --}}
                            <div style="border-bottom: 1px solid #8d58ff;" class="mb-3">
                                <div class="d-flex align-items-center mb-3">
                                    <!-- Icon -->
                                    <div class="avatar me-3" style=" width:1.5rem; height: 1.5rem;">
                                    <div class="avatar-initial rounded
                                            bg-label-dark document_type">
                                        <i class="ri-roadster-line ri-15px"></i>
                                    </div>
                                    </div>
                                    <!-- Document Name -->
                                    <div>
                                        <span class="fw-medium text-truncate text-dark">Driving Licence</span>
                                    </div>
                                </div>
                                @if($selectedCustomer->driving_licence_status>0)
                                    <div class="d-flex">
                                    <div class="col-6">
                                        <div class="card academy-content shadow-none border mx-2" style="width:150px">
                                            <div class="p-2">
                                            <div class="cursor-pointer">
                                            <img src="{{asset($selectedCustomer->driving_licence_front)}}" alt="" style="max-width: 150px;max-height: 130px; width: 100%;">
                                            </div>
                                            <div class="text-center fw-medium text-truncate">Front</div>
                                            </div>
                                        </div>
                                    </div>
                                        <div class="col-6">
                                            <div class="card academy-content shadow-none border mx-2" style="width:150px">
                                                <div class="p-2">
                                                <div class="cursor-pointer">
                                                <img src="{{asset($selectedCustomer->driving_licence_back)}}" alt="" style="max-width: 150px;max-height: 130px; width: 100%;">
                                                </div>
                                                <div class="text-center fw-medium text-truncate">Back</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex my-4">
                                        <div class="col-6 text-center cursor-pointer">
                                            <span class="badge rounded-pill bg-label-secondary" wire:click="OpenPreviewImage('{{asset($selectedCustomer->driving_licence_front)}}','{{asset($selectedCustomer->driving_licence_back)}}','Driving Licence')">Preview</span>
                                        </div>
                                        <div class="col-6 text-center cursor-pointer">
                                            @if($selectedCustomer->driving_licence_status==2)
                                                <span class="badge rounded-pill bg-label-success">
                                                    <i class="ri-check-line"></i> Approved
                                                </span>
                                            @elseif($selectedCustomer->driving_licence_status==3)
                                                <span class="badge rounded-pill bg-label-danger"><i class="ri-close-line"></i> Rejected</span>
                                            @else
                                                <span class="badge rounded-pill bg-label-warning">
                                                    Pending
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                @else
                                    <div class="alert alert-danger">
                                        Driving licence not uploaded
                                    </div>
                                @endif
                            </div>

                            {{-- Aadhar Card --}}
                            <div style="border-bottom: 1px solid #8d58ff;" class="mb-3">
                                @if($selectedCustomer->aadhar_card_status>0)
                                    <div class="d-flex align-items-center mb-3">
                                        <!-- Icon -->
                                        <div class="avatar me-3" style=" width:1.5rem; height: 1.5rem;">
                                        <div class="avatar-initial rounded
                                                bg-label-dark document_type">
                                            <i class="ri-passport-line ri-15px"></i>
                                        </div>
                                        </div>
                                        <!-- Document Name -->
                                        <div>
                                            <span class="fw-medium text-truncate text-dark">Aadhar Card</span>
                                        </div>
                                    </div>
                                    <div class="d-flex">
                                        <div class="col-6">
                                            <div class="card academy-content shadow-none border mx-2" style="width:150px">
                                                <div class="p-2">
                                                    <div class="cursor-pointer">
                                                        {{$selectedCustomer->aadhar_number?$selectedCustomer->aadhar_number:"N/A"}}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex my-4">
                                        <div class="col-12 text-center cursor-pointer">
                                            @if($selectedCustomer->aadhar_card_status==2)
                                                <span class="badge rounded-pill bg-label-success">
                                                    <i class="ri-check-line"></i> Approved
                                                </span>
                                            @else
                                                <span class="badge rounded-pill bg-label-warning">
                                                    Pending
                                                </span>
                                            @endif
                                        </div>
                                        <div class="col-6 text-center cursor-pointer">
                                            @if($selectedCustomer->aadhar_card_status==2)
                                                <a href="{{route('digilocker.aadhar.download',$selectedCustomer->id)}}" target="_blank">
                                                    <span class="badge rounded-pill bg-label-success">
                                                        <i class="ri-check-line"></i> Download PDF
                                                    </span>
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                @else
                                    <div class="alert alert-danger">
                                        Aadhar card not verified.
                                    </div>
                                @endif
                            </div>

                            {{-- Pan Card --}}
                            <div style="border-bottom: 1px solid #8d58ff;" class="mb-3">
                                @if($selectedCustomer->pan_card_status>0)
                                    <div class="d-flex align-items-center mb-3">
                                        <!-- Icon -->
                                        <div class="avatar me-3" style=" width:1.5rem; height: 1.5rem;">
                                        <div class="avatar-initial rounded
                                                bg-label-dark document_type">
                                            <i class="ri-passport-line ri-15px"></i>
                                        </div>
                                        </div>
                                        <!-- Document Name -->
                                        <div>
                                            <span class="fw-medium text-truncate text-dark">Pan Card</span>
                                        </div>
                                    </div>
                                    <div class="d-flex">
                                        <div class="col-6">
                                            <div class="card academy-content shadow-none border mx-2" style="width:150px">
                                                <div class="p-2">
                                                <div class="cursor-pointer">
                                                <img src="{{asset($selectedCustomer->pan_card_front)}}" alt="" style="max-width: 150px;max-height: 130px; width: 100%;">
                                                </div>
                                                <div class="text-center fw-medium text-truncate">Front</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="card academy-content shadow-none border mx-2" style="width:150px">
                                                <div class="p-2">
                                                    <div class="cursor-pointer">
                                                    <img src="{{asset($selectedCustomer->pan_card_back)}}" alt="" style="max-width: 150px;max-height: 130px; width: 100%;">
                                                    </div>
                                                    <div class="text-center fw-medium text-truncate">Back</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex my-4">
                                        <div class="col-6 text-center cursor-pointer">
                                            <span class="badge rounded-pill bg-label-secondary" wire:click="OpenPreviewImage('{{asset($selectedCustomer->pan_card_front)}}','{{asset($selectedCustomer->pan_card_back)}}','Pan Card')"> Preview</span>
                                        </div>
                                        <div class="col-6 text-center cursor-pointer">
                                            @if($selectedCustomer->pan_card_status==2)
                                                <span class="badge rounded-pill bg-label-success">
                                                    <i class="ri-check-line"></i> Approved
                                                </span>
                                            @elseif($selectedCustomer->pan_card_status==3)
                                                <span class="badge rounded-pill bg-label-danger"><i class="ri-close-line"></i> Rejected</span>
                                            @else
                                                <span class="badge rounded-pill bg-label-warning">
                                                    Pending
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                @else
                                    <div class="alert alert-danger">
                                        Pan card not uploaded
                                    </div>
                                @endif
                            </div>

                            {{-- Address Proff --}}
                            <div style="border-bottom: 1px solid #8d58ff;" class="mb-3">
                                @if($selectedCustomer->current_address_proof_status>0)
                                    <div class="d-flex align-items-center mb-3">
                                    <!-- Icon -->
                                    <!-- Icon -->
                                    <div class="avatar me-3" style=" width:1.5rem; height: 1.5rem;">
                                        <div class="avatar-initial rounded
                                                bg-label-dark document_type">
                                        <i class="ri-bank-line ri-15px"></i>
                                        </div>
                                    </div>
                                    <!-- Document Name -->
                                    <div>
                                        <span class="fw-medium text-truncate text-dark">Current Address Proof</span>
                                    </div>
                                    </div>
                                    <div class="d-flex">
                                    <div class="col-6">
                                        <div class="card academy-content shadow-none border mx-2" style="width:150px">
                                        <div class="p-2">
                                            <div class="cursor-pointer">
                                            <img src="{{asset($selectedCustomer->current_address_proof_front)}}" alt=""
                                                style="max-width: 150px;max-height: 130px; width: 100%;">
                                            </div>
                                            <div class="text-center fw-medium text-truncate">Front</div>
                                        </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="card academy-content shadow-none border mx-2" style="width:150px">
                                        <div class="p-2">
                                            <div class="cursor-pointer">
                                            <img src="{{asset($selectedCustomer->current_address_proof_back)}}" alt=""
                                                style="max-width: 150px;max-height: 130px; width: 100%;">
                                            </div>
                                            <div class="text-center fw-medium text-truncate">Back</div>
                                        </div>
                                        </div>
                                    </div>
                                    </div>
                                    <div class="d-flex my-4">
                                        <div class="col-6 text-center cursor-pointer">
                                            <span class="badge rounded-pill bg-label-secondary" wire:click="OpenPreviewImage('{{asset($selectedCustomer->current_address_proof_front)}}','{{asset($selectedCustomer->current_address_proof_back)}}','Current Address Proof')"> Preview</span>
                                        </div>
                                        <div class="col-6 text-center cursor-pointer">
                                            @if($selectedCustomer->current_address_proof_status==2)
                                                <span class="badge rounded-pill bg-label-success">
                                                    <i class="ri-check-line"></i> Approved
                                                </span>
                                            @elseif($selectedCustomer->current_address_proof_status==3)
                                                <span class="badge rounded-pill bg-label-danger"><i class="ri-close-line"></i>
                                                Rejected</span>
                                            @else
                                                <span class="badge rounded-pill bg-label-warning">
                                                    Pending
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                @else
                                    <div class="alert alert-danger">
                                        Current address proof not uploaded
                                    </div>
                                @endif
                            </div>

                            {{-- Passbook --}}
                            <div style="border-bottom: 1px solid #8d58ff;" class="mb-3">
                                @if($selectedCustomer->passbook_status>0)
                                    <div class="d-flex align-items-center mb-3">
                                    <div class="avatar me-3" style=" width:1.5rem; height: 1.5rem;">
                                            <div class="avatar-initial rounded
                                                    bg-label-dark document_type">
                                            <i class="ri-home-line ri-15px"></i>
                                            </div>
                                        </div>
                                    <!-- Document Name -->
                                    <div>
                                        <span class="fw-medium text-truncate text-dark">Passbook/Cancelled Cheque</span>
                                    </div>
                                    </div>
                                    <div class="d-flex">
                                        <div class="col-12">
                                            <div class="card academy-content shadow-none border mx-2" style="width:150px">
                                            <div class="p-2">
                                                <div class="cursor-pointer">
                                                <img src="{{asset($selectedCustomer->passbook_front)}}" alt=""
                                                    style="max-width: 150px;max-height: 130px; width: 100%;">
                                                </div>
                                            </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex my-4">
                                        <div class="col-6 text-center cursor-pointer">
                                            <span class="badge rounded-pill bg-label-secondary" wire:click="OpenPreviewImage('{{asset($selectedCustomer->passbook_front)}}','','Passbook')"> Preview</span>
                                        </div>
                                        <div class="col-6 text-center cursor-pointer">
                                            @if($selectedCustomer->passbook_status==2)
                                                <span class="badge rounded-pill bg-label-success">
                                                <i class="ri-check-line"></i> Approved
                                                </span>
                                            @elseif($selectedCustomer->passbook_status==3)
                                                <span class="badge rounded-pill bg-label-danger"><i class="ri-close-line"></i>
                                                Rejected</span>
                                            @else
                                                <span class="badge rounded-pill bg-label-warning">
                                                Pending
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                @else
                                    <div class="alert alert-danger">
                                        Passbook/Cancelled cheque not uploaded
                                    </div>
                                @endif
                            </div>

                            {{-- Rider Profile --}}
                            <div style="border-bottom: 1px solid #8d58ff;" class="mb-3">
                                @if($selectedCustomer->profile_image_status>0)
                                    <div class="d-flex align-items-center mb-3">
                                    <!-- Icon -->
                                    <div class="avatar me-3" style=" width:1.5rem; height: 1.5rem;">
                                        <div class="avatar-initial rounded
                                                bg-label-dark document_type">
                                        <i class="ri-user-line ri-16px text-dark"></i>
                                        </div>
                                    </div>
                                    <!-- Document Name -->
                                    <div>
                                        <span class="fw-medium text-truncate text-dark">Rider Profle Image</span>
                                    </div>
                                    </div>
                                    <div class="d-flex">
                                        <div class="col-12">
                                            <div class="card academy-content shadow-none border mx-2" style="width:150px">
                                                <div class="p-2">
                                                    <div class="cursor-pointer">
                                                    <img src="{{asset($selectedCustomer->profile_image)}}" alt=""
                                                        style="max-width: 150px;max-height: 130px; width: 100%;">
                                                    </div>
                                                    {{-- <div class="text-center fw-medium text-truncate">Front</div> --}}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex my-4">
                                        <div class="col-6 text-center cursor-pointer">
                                            <span class="badge rounded-pill bg-label-secondary" wire:click="OpenPreviewImage('{{asset($selectedCustomer->profile_image)}}','','Profile Image')"> Preview</span>
                                        </div>
                                        <div class="col-6 text-center cursor-pointer">
                                            @if($selectedCustomer->profile_image_status==2)
                                                <span class="badge rounded-pill bg-label-success">
                                                <i class="ri-check-line"></i> Approved
                                                </span>
                                            @elseif($selectedCustomer->profile_image_status==3)
                                                <span class="badge rounded-pill bg-label-danger"><i class="ri-close-line"></i>
                                                Rejected</span>
                                            @else
                                                <span class="badge rounded-pill bg-label-warning">
                                                Pending
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                @else
                                    <div class="alert alert-danger">
                                        Profile Image not uploaded
                                    </div>
                                @endif
                            </div>

                            <div class="text-center">
                                @if($selectedCustomer->org_is_verified=="verified")
                                <button type="button" class="btn btn-success text-white mb-0 custom-input-sm ms-2">
                                    KYC VERIFIED
                                </button>
                                @endif
                                @if($selectedCustomer->org_is_verified=="unverified")
                                    <button type="button" class="btn btn-warning text-white mb-0 custom-input-sm ms-2">
                                    KYC UNVERIFIED
                                    </button>
                                @endif
                                @if($selectedCustomer->org_is_verified=="rejected")
                                    <button type="button" class="btn btn-danger text-white mb-0 custom-input-sm ms-2">
                                        KYC REJECTED
                                    </button>
                                @endif
                            </div>
                            @if(session()->has('modal_message'))
                                <div class="alert alert-success" id="modalflashMessage">
                                    {{ session('modal_message') }}
                                </div>
                            @endif
                            @if(session()->has('error_kyc_message'))
                                <div class="alert alert-danger">
                                    {{ session('error_kyc_message') }}
                                </div>
                            @endif
                            <div style="margin-bottom: 20px;" class="text-start text-uppercase">
                                    <label for="startDate" class="form-label small mb-1">Update KYC Status</label>
                                <select
                                    class="form-select border border-2 p-2 custom-input-sm" wire:model="model" wire:change="VerifyKyc($event.target.value, {{$selectedCustomer->id}})">
                                    <option value="" selected hidden>Select one</option>
                                    <option value="verified" {{$selectedCustomer->org_is_verified=="verified"?"selected":""}}>KYC Verified</option>
                                    <option value="unverified" {{$selectedCustomer->org_is_verified=="unverified"?"selected":""}}>KYC Unverified</option>
                                    <option value="rejected" {{$selectedCustomer->org_is_verified=="rejected"?"selected":""}}>KYC Rejected</option>
                                </select>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="navs-justified-history" role="tabpanel">
                            <ul class="timeline pb-0 mb-0">
                                @if(count($selectedCustomer->doc_logs)>0)
                                    @foreach ($selectedCustomer->doc_logs->sortByDesc('id') as $logs)
                                    <li class="timeline-item timeline-item-transparent border-primary">
                                        <span class="timeline-point timeline-point-primary"></span>
                                        <div class="timeline-event">
                                            <div class="timeline-header mb-1">
                                            <h6 class="mb-0">{{ucwords($logs->document_type)}} | {{ucwords($logs->status)}}</h6>
                                            <small class="text-muted">{{ date('d M y h:i A', strtotime($logs->created_at)) }}</small>
                                            </div>
                                            @if($logs->remarks)
                                                <code>Remarks</code>
                                                <p class="mt-1 mb-3"><small>{{$logs->remarks}}</small></p>
                                            @endif
                                        </div>
                                        </li>
                                    @endforeach
                                @else
                                    <div class="alert alert-danger">
                                    Sorry! data not found!
                                    </div>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    @endif
     <!-- Overlay -->
    @if($isModalOpen)
        <div class="overlay" wire:click="closeModal"></div>
    @endif
     @if ($isPreviewimageModal)
        <div class="modal fade show d-block" tabindex="-1" role="dialog" style="background: rgba(0, 0, 0, 0.5);z-index: 99999;">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ $document_type }}</h5>
                        <button type="button" class="btn-close" wire:click="closePreviewImage"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <div class="card academy-content shadow-none border mx-2">
                                <div class="p-2">
                                    <div class="cursor-pointer">
                                        <img src="{{$preview_front_image}}" alt="" width="100%">
                                    </div>
                                </div>
                            </div>
                            <div class="card academy-content shadow-none border mx-2 my-2">
                                <div class="p-2">
                                    <div class="cursor-pointer">
                                        <img src="{{$preview_back_image}}" alt="" width="100%">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

</div>

@section('page-script')
<script>
    window.addEventListener('update-url', event => {
        let type = event.detail[0].type;
        let newUrl = new URL(window.location.href);
        newUrl.searchParams.set('type', type);
        window.history.pushState({}, '', newUrl);
    });
    window.addEventListener('payment_redirect_url', event => {
        let redirect_url = event.detail[0].redirect_url;
        let counter = 3;
        const interval = setInterval(() => {
            counter--;
            if (counter <= 0) {
                clearInterval(interval);
                window.location.href = redirect_url;
                }
        }, 1000);
    });

</script>
@endsection


