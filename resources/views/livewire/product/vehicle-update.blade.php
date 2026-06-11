<div>
    <style>
        .chosen-single{
            height: 47px !important;
        }
    </style>
    <div class="row gx-4 mb-4">
        <div class="col-auto my-auto">
          <div class="h-100">
            <h5 class="mb-0">Vehicle Management</h5>
            <div>
                 <small class="text-dark fw-medium">Dashboard </small>
                 <small class="text-light fw-medium arrow">Update Vehicle</small>
            </div>
          </div>
        </div>
        <div class="col-lg-4 col-md-6 my-sm-auto ms-sm-auto me-sm-0 mx-auto mt-3">
          <div class="nav-wrapper position-relative end text-end">
            <!-- Back Button -->
            <a class="btn btn-dark btn-sm" href="javascript:history.back();" role="button">
              <i class="ri-arrow-go-back-line ri-16px me-0 me-sm-2 align-baseline"></i>
              Back
            </a>
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
              <div class="alert alert-danger">
                  {{ session('error') }}
              </div>
          @endif
      </div>
      <div class="row">
        <!-- Left Card -->
        <div class="col-lg-12">
          <div class="card card-plain p-4">
            <div class="card-body p-3">
                <form wire:submit.prevent="updateVehicle">
                    <div class="row">
                        <!-- Product Title -->
                         @if(auth('admin')->user()->branch_id == 1)

                            <div class="col-4">
                                <div wire:ignore class="mb-2 mt-2 form-floating form-floating-outline">

                                    <select id="city_filter_update"
                                        class="form-select border border-2 p-2">

                                        <option value="">Search City or State...</option>

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

                                    <label class="form-label">
                                        City / State <span class="text-danger">*</span>
                                    </label>

                                </div>
                            </div>
                            <div class="col-4">
                                <div wire:ignore
                                    wire:key="branch-update-container-{{ count($branchs) }}"
                                    class="mb-2 mt-2 form-floating form-floating-outline">

                                    <select id="branch_filter_update"
                                        class="form-select border border-2 p-2">

                                        <option value="">Select Branch</option>

                                        @foreach($branchs as $item)
                                            <option value="{{ $item->id }}"
                                                {{ $branch == $item->id ? 'selected' : '' }}>
                                                {{ $item->name }} | {{ $item->branch_code }}
                                            </option>
                                        @endforeach

                                    </select>

                                    <label class="form-label">
                                        Branch <span class="text-danger">*</span>
                                    </label>

                                </div>

                                @error('branch')
                                    <p class="text-danger inputerror">{{ $message }}</p>
                                @enderror
                            </div>
                        @endif
                        <div class="col-4">
                            <div class="mb-2 mt-2 form-floating form-floating-outline">
                            <select wire:model="model"
                                class="form-select border border-2 p-2">
                                <option value="" selected hidden>Select model</option>
                                @foreach($models as $model_item)
                                <option value="{{ $model_item->id }}">{{$model_item->category->title}}|{{ $model_item->title }}</option>
                                @endforeach
                            </select>
                            <label class="form-label">Model <span class="text-danger">*</span></label>
                            </div>
                            @error('model')
                            <p class="text-danger inputerror">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="col-4">
                            <div class="form-floating form-floating-outline mb-3 mt-2">
                            <input type="text" wire:model="vehicle_track_id" class="form-control border border-2 p-2"
                                placeholder="T256356" readonly>
                            <label>Vehicle Track ID <span class="text-danger">*</span></label>
                            </div>
                            @error('vehicle_track_id')
                            <p class="text-danger inputerror">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="col-4">
                            <div class="form-floating form-floating-outline mb-3 mt-2">
                            <input type="text" wire:model="friendly_name" class="form-control border border-2 p-2"
                                placeholder="Enter friendly name">
                            <label>Friendly Name</label>
                            </div>
                            @error('friendly_name')
                            <p class="text-danger inputerror">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="col-4">
                            <div class="form-floating form-floating-outline mb-3 mt-2">
                            <input type="text" wire:model="vehicle_number" class="form-control border border-2 p-2"
                                placeholder="Enter vehicle number" readonly>
                            <label>Vehicle Number<span class="text-danger">*</span></label>
                            </div>
                            @error('vehicle_number')
                            <p class="text-danger inputerror">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="col-4">
                            <div class="form-floating form-floating-outline mb-3 mt-2">
                            <input type="text" wire:model="imei_number" class="form-control border border-2 p-2"
                                placeholder="Enter imei number">
                            <label>LOT IMEI Number</label>
                            </div>
                            @error('imei_number')
                            <p class="text-danger inputerror">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="col-4">
                            <div class="form-floating form-floating-outline mb-3 mt-2">
                            <input type="text" wire:model="chassis_number" class="form-control border border-2 p-2"
                                placeholder="Enter chassis number">
                            <label>Chassis Number<span class="text-danger">*</span></label>
                            </div>
                            @error('chassis_number')
                            <p class="text-danger inputerror">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="text-end">
                            <button type="submit" class="btn btn-secondary btn-sm add-new btn-primary waves-effect waves-light"
                            wire:loading.attr="disabled">
                            <span> Update Vehicle</span>
                            </button>
                        </div>
                    </div>
                </form>
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
var jq = $.noConflict();

function initUpdateChosen() {

    // City
    if (jq("#city_filter_update").length) {

        if (jq("#city_filter_update").data('chosen')) {
            jq("#city_filter_update").chosen('destroy');
        }

        jq("#city_filter_update")
            .chosen({
                width: "100%",
                search_contains: true
            })
            .off("change")
            .on("change", function () {
                @this.set('city_id', jq(this).val());
            });
    }

    // Branch
    if (jq("#branch_filter_update").length) {

        if (jq("#branch_filter_update").data('chosen')) {
            jq("#branch_filter_update").chosen('destroy');
        }

        jq("#branch_filter_update")
            .chosen({
                width: "100%",
                search_contains: true
            })
            .off("change")
            .on("change", function () {
                @this.set('branch', jq(this).val());
            });
    }
}

document.addEventListener("livewire:init", function () {

    initUpdateChosen();

    Livewire.hook('request', ({ respond }) => {
        respond(() => {

            setTimeout(() => {

                initUpdateChosen();

                jq("#city_filter_update")
                    .trigger("chosen:updated");

                jq("#branch_filter_update")
                    .trigger("chosen:updated");

            }, 100);

        });
    });
});

window.addEventListener('vehicle-city-updated', () => {

    setTimeout(() => {

        if (jq("#branch_filter_update").data('chosen')) {
            jq("#branch_filter_update").chosen('destroy');
        }

        jq("#branch_filter_update")
            .chosen({
                width: "100%",
                search_contains: true
            })
            .off("change")
            .on("change", function () {
                @this.set('branch', jq(this).val());
            });

        jq("#branch_filter_update")
            .trigger("chosen:updated");

    }, 100);

});
</script>
@endsection
