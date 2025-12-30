
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
                                    Action</th>
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


