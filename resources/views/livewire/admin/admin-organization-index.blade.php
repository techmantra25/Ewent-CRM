
<div class="row mb-4">
    <style>
        .btn-inactive{
            background: #fff;
            color: #000;
        }
    </style>
    <div class="col-lg-12 d-flex justify-content-between">
        <div>
            <h5 class="mb-0">Organization Management</h5>
            <div>
                 <small class="text-dark fw-medium">Dashboard</small>
                 <small class="text-success fw-medium arrow">Organization</small>
            </div>
         </div>
        @if($activeTab==="list")
            <div>
                <a href="javascript:void(0)" wire:click="AddNewOrganization" class="btn btn-primary">
                    <i class="ri-add-line ri-16px me-0 me-sm-2 align-baseline"></i>
                    Add Organization
                </a>
            </div>
        @endif
        @if($activeTab!=='list')
           <div>
             <a class="btn btn-dark btn-sm waves-effect waves-light" href="javascript:void(0)" role="button" wire:click="resetForm">
                <i class="ri-arrow-go-back-line ri-16px me-0 me-sm-2 align-baseline"></i>
                Back
            </a>
           </div>
        @endif
    </div>
    <div class="col-lg-12 col-md-6 mb-md-0 mb-4">
        <div class="row">
            <div class="col-12">
                <div class="card my-4">
                    @if($activeTab==="list")
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
                                    <div class="d-flex align-items-center">
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
                                            Organizations
                                        </th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 align-middle px-4">
                                           Subscription
                                        </th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 align-middle px-4">
                                            Address
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
                                    @foreach($organizations as $k => $organization)
                                        @php
                                            $colors = ['bg-label-primary', 'bg-label-success', 'bg-label-info', 'bg-label-secondary', 'bg-label-danger', 'bg-label-warning'];
                                            $colorClass = $colors[$k % count($colors)]; // Rotate colors based on index
                                        @endphp
                                        <tr>
                                            <td class="align-middle text-center">{{ $organizations->firstItem() + $k }}</td>
                                            <td class="sorting_1" width="25%">
                                                <div class="d-flex justify-content-start align-items-center customer-name">
                                                    <div class="avatar-wrapper me-3">
                                                        <div class="avatar avatar-sm">
                                                            @if ($organization->image)
                                                                <img src="{{ asset($organization->image) }}" alt="Avatar" class="rounded-circle">
                                                            @else
                                                                <div class="avatar-initial rounded-circle {{$colorClass}}">
                                                                    {{ strtoupper(substr($organization->name, 0, 1)) }}{{ strtoupper(substr(strrchr($organization->name, ' '), 1, 1)) }}
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="d-flex flex-column">
                                                        <div>
                                                            <small class="badge bg-label-primary mb-0 cursor-pointer text-uppercase"> {{$organization->organization_id}}</small>
                                                        </div>
                                                        <a href="{{ route('admin.customer.details', $organization->id) }}"
                                                            class="text-heading"><span class="fw-medium text-truncate">{{ ucwords($organization->name) }}</span>
                                                        </a>
                                                        <small class="text-truncate">{{$organization->email}} | {{ $organization->mobile }} </small>
                                                    <div>
                                                </div>
                                            </td>
                                            <td class="align-middle price-details text-center">
                                                <div class="d-flex flex-column align-items-center">
                                                    <!-- Subscription Type Badge -->
                                                    <span class="badge bg-label-{{$organization->subscription_type === "weekly"?"primary":"success"}} mb-0 cursor-pointer text-uppercase mb-1" style="font-size: 0.85rem;">
                                                        {{ $organization->subscription_type }}
                                                    </span>
                                                    <!-- Renewal Info -->
                                                    <small class="text-muted" style="font-size: 0.75rem;">
                                                        @if($organization->subscription_type === "weekly")
                                                            Renewal: <span class="text-dark fw-semibold">{{ ucwords($organization->renewal_day) }}</span>
                                                        @else
                                                            Renewal: <span class="text-dark ffw-semibold">{{ $organization->renewal_day_of_month }}<sup>th</sup></span>
                                                        @endif
                                                    </small>
                                                </div>
                                            </td>

                                        <td class="align-middle text-start">
                                                <i class="fa-solid fa-location-dot text-primary mb-1"></i>
                                                <div class="fw-semibold">{{ $organization->street_address }}</div>
                                                <div>{{ $organization->city }}, {{ $organization->state }}</div>
                                                <div>{{ $organization->pincode }}</div>
                                            </td>

                                            <td class="align-middle text-sm text-center">
                                                <div class="form-check form-switch">
                                                    <input
                                                        class="form-check-input ms-auto"
                                                        type="checkbox"
                                                        id="flexSwitchCheckDefault{{ $organization->id }}"
                                                        wire:click="toggleStatus({{ $organization->id }})"
                                                        @if($organization->status) checked @endif>
                                                </div>
                                            </td>
                                            <td class="align-middle text-sm text-end">
                                                <div class="dropdown cursor-pointer">
                                                    <span class="badge px-2 rounded-pill bg-label-secondary dropdown-toggle" id="exploreDropdown_organization_{{$organization->id}}" data-bs-toggle="dropdown" aria-expanded="false">Explore</span>
                                                    <ul class="dropdown-menu" aria-labelledby="exploreDropdown_organization_{{$organization->id}}">
                                                        <li><a class="dropdown-item" href="{{route('admin.organization.dashboard', $organization->id)}}">Dashboard</a></li>
                                                        <li><a class="dropdown-item" href="javascript:void(0)" wire:click="editOrganization({{$organization->id}})">Edit</a></li>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                                <div class="d-flex justify-content-end mt-2">
                                    {{ $organizations->links() }}
                                </div>
                            </div>
                        </div>
                    @endif
                    @if($activeTab==="create")
                        <div class="card-header">
                        <h5 class="mb-0">{{$edit_id?"Update":"Add New"}} Organization</h5>
                        </div>
                        <div class="card-body">
                        <form wire:submit.prevent="saveOrganization">
                            <div class="row">
                                <div class="col-md-9">
                                    <div class="row g-2">
                                        {{-- Auto Org ID --}}
                                        <div class="col-md-3 mb-3">
                                            <label class="form-label">Organization ID</label>
                                            <input type="text" class="form-control form-control-sm" wire:model="organization_id"
                                            readonly disabled>
                                        </div>
                                        {{-- Name --}}
                                        <div class="col-md-9 mb-3">
                                            <label class="form-label">Organization Name</label>
                                            <input type="text" class="form-control form-control-sm" wire:model.defer="name" placeholder="Enter name here">
                                            @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                                        </div>
                                        {{-- Email --}}
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Email</label>
                                            <input type="email" class="form-control form-control-sm" wire:model.defer="email" placeholder="Enter email here">
                                            @error('email') <small class="text-danger">{{ $message }}</small> @enderror
                                        </div>
                                        {{-- Mobile --}}
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Mobile</label>
                                            <input type="text" class="form-control form-control-sm" wire:model.defer="mobile" placeholder="Enter mobile here">
                                            @error('mobile') <small class="text-danger">{{ $message }}</small> @enderror
                                        </div>
                                       {{-- GST Number --}}
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">GST Number</label>
                                            <input type="text" class="form-control form-control-sm" wire:model.defer="gst_number"
                                                placeholder="Enter GST number here">
                                            @error('gst_number') <small class="text-danger">{{ $message }}</small> @enderror
                                        </div>

                                        {{-- GST File Upload --}}
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Upload GST Document</label>
                                            <input type="file" class="form-control form-control-sm" wire:model="gst_file">
                                            @error('gst_file') <small class="text-danger">{{ $message }}</small> @enderror

                                            {{-- Preview (show if uploaded or existing) --}}
                                            @if ($gst_file_preview ?? false)
                                                <a href="{{ asset($gst_file_preview) }}" target="_blank"
                                                class="btn btn-sm btn-outline-primary mt-2">
                                                    View GST Document
                                                </a>
                                            @endif
                                        </div>

                                        {{-- PAN Number --}}
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">PAN Number</label>
                                            <input type="text" class="form-control form-control-sm" wire:model.defer="pan_number"
                                                placeholder="Enter PAN number here">
                                            @error('pan_number') <small class="text-danger">{{ $message }}</small> @enderror
                                        </div>

                                        {{-- PAN File Upload --}}
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Upload PAN Document</label>
                                            <input type="file" class="form-control form-control-sm" wire:model="pan_file">
                                            @error('pan_file') <small class="text-danger">{{ $message }}</small> @enderror

                                            {{-- Preview (show if uploaded or existing) --}}
                                            @if ($pan_file_preview ?? false)
                                                <a href="{{ asset($pan_file_preview) }}" target="_blank"
                                                class="btn btn-sm btn-outline-primary mt-2">
                                                    View PAN Document
                                                </a>
                                            @endif
                                        </div>



                                         <div class="col-md-12 mb-3">
                                            <label class="form-label">Street Address</label>
                                            <textarea class="form-control form-control-sm" wire:model.defer="street_address"></textarea>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">City</label>
                                            <input type="text" class="form-control form-control-sm" wire:model.defer="city" placeholder="Enter city here">
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">State</label form-control-sm>
                                            <input type="text" class="form-control form-control-sm" wire:model.defer="state" placeholder="Enter state here">
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Pincode</label>
                                            <input type="text" class="form-control form-control-sm" wire:model.defer="pincode" placeholder="Enter pincode here">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div>
                                        {{-- Image Upload --}}
                                        <label class="form-label">Logo / Image</label>
                                        <div class="mb-2 mt-2 text-center">
                                            <input type="file" 
                                                wire:model="image" 
                                                id="image" 
                                                accept="image/*"
                                                class="form-control border border-2 p-2 d-none">

                                            {{-- Preview Logic --}}
                                            <img id="image-preview"
                                                src="
                                                    @if($image)
                                                        {{ $image->temporaryUrl() }}
                                                    @elseif($old_image)
                                                        {{ asset($old_image) }}
                                                    @else
                                                        {{ asset('assets/img/organization.png') }}
                                                    @endif
                                                "
                                                alt="Selected Image"
                                                class="w-80 h-52 object-cover rounded-lg border border-gray-300"
                                                style="width: 50%; height: 120px;" 
                                                onclick="document.getElementById('image').click()">
                                        </div>
                                        @error('image')
                                        <p class="text-danger inputerror">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Rider Visibility (%)</label>
                                        <div class="input-group">
                                            <span class="input-group-text p-0">
                                                <button type="button" class="btn btn-success btn-sm border-0" style="border-radius: 5px 0px 0px 5px;">
                                                 Plus
                                                </button>
                                            </span>
                                            <input type="number" class="form-control form-control-sm text-center" wire:model="rider_visibility_percentage" min="0" max="100" step="0.01" style="min-height:31px !important;padding: 0 12px !important;" placeholder="Enter visibility %">
                                        </div>
                                        @error('rider_visibility_percentage') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Organization Discount (%)</label>
                                        <div class="input-group">
                                            <input type="number" class="form-control form-control-sm text-center" wire:model="discount_percentage" min="0" max="100" step="0.01" style="min-height:31px !important;padding: 0 12px !important;" placeholder="Enter discount %">
                                            <span class="input-group-text p-0">
                                                <button type="button" class="btn btn-danger btn-sm border-0" style="border-radius: 0px 5px 5px 0px;">
                                                    Minus
                                                </button>
                                            </span>
                                        </div>
                                        @error('discount_percentage') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>
                                    <div>
                                        <div class="mb-3">
                                            <label class="form-label d-block">Subscription Type</label>
                                            <div class="btn-group w-100" role="group" aria-label="Subscription Type">
                                                <input type="radio"
                                                    class="btn-check"
                                                    wire:model="subscription_type"
                                                    wire:change="SubscriptionTypeChange"
                                                    value="weekly"
                                                    id="weekly"
                                                    autocomplete="off">
                                                <label class="btn {{ $subscription_type === 'weekly' ? 'btn-success active' : 'btn-outline-success' }} btn-sm" for="weekly">
                                                    Weekly
                                                </label>

                                                <input type="radio"
                                                    class="btn-check"
                                                    wire:model="subscription_type"
                                                    wire:change="SubscriptionTypeChange"
                                                    value="monthly"
                                                    id="monthly"
                                                    autocomplete="off">
                                                <label class="btn  {{ $subscription_type === 'monthly' ? 'btn-success active' : 'btn-outline-success' }} btn-sm" for="monthly">
                                                    Monthly
                                                </label>
                                            </div>
                                        </div>


                                        @if($subscription_type === 'weekly')
                                            <div class="mb-3">
                                                <label class="form-label">Renewal Day (Weekly)</label>
                                                <select wire:model="renewal_day" class="form-control">
                                                    <option value="">Select Day</option>
                                                    <option value="sunday">Sunday</option>
                                                    <option value="monday">Monday</option>
                                                    <option value="tuesday">Tuesday</option>
                                                    <option value="wednesday">Wednesday</option>
                                                    <option value="thursday">Thursday</option>
                                                    <option value="friday">Friday</option>
                                                    <option value="saturday">Saturday</option>
                                                </select>
                                                @error('renewal_day') <small class="text-danger">{{ $message }}</small> @enderror
                                            </div>
                                        @elseif($subscription_type === 'monthly')
                                            <div class="mb-3">
                                                <label class="form-label">Renewal Day of Month (1-28)</label>
                                                <select wire:model="renewal_day_of_month" class="form-control">
                                                    <option value="">Select Date</option>
                                                    @for($i=1; $i<=28; $i++)
                                                        <option value="{{ $i }}">{{ $i }}</option>
                                                    @endfor
                                                </select>
                                                @error('renewal_day_of_month') <small class="text-danger">{{ $message }}</small> @enderror
                                            </div>
                                        @endif
                                    </div>
                                     {{-- Password --}}
                                    <div class="mb-3">
                                        <label class="form-label">Password</label>
                                        <input type="text" class="form-control form-control-sm"
                                            wire:model.defer="password" placeholder="Enter password here">
                                        @error('password') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>
                                </div>
                            </div>
                            {{-- Submit --}}
                            <div class="text-end">
                                <button type="submit" class="btn btn-secondary btn-sm add-new btn-primary waves-effect waves-light">
                                    <i class="fa fa-save"></i> {{$edit_id?"Update":"Save"}} Organization
                                </button>
                            </div>
                        </form>
                        </div>
                    @endif
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
                imageElement.src = '{{ asset('assets/img/organization.png') }}';
            }
        }
    }
}
  
</script>

@endsection

