<div class="row mb-4">
    <div class="col-lg-12 d-flex justify-content-between">
        <div>
            <h5 class="mb-0">Model Management</h5>
            <div>
                <small class="text-dark fw-medium">Dashboard</small>
                <small class="text-light fw-medium arrow">Subscriptions</small>
            </div>
        </div>

        @if($active_tab == 1)
            <button type="button" class="btn btn-primary" wire:click="ActiveCreateTab(2)">
                <i class="ri-add-line ri-16px me-1"></i> Create Subscription
            </button>
        @else
            <button type="button" class="btn btn-dark" wire:click="ActiveCreateTab(1)">
                <i class="ri-arrow-go-back-line"></i> Back
            </button>
        @endif
    </div>

    @if($active_tab == 1)
    {{-- LISTING SECTION --}}
    <div class="col-lg-12 col-md-12 my-4">
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
                        </div>
                        <div class="row">

                           {{-- City Filter --}}
                           <div class="col-lg-3">
                                <div wire:ignore class="chosen-floating">
                                    <select id="city_filter_search" class="form-select">
                                        <option value=""></option>

                                        @foreach($filterCities as $city)
                                            <option value="{{ $city->id }}" @if($filter_city_id == $city->id) selected @endif>
                                                {{ $city->name }}
                                                @if($city->state)
                                                    ({{ $city->state->name }})
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>

                                    <label class="chosen-label">
                                        City / State
                                    </label>
                                </div>
                            </div>
                            {{-- Branch Filter --}}
                           <div class="col-lg-3">
                                <div wire:ignore
                                    wire:key="branch-search-filter-container-{{ count($filterBranches) }}"
                                    class="chosen-floating">

                                    <select id="branch_filter_search" class="form-select">
                                        <option value=""></option>

                                        @foreach($filterBranches as $branch)
                                            <option value="{{ $branch->id }}"
                                                @if($filter_branch_id == $branch->id) selected @endif>
                                                {{ $branch->name }}
                                            </option>
                                        @endforeach
                                    </select>

                                    <label class="chosen-label">
                                        Branch
                                    </label>
                                </div>
                            </div>
                            {{-- Type --}}
                            <div class="col-lg-2">
                                <div class="form-floating form-floating-outline mb-5 fv-plugins-icon-container">
                                    <select wire:model.live="customerType"
                                        class="form-control border border-2 p-2">
                                        <option value="">All Type</option>
                                        <option value="B2B">B2B</option>
                                        <option value="B2C">B2C</option>
                                    </select>
                                    <label>Type</label>
                                </div>
                            </div>

                            {{-- Asset --}}
                            <div class="col-lg-4">
                                <div class="form-floating form-floating-outline mb-5 fv-plugins-icon-container">
                                    <select wire:model.live="asset"
                                        class="form-control border border-2 p-2">
                                        <option value="">All Assets</option>

                                        @foreach($models as $item)
                                            <option value="{{ $item->id }}">
                                                {{ $item->category ? $item->category->title.' | ' : '' }}
                                                {{ $item->title }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <label>Assets</label>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="card-body px-0 pb-2">
                        <div class="table-responsive p-0">
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 align-middle">SL</th>
                                        <th class="text-left text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 align-middle">Type / Branch</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 align-middle">Validity In Days</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 align-middle">Deposit Amount</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 align-middle">Rental Amount</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 align-middle px-4">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($subscriptions as $k => $sub_item)
                                        <tr>
                                            <td class="align-middle text-center">{{ $k + 1 }}</td>
                                            <td class="align-middle text-left">
                                                <div class="d-flex flex-column">
                                                    <a href="javascript:void(0)" class="text-heading">
                                                        <span class="fw-medium text-truncate">{{ ucwords($sub_item->subscription_type) }}</span>
                                                    </a>
                                                    <small class="text-truncate"><strong>Product:</strong> {{optional($sub_item->product)->title}}</small>
                                                    <small class="text-muted text-truncate"><strong>Branch:</strong> {{optional($sub_item->branch)->name ?? 'N/A'}}</small>
                                                    <span class="badge rounded-pill badge-center w-px-40 bg-label-{{ $sub_item->customer_type == 'B2B' ? 'primary' : 'danger' }} mt-1">{{$sub_item->customer_type}}</span>
                                                </div>
                                            </td>
                                            <td class="align-middle text-center">{{ $sub_item->duration }} Days</td>
                                            <td class="align-middle text-center">{{env('APP_CURRENCY')}}{{ $sub_item->deposit_amount }} </td>
                                            <td class="align-middle text-center">{{env('APP_CURRENCY')}}{{ $sub_item->rental_amount }} </td>
                                            <td class="align-middle text-end px-4" width="200">
                                                <div class="d-flex justify-content-center align-items-center">
                                                    <div class="form-check form-switch mb-0 mt-1">
                                                        <input class="form-check-input ms-auto" type="checkbox" id="flexSwitchCheckDefault{{ $sub_item->id }}" wire:click="toggleStatus({{ $sub_item->id }})" @if($sub_item->status) checked @endif>
                                                    </div>
                                                    <button wire:click="edit({{ $sub_item->id }})" class="btn btn-sm btn-icon edit-record btn-text-secondary rounded-pill waves-effect btn-sm" title="Edit"><i class="ri-edit-box-line ri-20px text-info"></i></button>
                                                    @if($sub_item->orders()->count()==0)
                                                        <button wire:click="destroy({{ $sub_item->id }})" class="btn btn-sm btn-icon delete-record btn-text-secondary rounded-pill waves-effect btn-sm" title="Delete"> <i class="ri-delete-bin-7-line ri-20px text-danger"></i> </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @elseif($active_tab == 2)
    {{-- CREATE FORM --}}
    <div class="col-lg-12 col-md-12 my-4">
        <div class="row">
            <div class="col-12">             
                <div class="card my-4">       
                    <div class="card-body px-0 pb-2 mx-4">
                        <div class="d-flex justify-content-between mb-3">
                            <h5>New Subscription</h5>  
                        </div>
                        <form wire:submit.prevent="store">
                            <div class="row">

                                    {{-- City --}}
                                    <div class="col-md-6">
                                        <div wire:ignore class="chosen-floating">
                                            <select id="city_filter_create" class="form-select">
                                                <option value=""></option>

                                                @foreach($cities as $city)
                                                    <option value="{{ $city->id }}"
                                                        {{ $city_id == $city->id ? 'selected' : '' }}>
                                                        {{ $city->name }}
                                                        @if($city->state)
                                                            ({{ $city->state->name }})
                                                        @endif
                                                    </option>
                                                @endforeach
                                            </select>

                                            <label class="chosen-label">
                                                City / State <span class="text-danger">*</span>
                                            </label>
                                        </div>
                                    </div>

                                    {{-- Branch Create --}}
                                    <div class="col-md-6">
                                        <div wire:ignore
                                            wire:key="branch-create-container-{{ count($branches) }}"
                                            class="chosen-floating">

                                            <select id="branch_filter_create" class="form-select">
                                                <option value=""></option>

                                                @foreach($branches as $branch)
                                                    <option value="{{ $branch->id }}"
                                                        {{ $branch_id == $branch->id ? 'selected' : '' }}>
                                                        {{ $branch->name }}
                                                    </option>
                                                @endforeach
                                            </select>

                                            <label class="chosen-label">
                                                Branch <span class="text-danger">*</span>
                                            </label>
                                        </div>

                                        @error('branch_id')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                <div class="col-md-6">
                                    <div class="form-floating form-floating-outline mb-5 fv-plugins-icon-container mt-4">
                                        <select wire:model="subscription_type" class="form-control border border-2 p-2" wire:change="GetDuration($event.target.options[$event.target.selectedIndex].dataset.duration)">
                                            <option value="">Type</option>
                                            <option value="daily" data-duration="1">Daily</option>
                                            <option value="weekly" data-duration="7">Weekly</option>
                                            <option value="monthly" data-duration="30">Monthly</option>
                                            <option value="quarterly" data-duration="90">Quarterly</option>
                                            <option value="yearly" data-duration="365">Yearly</option>
                                        </select>
                                        <label>Subscription Type </label>
                                        @error('subscription_type') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating form-floating-outline mb-5 fv-plugins-icon-container mt-4">
                                        <select wire:model="customer_type" class="form-control border border-2 p-2" wire:change="GetCustomerType($event.target.value)">
                                            <option value="">Customer Type</option>
                                            <option value="B2B">B2B</option>
                                            <option value="B2C">B2C</option>
                                        </select>
                                        <label>Customer Type </label>
                                        @error('customer_type') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating form-floating-outline mb-5 fv-plugins-icon-container mt-4">
                                        <select wire:model="model" class="form-control border border-2 p-2">
                                            <option value="" selected hidden>Select model</option>
                                            @foreach($models as $item)
                                                <option value="{{ $item->id }}">{{$item->category?$item->category->title.' | ':""}}{{ $item->title }}</option>
                                            @endforeach
                                        </select>
                                        <label>Model </label>
                                        @error('model') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating form-floating-outline mb-5 fv-plugins-icon-container mt-4">
                                        <input type="number" wire:model="deposit_amount" class="form-control border border-2 p-2" placeholder="{{env('APP_CURRENCY')}}1000.00" {{ $customer_type=="B2C" ? '' : 'disabled' }}>
                                        <label>Deposit Amount</label>
                                        @error('deposit_amount') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating form-floating-outline mb-5 fv-plugins-icon-container mt-4">
                                        <input type="number" wire:model="rental_amount" class="form-control border border-2 p-2" placeholder="{{env('APP_CURRENCY')}}100.00">
                                        <label>Rental Amount</label>
                                        @error('rental_amount') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="text-end">
                                <button type="submit" class="btn btn-secondary btn-sm add-new btn-primary waves-effect waves-light">
                                    Create Subscription
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @elseif($active_tab == 3)
    {{-- EDIT FORM --}}
    <div class="col-lg-12 col-md-12 my-4">
        <div class="row">
            <div class="col-12">             
                <div class="card my-4">       
                    <div class="card-body px-0 pb-2 mx-4">
                        <div class="d-flex justify-content-between mb-3">
                            <h5>Update Subscription</h5>  
                        </div>
                        <form wire:submit.prevent="update">
                            <div class="row">
                                {{-- City --}}
                                <div class="col-md-6">
                                    <div wire:ignore class="chosen-floating">
                                        <select id="city_filter_edit" class="form-select">
                                            <option value=""></option>

                                            @foreach($cities as $city)
                                                <option value="{{ $city->id }}"
                                                    {{ $city_id == $city->id ? 'selected' : '' }}>
                                                    {{ $city->name }}
                                                    @if($city->state)
                                                        ({{ $city->state->name }})
                                                    @endif
                                                </option>
                                            @endforeach
                                        </select>

                                        <label class="chosen-label">
                                            City / State <span class="text-danger">*</span>
                                        </label>
                                    </div>
                                </div>
                                {{-- Branch Edit --}}
                               <div class="col-md-6">
                                    <div wire:ignore
                                        wire:key="branch-edit-container-{{ count($branches) }}"
                                        class="chosen-floating">

                                        <select id="branch_filter_edit" class="form-select">
                                            <option value=""></option>

                                            @foreach($branches as $branch)
                                                <option value="{{ $branch->id }}"
                                                    {{ $branch_id == $branch->id ? 'selected' : '' }}>
                                                    {{ $branch->name }}
                                                </option>
                                            @endforeach
                                        </select>

                                        <label class="chosen-label">
                                            Branch <span class="text-danger">*</span>
                                        </label>
                                    </div>

                                    @error('branch_id')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <div class="form-floating form-floating-outline mb-5 fv-plugins-icon-container mt-4">
                                        <select wire:model="subscription_type" class="form-control border border-2 p-2" wire:change="GetDuration($event.target.options[$event.target.selectedIndex].dataset.duration)">
                                            <option value="">Type</option>
                                            <option value="daily" data-duration="1">Daily</option>
                                            <option value="weekly" data-duration="7">Weekly</option>
                                            <option value="monthly" data-duration="30">Monthly</option>
                                            <option value="quarterly" data-duration="90">Quarterly</option>
                                            <option value="yearly" data-duration="365">Yearly</option>
                                        </select>
                                        <label>Subscription Type </label>
                                        @error('subscription_type') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating form-floating-outline mb-5 fv-plugins-icon-container mt-4">
                                        <select wire:model="customer_type" class="form-control border border-2 p-2" wire:change="GetCustomerType($event.target.value)">
                                            <option value="">Customer Type</option>
                                            <option value="B2B">B2B</option>
                                            <option value="B2C">B2C</option>
                                        </select>
                                        <label>Customer Type </label>
                                        @error('customer_type') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating form-floating-outline mb-5 fv-plugins-icon-container mt-4">
                                        <select wire:model="model" class="form-control border border-2 p-2">
                                            <option value="" selected hidden>Select model</option>
                                            @foreach($models as $item)
                                                <option value="{{ $item->id }}">{{$item->category?$item->category->title.' | ':""}}{{ $item->title }}</option>
                                            @endforeach
                                        </select>
                                        <label>Model </label>
                                        @error('model') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating form-floating-outline mb-5 fv-plugins-icon-container mt-4">
                                        <input type="number" wire:model="deposit_amount" class="form-control border border-2 p-2" placeholder="{{env('APP_CURRENCY')}}1000.00" {{ $customer_type=="B2C" ? '' : 'disabled' }}>
                                        <label>Deposit Amount</label>
                                        @error('deposit_amount') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating form-floating-outline mb-5 fv-plugins-icon-container mt-4">
                                        <input type="number" wire:model="rental_amount" class="form-control border border-2 p-2" placeholder="{{env('APP_CURRENCY')}}100.00">
                                        <label>Rental Amount</label>
                                        @error('rental_amount') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="text-end">
                                <button type="submit" class="btn btn-secondary btn-sm add-new btn-primary waves-effect waves-light">
                                    Update Subscription
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
    <div class="loader-container" wire:loading>
        <div class="loader"></div>
    </div>
</div>
@section('page-script')
<style>
    .chosen-floating {
        position: relative;
    }

    .chosen-floating .chosen-container {
        width: 100% !important;
    }

    .chosen-floating .chosen-single {
        height: 48px !important;
        line-height: 48px !important;
        border: 2px solid #d9dee3 !important;
        border-radius: 7px !important;
        background: #fff !important;
        padding-left: 12px !important;
        box-shadow: none !important;
    }

    .chosen-floating .chosen-label {
        position: absolute;
        top: -10px;
        left: 12px;
        background: #fff;
        padding: 0 5px;
        font-size: 12px;
        color: #666;
        z-index: 10;
        pointer-events: none;
    }

    .chosen-floating .chosen-search input {
        height: 35px !important;
    }
</style>

<script>
    var jq = $.noConflict();

    function initSubscriptionChosen() {
        // --- LISTING / SEARCH TAB FILTERS ---
        if (jq("#city_filter_search").length) {
            jq("#city_filter_search").chosen({ width: "100%", search_contains: true })
                .off("change")
                .on("change", function () {
                    @this.set('filter_city_id', jq(this).val());
                });
        }

        if (jq("#branch_filter_search").length) {
            jq("#branch_filter_search").chosen({ width: "100%", search_contains: true })
                .off("change")
                .on("change", function () {
                    @this.set('filter_branch_id', jq(this).val());
                });
        }

        // --- CREATE TAB ---
        if (jq("#city_filter_create").length) {
            jq("#city_filter_create").chosen({ width: "100%", search_contains: true })
                .off("change")
                .on("change", function () {
                    @this.set('city_id', jq(this).val());
                });
        }

        if (jq("#branch_filter_create").length) {
            jq("#branch_filter_create").chosen({ width: "100%", search_contains: true })
                .off("change")
                .on("change", function () {
                    @this.set('branch_id', jq(this).val());
                });
        }

        // --- EDIT TAB ---
        if (jq("#city_filter_edit").length) {
            jq("#city_filter_edit").chosen({ width: "100%", search_contains: true })
                .off("change")
                .on("change", function () {
                    @this.set('city_id', jq(this).val());
                });
        }

        if (jq("#branch_filter_edit").length) {
            jq("#branch_filter_edit").chosen({ width: "100%", search_contains: true })
                .off("change")
                .on("change", function () {
                    @this.set('branch_id', jq(this).val());
                });
        }
    }

    document.addEventListener("livewire:init", function () {
        initSubscriptionChosen();

        Livewire.hook('request', ({ respond }) => {
            respond(() => {
                setTimeout(() => {
                    initSubscriptionChosen();
                    jq("#city_filter_search, #branch_filter_search, #city_filter_create, #branch_filter_create, #city_filter_edit, #branch_filter_edit").trigger("chosen:updated");
                }, 50);
            });
        });
    });

    window.addEventListener('subscription-edit-loaded', () => {
        setTimeout(() => {
            initSubscriptionChosen();
            jq("#city_filter_edit").val(@this.get('city_id')).trigger("chosen:updated");
            jq("#branch_filter_edit").val(@this.get('branch_id')).trigger("chosen:updated");
        }, 300);
    });

    // Handle the dynamic dropdown rebuild correctly
    window.addEventListener('subscription-filter-updated', () => {
        setTimeout(() => {
            // 1. Destroy the old Chosen container setup completely
            if (jq("#branch_filter_search").data('chosen')) {
                jq("#branch_filter_search").chosen('destroy');
            }
            
            // 2. Re-apply Chosen on the fresh HTML options sent from backend
            jq("#branch_filter_search").chosen({ width: "100%", search_contains: true })
                .off("change")
                .on("change", function () {
                    @this.set('filter_branch_id', jq(this).val());
                });

            // 3. Inform Chosen UI to paint the options layout
            jq("#branch_filter_search").trigger("chosen:updated");
        }, 100); 
    });
</script>
@endsection