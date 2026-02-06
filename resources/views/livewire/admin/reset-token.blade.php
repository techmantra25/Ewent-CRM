<div>
    @if (session()->has('message'))
        <div class="alert alert-success">
            {{ session('message') }}
        </div>
    @endif

    <div class="card my-4">
        <div class="card-body mx-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="mb-0">Reset API Token</h4>

                <button
                    class="btn btn-danger"
                    type="button"
                    onclick="confirmGenerateToken({{ $generatedToken ? 'true' : 'false' }})"
                    wire:loading.attr="disabled"
                >
                    {{ $generatedToken ? 'Reset Token' : 'Generate Token' }}
                </button>
            </div>

            @if ($generatedToken)
                <div class="mb-3">
                    <label class="form-label">API Token</label>

                    <div class="input-group">
                        <input
                            type="text"
                            id="generatedToken"
                            class="form-control bg-light"
                            value="{{ $generatedToken }}"
                            readonly
                        >
                        <button
                            class="btn btn-outline-success"
                            type="button"
                            onclick="copyToken(this)"
                        >
                            Copy
                        </button>
                    </div>

                    <small class="text-muted">
                        Copy and store this token securely. You won’t be able to see it again if changed.
                    </small>
                </div>
            @else
                <div class="alert alert-info">
                    No API token available. Generate one to continue.
                </div>
            @endif

        </div>
    </div>

    <div wire:loading class="loader-container">
        <div class="loader"></div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function confirmGenerateToken(hasToken) {
        if (!hasToken) {
            @this.call('generateToken');
            return;
        }

        Swal.fire({
            title: "API Token Already Exists",
            text: "Generating a new token may break existing APIs. Do you really want to reset it?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "Yes, reset token",
            cancelButtonText: "Cancel"
        }).then((result) => {
            if (result.isConfirmed) {
                @this.call('generateToken');
            }
        });
    }

    function copyToken(btn) {
        const token = document.getElementById('generatedToken').value;
        navigator.clipboard.writeText(token).then(() => {
            btn.innerText = 'Copied ✓';
            setTimeout(() => btn.innerText = 'Copy', 2000);
        });
    }
</script>

