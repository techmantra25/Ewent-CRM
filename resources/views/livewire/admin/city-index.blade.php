<div>
  <div class="row">
        <div class="col-lg-12 d-flex justify-content-end">
            <button type="button" class="btn btn-primary" wire:click="ModalImport(1)">
                <i class="ri-add-line ri-16px me-0 me-sm-2 align-baseline"></i>
                Import
            </button>
        </div>
        <div class="modal fade {{$modal_activity_class==1?"show d-block":""}}" id="uploadStockModal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Upload Cities via CSV</h5>
                        <button type="button" class="btn-close" wire:click="ModalImport(0)"></button>
                    </div>
                    <div class="modal-body">
                        <form wire:submit.prevent="uploadFile">
                            <div class="mb-3">
                                <label for="csvFile" class="form-label">Select CSV File</label>
                                <input class="form-control" type="file" id="csvFile" wire:model="csvFile" accept=".csv,.txt">
                                @error('csvFile') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="mb-3">
                                <a href="{{ asset('assets/csv/city-sample.csv') }}" class="btn btn-link" download>
                                    <i class="ri-download-line"></i> Download Sample CSV File
                                </a>
                            </div>
                            <div class="text-end">
                                @if(session()->has('csv_error'))
                                    <div class="alert alert-danger">
                                        {{ session('csv_error') }}
                                    </div>
                                @endif
                                <button type="submit" class="btn btn-primary">Upload</button>
                            </div>
                        </form>
                    </div>
                    
                </div>
            </div>
        </div>
        @if($modal_activity_class == 1)
            <div class="modal-backdrop fade show"></div>
        @endif
    </div>
    <div class="row mb-4">
      <div class="col-lg-8 col-md-6 mb-md-0 mb-4">
        <div class="row">
          <div class="col-12">
            <div class="card my-4">
              <div class="card-header pb-2">
                <div class="row">
                  @if(session()->has('message'))
                  <div class="alert alert-success" id="flashMessage">
                    {{ session('message') }}
                  </div>
                  @endif
                </div>
                <div class="row">
                  <div class="col-lg-2 col-7">
                    <h6>Cities</h6>
                  </div>
                  <div class="col-lg-10 col-5 my-auto text-end">
                    <div class="ms-md-auto d-flex align-items-center justify-content-end">

                        <!-- State Filter -->
                        <div wire:ignore.self style="width:400px;" class="me-2">
                            <select id="state_filter" class="form-select">
                                <option value="">State</option>
                                @foreach($states as $state)
                                    <option value="{{ $state->id }}"
                                        {{ $state->id == $filter_state_id ? 'selected' : '' }}>
                                        {{ $state->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Search -->
                        <input type="text"
                              wire:model.debounce.500ms="search"
                              class="form-control border border-2 p-2 custom-input-sm"
                              placeholder="Enter Name">

                        <button type="button"
                                wire:click="searchButtonClicked"
                                class="btn btn-dark text-white mb-0 custom-input-sm">
                            <span class="material-icons">search</span>
                        </button>

                        <!-- Reset -->
                        <button type="button"
                                wire:click="resetSearch"
                                class="btn btn-danger text-white mb-0 custom-input-sm ms-2">
                            <i class="ri-restart-line"></i>
                        </button>

                    </div>
                </div>
                </div>
              </div>
              <div class="card-body px-0 pb-2">
                <div class="table-responsive p-0">
                <table class="table align-items-center mb-0">
                    <thead>
                        <tr>
                            <th
                                class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 align-middle">
                                SL
                            </th>
                            <th
                                class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 align-middle">
                                State
                            </th>
                            <th
                                class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 align-middle">
                                City
                            </th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 align-middle">
                                Status
                            </th>
                            <th
                                class="text-end text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 align-middle px-4">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($cities as $k => $city)
                        <tr>
                            <td class="align-middle text-center">{{ $k + 1 }}</td>
    
                            {{-- Display city Image --}}
                            <td class="align-middle text-center">
                                {{ ucwords($city->state->name) }}
                            </td>
    
                            {{-- Display city Title --}}
                            <td class="align-middle text-center">{{ ucwords($city->name) }}</td>
    
                            {{-- Toggle Status --}}
                            <td class="align-middle text-center">
                                <div class="form-check form-switch">
                                    <input class="form-check-input ms-auto" type="checkbox"
                                        id="flexSwitchCheckDefault{{ $city->id }}" wire:click="toggleStatus({{ $city->id }})"
                                        @if($city->status) checked @endif>
                                </div>
                            </td>
    
                            {{-- Action Buttons --}}
                            <td class="align-middle text-end px-4">
                                <button wire:click="edit({{ $city->id }})"
                                    class="btn btn-sm btn-icon edit-record btn-text-secondary rounded-pill waves-effect btn-sm"
                                    title="Edit">
                                    <i class="ri-edit-box-line ri-20px text-info"></i>
                                </button>
                                 <button wire:click="destroy({{ $city->id }})"
                                    class="btn btn-sm btn-icon delete-record btn-text-secondary rounded-pill waves-effect"
                                    title="Delete">
                                    <i class="ri-delete-bin-7-line ri-20px text-danger"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
    
    
                  <div class="d-flex justify-content-end mt-2">
                    {{-- {{ $banners->links() }} --}} 
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    
      <div class="col-lg-4 col-md-6 mb-md-0 mb-4">
        <div class="row">
          <div class="col-12">
            <div class="card my-4">
              <div class="card-body px-0 pb-2 mx-4">
                <div class="d-flex justify-content-between mb-3">
                  <h5>{{$cityId ? "Update City" : "Create City"}}</h5>
                </div>
                <form wire:submit.prevent="save">
                  <div class="row">
    
                    <div class="mb-2 form-floating form-floating-outline">
                        <select wire:model="state_id"
                            class="form-select border border-2 p-2">
                            <option value="" selected="" hidden="">Select State</option>
                            @foreach ($states as $item)
                                <option value="{{$item->id}}">{{$item->name}}</option>
                            @endforeach
                        </select>
                        <label class="form-label">State</label>
                    </div>
                    @error('state_id')
                    <p class='text-danger inputerror'>{{ $message }}</p>
                    @enderror
                    <div class="form-floating form-floating-outline mb-5 fv-plugins-icon-container">
                      <input type="text" wire:model="name" class="form-control border border-2 p-2"
                        placeholder="Enter City Name">
                      <label> Name <span class="text-danger">*</span></label>
                    </div>
                    @error('name')
                    <p class='text-danger inputerror'>{{ $message }}</p>
                    @enderror
                    <div class="mb-2 text-end mt-4">
                        <button type="button" wire:click="refresh" class="btn btn-danger text-white mb-0 custom-input-sm ms-2">
                                <i class="ri-restart-line"></i>
                        </button>
                          <button type="submit" class="btn btn-secondary btn-sm add-new btn-primary waves-effect waves-light">
                              <span>{{ $cityId ? "Update City" : "Create City" }}</span>
                          </button>
                    </div>
                  </div>
                </form>
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
var jq = $.noConflict();

function initChosen() {

    jq("#state_filter")
        .off('change')
        .chosen({
            width: "200px"
        })
        .on('change', function () {

            let state = jq(this).val();

            @this.call('changeState', state);
        });
}

document.addEventListener("livewire:init", function () {
    initChosen();
});

window.addEventListener("refreshChosen", function () {

    setTimeout(() => {

        if (jq("#state_filter").data('chosen')) {
            jq("#state_filter").chosen("destroy");
        }

        initChosen();

        jq("#state_filter").trigger("chosen:updated");

    }, 50);
});
</script>

@endsection
    