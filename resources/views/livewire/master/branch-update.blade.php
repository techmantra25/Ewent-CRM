<div class="container-fluid px-2 px-md-4">
  <form wire:submit.prevent="update">
     <div class="row gx-4 mb-4">
      <div class="col-auto my-auto">
        <div class="h-100">
          <h5 class="mb-0">Branch Management</h5>
          <div>
               <small class="text-dark fw-medium">Branch</small>
               <small class="text-light fw-medium arrow">Update Branch</small>
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
            <div class="row">
                <div class="col-6">
                    <div class="form-floating form-floating-outline mb-3">
                        <input type="text" wire:model="branch_name" class="form-control border border-2 p-2" placeholder="Enter Branch Name">
                        <label>Branch Name <span class="text-danger">*</span></label>
                    </div>
                </div>

                <div class="col-6">
                    <div class="form-floating form-floating-outline mb-3">
                        <input type="text" wire:model="branch_code" class="form-control border border-2 p-2" placeholder="Enter Branch Code" readonly>
                        <label>Branch Code</label>
                    </div>
                </div>

                <div class="col-12">
                    <div class="form-floating form-floating-outline mb-3">
                        <textarea wire:model="address" class="form-control border border-2 p-2" placeholder="Enter Address"></textarea>
                        <label>Address <span class="text-danger">*</span></label>
                    </div>
                </div>
                <div class="col-6">
                    <div wire:ignore class="chosen-floating mb-3">

                        <select id="city_filter_update" class="form-select">
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

                    @error('city_id')
                        <p class="text-danger inputerror">{{ $message }}</p>
                    @enderror
                </div>

                <div class="col-12 text-end">
                    <button type="submit" class="btn btn-secondary btn-sm add-new btn-primary waves-effect waves-light"
                        wire:loading.attr="disabled">
                        <span> Update Branch</span>
                    </button>
                </div>
            </div>
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

.chosen-container-single .chosen-single {
    height: 48px !important;
    line-height: 48px !important;
    border: 2px solid #d2d6da !important;
    border-radius: 0.5rem !important;
    background: #fff !important;
}

.chosen-container-single .chosen-single div b {
    margin-top: 10px;
}
</style>
<script>
  
  function updateImage(event, name) {
    const fileInput = event.target;
    const file = fileInput.files[0];
    const imageElement = document.getElementById(`image-preview`);

    // Check if a file was selected
    if (file) {
        const validTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        
        // Check if the selected file type is valid
        if (validTypes.includes(file.type)) {
            const reader = new FileReader();
            reader.onload = function (e) {
                if (imageElement) {
                    imageElement.src = e.target.result; // Update the image source with the file's data URL
                }
            };
            reader.readAsDataURL(file); // Read the file as a data URL for preview
        } else {
            // Alert user about invalid file type
            alert('Invalid file type. Please select a valid image (JPEG, PNG, GIF, WEBP).');
            fileInput.value = ''; // Reset the input field
            if (imageElement) {
                // Reset the image to the default preview image
                imageElement.src = '{{ asset('assets/img/profile-image.webp') }}';
            }
        }
    }
}
  
</script>
<script>
var jq = $.noConflict();

function initCityChosen() {

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

        jq("#city_filter_update")
            .val(@this.get('city_id'))
            .trigger("chosen:updated");
    }
}

document.addEventListener("livewire:init", function () {

    initCityChosen();

    Livewire.hook('request', ({ respond }) => {
        respond(() => {
            setTimeout(() => {
                initCityChosen();
            }, 100);
        });
    });
});
</script>

@endsection

