<div class="container-fluid">

  {{-- HEADER --}}
  <div class="row mb-4 align-items-center">
    <div class="col-auto my-auto">
      <h5 class="mb-1">Admin Internal Tools</h5>

      <div class="d-flex gap-1 text-muted">
        <small>Developer Settings</small>
        <span>/</span>
        <small class="text-primary">Rider Type Change (B2B/B2C)</small>
      </div>
    </div>
    <div class="col text-end">
        <a href="{{ route('admin.admin_internal_tools.developer_settings') }}" 
        class="btn btn-dark btn-sm">
            ← Back
        </a>
    </div>
  </div>

  {{-- ALERTS --}}
  @if (session()->has('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  @if (session()->has('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
  @endif

  <div class="card">
    <div class="card-body">
      <div class="row">

        {{-- LEFT: Rider Details --}}
        <div class="col-md-8">
            @if($rider)

                {{-- Header --}}
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">Rider Details</h5>

                    <span class="badge 
                        {{ $rider->user_type === 'B2B' ? 'bg-primary' : 'bg-success' }}">
                        {{ $rider->user_type }}
                    </span>
                </div>

                {{-- Rider Info --}}
                <div class="row g-3">

                    <div class="col-md-6">
                        <div class="p-2 ">
                            <small class="text-muted">Name</small>
                            <div class="fw-semibold">{{ $rider->name }}</div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="p-2 ">
                            <small class="text-muted">Mobile</small>
                            <div class="fw-semibold">{{ $rider->mobile }}</div>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="p-2 ">
                            <small class="text-muted">Email</small>
                            <div class="fw-semibold">{{ $rider->email ?? 'N/A' }}</div>
                        </div>
                    </div>

                </div>

                {{-- Organization Section --}}
                @if($rider->user_type === 'B2B' && $rider->organization_details)

                    <hr class="my-4">

                    <div class="d-flex align-items-center mb-3">
                        <h6 class="mb-0">Organization Details</h6>
                    </div>

                    <div class="row g-3">

                        <div class="col-md-6">
                            <div class="p-2 ">
                                <small class="text-muted">Organization Name</small>
                                <div class="fw-semibold">
                                    {{ $rider->organization_details->name }}
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="p-2 ">
                                <small class="text-muted">Mobile</small>
                                <div class="fw-semibold">
                                    {{ $rider->organization_details->mobile }}
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="p-2 ">
                                <small class="text-muted">Email</small>
                                <div class="fw-semibold">
                                    {{ $rider->organization_details->email ?? 'N/A' }}
                                </div>
                            </div>
                        </div>

                    </div>

                @elseif($rider->user_type === 'B2B')

                    <hr class="my-4">
                    <div class="alert alert-warning mb-0">
                        Organization not assigned
                    </div>

                @endif

            @else

                {{-- Empty State --}}
                <div class="text-center py-5 text-muted">
                    <div class="mb-2" style="font-size: 40px;">👤</div>
                    <div>No rider selected</div>
                </div>

            @endif
        </div>

        {{-- RIGHT: Actions --}}
        <div class="col-md-4">
          <div class="border p-3 rounded">

            {{-- INPUT (Always editable) --}}
            <div class="mb-3">
              <label class="form-label">Rider Mobile/Email*</label>
              <input type="text"
                     class="form-control"
                     wire:model="rider_mobile_or_email"
                     placeholder="Enter here">
            </div>

            {{-- SEARCH BUTTON (only when no rider) --}}
            @if(!$rider)
              <button class="btn btn-primary w-100 mb-3"
                      wire:click="searchRider"
                      wire:loading.attr="disabled">
                <span wire:loading.remove>Search Rider</span>
                <span wire:loading>Searching...</span>
              </button>
            @endif

            {{-- AFTER SEARCH --}}
            @if($rider)

              {{-- Rider Type (readonly opposite) --}}
              <div class="mb-3">
                <label class="form-label">Rider Type*</label>
                <input type="text" class="form-control" value="{{ $rider_type }}" readonly>
              </div>

              {{-- Organization (only if B2B) --}}
              @if($rider_type === 'B2B')
                <div class="mb-3">
                  <label class="form-label">Select Organization*</label>
                  <select class="form-select" wire:model="organization_id">
                    <option value="">Select Organization</option>
                    @foreach($organizations as $org)
                      <option value="{{ $org->id }}">{{ $org->name }}</option>
                    @endforeach
                  </select>
                  @error('organization_id')
                    <small class="text-danger">{{ $message }}</small>
                  @enderror
                </div>
              @endif

              {{-- SUBMIT BUTTON --}}
              <button class="btn btn-success w-100"
                      onclick="confirm('Are you sure to change rider type?') || event.stopImmediatePropagation()"
                      wire:click="updateRiderType"
                      wire:loading.attr="disabled">

                <span wire:loading.remove>Submit</span>
                <span wire:loading>Processing...</span>
              </button>

            @endif

          </div>
        </div>

      </div>
    </div>
  </div>
  {{-- <div class="loader-container" wire:loading>
      <div class="loader"></div>
    </div> --}}
</div>