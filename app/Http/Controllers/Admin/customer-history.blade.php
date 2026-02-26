<div>
    <div class="row">
        <!-- Season Table -->
        <div class="col-md-8">
            <div class="card">
                <div class="p-1 d-flex justify-content-between">
                    @if (session()->has('success'))
                        <div class="alert alert-success alert-dismissible fade show w-100" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                </div>
                <div class="card-header d-flex justify-content-between">
                    <h5 class="mb-0">Seasons</h5>
                    <input wire:model.live="search" type="text" class="form-control w-50" placeholder="Search season">
                </div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>SL</th>
                                <th>Name</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($seasons as $index => $season)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $season->name }}</td>
                                    <td>{{ date('Y-m-d', strtotime($season->start_date)) }}</td>
                                    <td>{{ date('Y-m-d', strtotime($season->end_date)) }}</td>
                                    <td class="text-center">
                                        <button wire:click="edit({{ $season->id }})" class="btn btn-sm btn-primary">
                                            <i class="ri-edit-box-line ri-12px ri-padding"></i>
                                        </button>
                                        <button wire:click="delete({{ $season->id }})" class="btn btn-sm btn-danger">
                                           <i class="ri-delete-bin-7-line ri-12px ri-padding"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">No seasons found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Season Form -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">{{ $editMode ? 'Update Season' : 'Create Season' }}</h5>
                </div>
                <div class="card-body">
                    <form wire:submit.prevent="{{ $editMode ? 'update' : 'store' }}">
                        <div class="mb-3">
                            <label class="form-label">Season Name <span class="text-danger">*</span></label>
                            <input wire:model="name" type="text" class="form-control" placeholder="Enter season name">
                            @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Start Date</label>
                            <input wire:model="start_date" type="date" class="form-control">
                            @error('start_date') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">End Date</label>
                            <input wire:model="end_date" type="date" class="form-control">
                            @error('end_date') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        @if (session()->has('error'))
                            <div class="alert alert-danger alert-dismissible fade show w-100" role="alert">
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif
                        <div class="d-flex justify-content-between">
                            @if($editMode)
                                <button type="button" wire:click="resetForm" class="btn btn-warning">Cancel</button>
                            @endif
                            <button type="submit" class="btn btn-success ms-auto">
                                {{ $editMode ? 'Update' : 'Create' }}
                            </button>
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
         window.addEventListener('showConfirm', function (event) {
            let itemId = event.detail[0].itemId;
            Swal.fire({
                title: "Delete Season?",
                text: "Are you sure you want to delete the season?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, Delete it!"
            }).then((result) => {
                if (result.isConfirmed) {
                    @this.call('DestroyData', itemId);
                }
            });
        });
    </script>
@endsection