<div class="container mt-4">

    <div class="row mb-4">
        <div class="col-auto my-auto">
            <div class="h-100">
                
                <h5 class="mb-1">Internal Tools</h5>

                <div class="d-flex align-items-center gap-1">
                    <small class="text-muted fw-medium">Admin</small>

                    <span class="text-muted">/</span>

                    <small class="fw-medium">
                        <a href="{{ route('admin.admin_internal_tools.developer_settings') }}" class="text-primary">
                            Developer Settings
                        </a>
                    </small>
                </div>

            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">

            <table class="table table-bordered text-center">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Tool Name</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($tools as $index => $tool)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $tool['title'] }}</td>
                            <td>
                                <a href="{{ route($tool['route']) }}" 
                                   class="btn btn-sm btn-primary">
                                    Go
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>

            </table>

        </div>
    </div>

</div>