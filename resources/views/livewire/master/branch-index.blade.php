
<div class="row mb-4">
    <div class="col-lg-12 d-flex justify-content-between">
        <div>
            <h5 class="mb-0">Branch Management</h5>
            <div>
                 <small class="text-dark fw-medium">Dashboard</small>
                 <small class="text-light fw-medium arrow">Branch</small>
            </div>
         </div>
        <div>
            <a href="{{route('admin.branch.create')}}" class="btn btn-primary">
                <i class="ri-add-line ri-16px me-0 me-sm-2 align-baseline"></i>
                Add Branch
            </a>
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
                        <div class="row">
                            <div class="col-lg-12 d-flex justify-content-end my-auto">
                               <div wire:ignore.self style="width:200px;">
                                    <select id="state_filter" class="form-select">
                                        <option value="">Select State</option>
                                        @foreach($states as $state)
                                            <option value="{{ $state->id }}" {{ $state->id == $state_id ? 'selected' : '' }}>
                                                {{ $state->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div wire:ignore.self style="width:200px;">
                                    <select id="city_filter" class="form-select">
                                        <option value="">Select City</option>
                                        @foreach($cities as $city)
                                            <option value="{{ $city->id }}" {{ $city->id == $city_id ? 'selected' : '' }}>
                                                {{ $city->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="ms-2 d-flex align-items-center">
                                    <input type="text" wire:model.debounce.300ms="search"
                                           class="form-control border border-2 p-2 custom-input-sm"
                                           placeholder="Search here...">
                                        <button type="button" wire:click="searchButtonClicked"
                                                class="btn btn-dark text-white mb-0 custom-input-sm ms-2">
                                            <span class="material-icons">search</span>
                                        </button>
                                    <!-- Refresh Button -->
                                    <button type="button" wire:click="resetSearch" class="btn btn-danger text-white mb-0 custom-input-sm ms-2">
                                            <i class="ri-restart-line"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body px-0 pb-2 mt-2">
                        <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0 product-list">
                            <thead>
                                <tr>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 align-middle">
                                        SL
                                    </th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 align-middle" width="25%">
                                        Branch Name
                                    </th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 align-middle px-4">
                                        Branch Code
                                    </th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 align-middle px-4">
                                        City
                                    </th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 align-middle px-4">
                                        Address
                                    </th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 align-middle px-4">
                                        Employees
                                    </th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 align-middle px-4">
                                        Status
                                    </th>
                                    <th class="text-end text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 align-middle px-4">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($branches as $k => $branch)
                                    @php
                                        $colors = ['bg-label-primary', 'bg-label-success', 'bg-label-info', 'bg-label-secondary', 'bg-label-danger', 'bg-label-warning'];
                                        $colorClass = $colors[$k % count($colors)]; // Rotate colors based on index
                                    @endphp
                                    <tr>
                                        <td class="align-middle text-center">{{ $branches->firstItem()+$k }}</td>
                                        <td class="align-middle text-center">
                                           {{ ucwords($branch->name) }}
                                        </td>
                                        <td class="align-middle price-details text-center">
                                            {{$branch->branch_code}}
                                        </td>
                                        <td class="align-middle text-center">
                                            {{ $branch->city->name ?? 'N/A' }}
                                        </td>
                                        <td class="align-middle price-details text-center">
                                            {{$branch->address}}
                                        </td>
                                        <td class="align-middle price-details text-center">
                                            <a href="{{ route('admin.employee.list', ['branch_id' => $branch->id]) }}"
                                                class="text-primary fw-bold">
                                                    {{ $branch->employees_count }}
                                            </a>
                                        </td>
                                        <td class="align-middle text-sm text-center">
                                            <div class="form-check form-switch">
                                                <input
                                                    class="form-check-input ms-auto"
                                                    type="checkbox"
                                                    id="flexSwitchCheckDefault{{ $branch->id }}"
                                                    wire:click="toggleStatus({{ $branch->id }})"
                                                    @if($branch->status) checked @endif>
                                            </div>
                                        </td>
                                        <td class="align-middle text-end px-4">
                                            <a href="{{ route('admin.branch.update', $branch->id) }}" class="btn btn-sm btn-icon edit-record btn-text-secondary rounded-pill waves-effect btn-sm" title="Edit">
                                                <i class="ri-edit-box-line ri-20px text-info"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                            <div class="d-flex justify-content-end mt-2">
                                {{ $branches->links('pagination::bootstrap-4') }}
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
<link rel="stylesheet" href="{{ asset('assets/custom_css/component-chosen.css') }}">
<script src="{{ asset('assets/js/chosen.jquery.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    window.addEventListener('showConfirm', function (event) {
        let itemId = event.detail[0].itemId;
        Swal.fire({
            title: "Are you sure?",
            text: "You won't be able to revert this!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, delete it!"
        }).then((result) => {
            if (result.isConfirmed) {
                @this.call('destroy', itemId);
            }
        });
    });
</script>
<script>
var jq = $.noConflict();

function initChosen() {
    jq("#state_filter")
        .off('change')
        .chosen({width: "200px"})
        .on('change', function () {
            let state = jq(this).val();
            @this.call('changeState', state);
        });

    jq("#city_filter")
        .off('change')
        .chosen({width: "200px"})
        .on('change', function () {
            let city = jq(this).val();
            @this.set('city_id', city);
        });
}

// Initialize Chosen on page load
document.addEventListener("livewire:init", function () {
    initChosen();
});

// Re-initialize Chosen whenever Livewire updates the city options
document.addEventListener('livewire:navigated', () => {
    initChosen();
});

// This listens directly to your PHP component's $this->dispatch('refreshChosen')
window.addEventListener("refreshChosen", function () {
    setTimeout(() => {
        // Destroy old instances
        if (jq("#city_filter").data('chosen')) {
            jq("#city_filter").chosen("destroy");
        }
        if (jq("#state_filter").data('chosen')) {
            jq("#state_filter").chosen("destroy");
        }

        // Re-initialize with new HTML layout options
        initChosen();

        // Force Chosen plugin visual interface update
        jq("#city_filter").trigger("chosen:updated");
        jq("#state_filter").trigger("chosen:updated");
    }, 50);
});
</script>
@endsection

