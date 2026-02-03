<div>
    @if (session()->has('message'))
        <div class="alert alert-success">
            {{ session('message') }}
        </div>
    @endif

    <div class="card my-4">
        <div class="card-body mx-4">
            <h4 class="mb-4">Reset API Token</h4>

            <button
                class="btn btn-primary mb-3"
                wire:click="generateToken"
                wire:loading.attr="disabled"
            >
                Generate / Reset Token
            </button>

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
                        Copy and store this token securely.
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

<script>
function copyToken(btn) {
    const token = document.getElementById('generatedToken').value;
    navigator.clipboard.writeText(token).then(() => {
        btn.innerText = 'Copied ✓';
        setTimeout(() => btn.innerText = 'Copy', 2000);
    });
}
</script>
