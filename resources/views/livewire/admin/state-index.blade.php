<div>
    <div class="row mb-4">

        <div class="col-lg-8 col-md-6 mb-md-0 mb-4">
            <div class="card my-4">
                <div class="card-header pb-2">

                    @if(session()->has('message'))
                        <div class="alert alert-success" id="flashMessage">
                            {{ session('message') }}
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-lg-6 col-7">
                            <h6>States</h6>
                        </div>

                        <div class="col-lg-6 col-5 my-auto text-end">
                            <div class="ms-md-auto d-flex align-items-center">
                                <input type="text"
                                       wire:model.debounce.500ms="search"
                                       class="form-control border border-2 p-2 custom-input-sm"
                                       placeholder="Enter State Name">

                                <button type="button"
                                        wire:click="searchButtonClicked"
                                        class="btn btn-dark text-white mb-0 custom-input-sm">
                                    <span class="material-icons">search</span>
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
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        SL
                                    </th>

                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        State Name
                                    </th>

                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Country
                                    </th>

                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Status
                                    </th>

                                    <th class="text-end text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 px-4">
                                        Actions
                                    </th>
                                </tr>
                            </thead>

                            <tbody>

                                @forelse($states as $k => $state)

                                <tr>

                                    <td class="align-middle text-center">
                                        {{ $k + 1 }}
                                    </td>

                                    <td class="align-middle text-center">
                                        {{ ucwords($state->name) }}
                                    </td>

                                    <td class="align-middle text-center">
                                        {{ $state->country }}
                                    </td>

                                    <td class="align-middle text-center">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input ms-auto"
                                                   type="checkbox"
                                                   wire:click="toggleStatus({{ $state->id }})"
                                                   @if($state->status) checked @endif>
                                        </div>
                                    </td>

                                    <td class="align-middle text-end px-4">
                                        <button wire:click="edit({{ $state->id }})"
                                                class="btn btn-sm btn-icon edit-record btn-text-secondary rounded-pill waves-effect"
                                                title="Edit">
                                            <i class="ri-edit-box-line ri-20px text-info"></i>
                                        </button>
                                    </td>

                                </tr>

                                @empty

                                <tr>
                                    <td colspan="5" class="text-center">
                                        No Record Found
                                    </td>
                                </tr>

                                @endforelse

                            </tbody>

                        </table>

                    </div>
                </div>
            </div>
        </div>

        <!-- Form Section -->

        <div class="col-lg-4 col-md-6 mb-md-0 mb-4">
            <div class="card my-4">

                <div class="card-body px-0 pb-2 mx-4">

                    <div class="d-flex justify-content-between mb-3">
                        <h5>{{ $stateId ? 'Update State' : 'Create State' }}</h5>
                    </div>

                    <form wire:submit.prevent="save">

                        <div class="row">

                            <div class="form-floating form-floating-outline mb-3">
                                <input type="text"
                                       wire:model="name"
                                       class="form-control border border-2 p-2"
                                       placeholder="Enter State Name">

                                <label>
                                    State Name
                                    <span class="text-danger">*</span>
                                </label>
                            </div>

                            @error('name')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror

                            <div class="form-floating form-floating-outline mb-3">
                                <input type="text"
                                       wire:model="country"
                                       class="form-control border border-2 p-2"
                                       placeholder="Enter Country">

                                <label>
                                    Country
                                    <span class="text-danger">*</span>
                                </label>
                            </div>

                            @error('country')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror

                            <div class="mb-2 text-end mt-4">

                                <button type="button"
                                        wire:click="refresh"
                                        class="btn btn-danger text-white mb-0 custom-input-sm ms-2">
                                    <i class="ri-restart-line"></i>
                                </button>

                                <button type="submit"
                                        class="btn btn-secondary btn-sm">
                                    {{ $stateId ? 'Update State' : 'Create State' }}
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