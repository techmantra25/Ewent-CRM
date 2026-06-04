<div class="container-fluid px-2 px-md-4">
  <form wire:submit.prevent="saveProduct" enctype="multipart/form-data">
    <div class="row gx-4 mb-4">
      <div class="col-auto my-auto">
        <div class="h-100">
          <h5 class="mb-0">Employee Management</h5>
          <div>
              <small class="text-dark fw-medium">Employee</small>
              <small class="text-light fw-medium arrow">New Employee</small>
          </div>
        </div>
      </div>
      <div class="col-lg-4 col-md-6 my-sm-auto ms-sm-auto me-sm-0 mx-auto mt-3">
        <div class="nav-wrapper position-relative end text-end">
          <a class="btn btn-dark btn-sm" href="javascript:history.back();" role="button">
            <i class="ri-arrow-go-back-line ri-16px me-0 me-sm-2 align-baseline"></i>
            Back
          </a>
          <button type="submit" class="btn btn-secondary btn-sm add-new btn-primary waves-effect waves-light"
            wire:loading.attr="disabled">
            <span> Create Employee</span>
          </button>
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
      <div class="col-lg-9">
        <div class="card card-plain p-4">
          <div class="card-body p-3">
            <div class="row">
              <div class="col-8">
                  <div class="form-floating form-floating-outline mb-3">
                    <input type="text" wire:model="name" class="form-control border border-2 p-2"
                      placeholder="Enter Name">
                    <label>Employee Name <span class="text-danger">*</span></label>
                  </div>
                  @error('name')
                  <p class="text-danger inputerror">{{ $message }}</p>
                  @enderror
              </div>
              <div class="col-4">
                <div class="mb-2 form-floating form-floating-outline">
                  <select wire:model="designation" wire:change="GetDesignation($event.target.value)"
                    class="form-select border border-2">
                    <option value="" selected hidden>Select Designation</option>
                      @foreach($designations as $designation_item)
                      <option value="{{ $designation_item->id }}">{{ $designation_item->name }}</option>
                      @endforeach
                  </select>
                  <label class="form-label">Designation<span class="text-danger">*</span></label>
                </div>
                @error('designation')
                <p class="text-danger inputerror">{{ $message }}</p>
                @enderror
              </div>

              <div class="col-6">
                <div class="form-floating form-floating-outline mb-3 mt-2">
                    <input type="text" wire:model="email" class="form-control border border-2 p-2"
                      placeholder="Enter Email">
                    <label>Email <span class="text-danger">*</span></label>
                  </div>
                  @error('email')
                  <p class="text-danger inputerror">{{ $message }}</p>
                  @enderror
              </div>
              <div class="col-6">
                <div class="form-floating form-floating-outline mb-3 mt-2">
                    <input type="text" wire:model="mobile" class="form-control border border-2 p-2"
                      placeholder="Enter Mobile">
                    <label>Mobile <span class="text-danger">*</span></label>
                  </div>
                  @error('mobile')
                  <p class="text-danger inputerror">{{ $message }}</p>
                  @enderror
              </div>

              <div class="col-md-6 col-12 mt-2">
                <div class="chosen-floating mb-3" wire:ignore>
                    <select id="city_select" class="form-select">
                        <option value=""></option>

                        @foreach($cities as $city)
                            <option value="{{ $city->id }}" {{ $city->id == $city_id ? 'selected' : '' }}>
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

                <div class="col-md-6 col-12 mt-2">
                  <div class="chosen-floating mb-3"
                    wire:key="branch-select-container-{{ $city_id }}">

                    <select id="branch_select" class="form-select">
                        <option value=""></option>

                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}"
                                {{ $branch->id == $branch_id ? 'selected' : '' }}>
                                {{ $branch->name }}
                            </option>
                        @endforeach
                    </select>

                    <label class="chosen-label">
                        Branch <span class="text-danger">*</span>
                    </label>
                  </div>
                </div>


            </div>
          </div>
        </div>
      </div>
      
      <div class="col-lg-3">
        <div class="card card-plain mb-3">
          <div class="card-body p-3">
            <h6>Employee Image</h6>
            <div class="mb-2 mt-2">
              <input type="file" wire:model="image" id="image" accept="image/*"
                class="form-control border border-2 p-2 d-none" onchange="updateImage(event, 'image')">
              <img id="image-preview"
                src="{{ $image ? $image->temporaryUrl() : asset('assets/img/profile-image.webp') }}"
                alt="Selected Image" class="w-80 h-52 object-cover rounded-lg border border-gray-300"
                style="width: 100%" onclick="document.getElementById('image').click()">
            </div>
            @error('image')
            <p class="text-danger inputerror">{{ $message }}</p>
            @enderror
          </div>
        </div>
      </div>
    </div>
  </form>
  <div class="loader-container" wire:loading>
    <div class="loader"></div>
  </div>
</div>

@section('page-script')
<style>
 .chosen-floating {
    position: relative;
}

.chosen-label {
    position: absolute;
    top: -10px;
    left: 12px;
    z-index: 10;
    background: #fff;
    padding: 0 6px;
    font-size: 12px;
    color: #6c757d;
    font-weight: 500;
}

.chosen-floating .chosen-container {
    width: 100% !important;
}

.chosen-floating .chosen-container-single .chosen-single {
    height: 50px !important;
    line-height: 48px !important;
    border: 2px solid #dee2e6 !important;
    border-radius: 0.5rem !important;
    background: #fff !important;
}
</style>
<script>
  
  function updateImage(event, name) {
    const fileInput = event.target;
    const file = fileInput.files[0];
    const imageElement = document.getElementById(`image-preview`);

    if (file) {
        const validTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (validTypes.includes(file.type)) {
            const reader = new FileReader();
            reader.onload = function (e) {
                if (imageElement) {
                    imageElement.src = e.target.result;
                }
            };
            reader.readAsDataURL(file);
        } else {
            alert('Invalid file type. Please select a valid image (JPEG, PNG, GIF, WEBP).');
            fileInput.value = '';
            if (imageElement) {
                imageElement.src = '{{ asset('assets/img/profile-image.webp') }}';
            }
        }
    }
}

var jq = $.noConflict();

  function initDropdowns() {
     
      let citySelect = jq("#city_select");
      citySelect.chosen({ 
          width: "100%",
          search_contains: true 
      });
      citySelect.trigger("chosen:updated");

      citySelect.off('change').on('change', function () {
          let selectedCity = jq(this).val();
          @this.set('city_id', selectedCity);
      });

      let branchSelect = jq("#branch_select");
      branchSelect.chosen({ 
          width: "100%",
          search_contains: true 
      });
      branchSelect.trigger("chosen:updated");

      branchSelect.off('change').on('change', function () {
          let selectedBranch = jq(this).val();
          @this.set('branch_id', selectedBranch);
      });
  }

  document.addEventListener("livewire:init", function () {
      initDropdowns();

      Livewire.hook('morph.updated', () => {
          initDropdowns();
      });
  });
</script>
@endsection