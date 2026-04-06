<div class="container-fluid">

    {{-- HEADER --}}
    <div class="row mb-4 align-items-center">
        <div class="col-auto my-auto">
            <h5 class="mb-1">Admin Internal Tools</h5>

            <div class="d-flex gap-1 text-muted">
                <small>Developer Settings</small>
                <span>/</span>
                <small class="text-primary">Failed Payment Captured</small>
            </div>
        </div>

        <div class="col text-end">
            <a href="{{ route('admin.admin_internal_tools.developer_settings') }}" 
               class="btn btn-dark btn-sm">
                ← Back
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="row">
                {{-- LEFT SIDE --}}
                <div class="col-md-8">
                    <div class="border p-3 rounded">
                        @if($errorMessage)
                            <div class="alert alert-danger">{!! $errorMessage !!}</div>
                        @endif
                        @if($successMessage)
                            <div class="alert alert-success">{!! $successMessage !!}</div>
                        @endif

                        @if($result)
                            <h6 class="mt-3">Account Details</h6>
                            <table class="table table-bordered">

                                {{-- Rider --}}
                                @if(data_get($result, 'order.user'))
                                <tr>
                                    <th>Rider Details</th>
                                    <td colspan="3">
                                        <div>
                                            <a href="{{ url('admin/rider/details/'.data_get($result,'order.user.id')) }}">
                                                {{ data_get($result,'order.user.name','-') }}
                                            </a>
                                            <br>
                                            <small>
                                                {{ data_get($result,'order.user.email','-') }} |
                                                {{ data_get($result,'order.user.mobile','-') }}
                                            </small>
                                        </div>
                                    </td>
                                </tr>
                                @endif

                                {{-- Organization --}}
                                @if(data_get($result,'organization'))
                                <tr>
                                    <th>Organization</th>
                                    <td colspan="3">
                                        {{ data_get($result,'organization.name','-') }}
                                    </td>
                                </tr>
                                @endif

                                {{-- Amount --}}
                                <tr>
                                    <th>Amount</th>
                                    <td colspan="3">
                                        {{ env('APP_CURRENCY') }}{{ $result->amount ?? '-' }}
                                    </td>
                                </tr>

                                {{-- Transaction Date --}}
                                <tr>
                                    <th>Transaction Date</th>
                                    <td colspan="3">
                                        {{ optional($result->created_at)->format('d M Y, h:i A') ?? '-' }}
                                    </td>
                                </tr>

                            </table>
                        @endif
                       @if($finalStatus)
                            <div class="mt-3">
                                <h6>Transaction Details</h6>

                                <table class="table table-bordered">

                                    <tr>
                                        <th>Transaction ID</th>
                                        <td>{{ data_get($finalStatus, 'txnID', '-') }}</td>
                                    </tr>

                                    <tr>
                                        <th>Merchant Txn No</th>
                                        <td>{{ data_get($finalStatus, 'merchantTxnNo', '-') }}</td>
                                    </tr>

                                    <tr>
                                        <th>Amount</th>
                                        <td>{{ data_get($finalStatus, 'amount', '-') }}</td>
                                    </tr>

                                    <tr>
                                        <th>Status</th>
                                        <td>
                                            <span class="badge bg-success">
                                                {{ data_get($finalStatus, 'txnStatus', '-') }}
                                            </span>
                                        </td>
                                    </tr>

                                    <tr>
                                        <th>Response Message</th>
                                        <td>{{ data_get($finalStatus, 'txnRespDescription', '-') }}</td>
                                    </tr>

                                    <tr>
                                        <th>Payment Mode</th>
                                        <td>{{ data_get($finalStatus, 'paymentMode', '-') }}</td>
                                    </tr>

                                    <tr>
                                        <th>UPI / Account</th>
                                        <td>{{ data_get($finalStatus, 'paymentInstId', '-') }}</td>
                                    </tr>

                                    <tr>
                                        <th>Customer Email</th>
                                        <td>{{ data_get($finalStatus, 'customerEmailID', '-') }}</td>
                                    </tr>

                                    <tr>
                                        <th>Customer Mobile</th>
                                        <td>{{ data_get($finalStatus, 'customerMobileNo', '-') }}</td>
                                    </tr>

                                    <tr>
                                        <th>Transaction Date</th>
                                        <td>
                                            {{ \Carbon\Carbon::createFromFormat('YmdHis', data_get($finalStatus, 'paymentDateTime', ''))->format('d M Y, h:i A') ?? '-' }}
                                        </td>
                                    </tr>

                                </table>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- RIGHT SIDE --}}
                <div class="col-md-4">
                    <div class="border p-3 rounded">

                        {{-- Payment Type --}}
                        <div class="mb-3">
                            <label class="form-label">Payment Type *</label>

                            <select class="form-select" wire:model="payment_type" @disabled($is_success)>
                                <option value="">Select Type</option>
                                <option value="rider_subscription">Rider Subscription</option>
                                <option value="organization_subscription">Organization Subscription</option>
                                <option value="organization_deposit">Organization Deposit</option>
                            </select>

                            @error('payment_type') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        {{-- Merchant Ref --}}
                        @if($payment_type)
                        <div class="mb-3">
                            <label class="form-label">Merchant Ref *</label>

                            <textarea class="form-control"
                                wire:model.defer="merchant_ref" @disabled($is_success)></textarea>

                            @error('merchant_ref') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        @endif

                        {{-- Transaction ID --}}
                        @if($isactiveTransactionNumber)
                        <div class="mb-3">
                            <label class="form-label">Transaction ID *</label>

                            <input type="text" class="form-control"
                                   wire:model.defer="transaction_id" @disabled($is_success)>

                            @error('transaction_id') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        @endif

                        {{-- Button --}}
                        @if($is_success)

                            <div class="d-grid gap-2">

                                {{-- Capture Button --}}
                                <button 
                                    class="btn btn-success"
                                    wire:click="capturePayment"
                                    wire:loading.attr="disabled">

                                    <span wire:loading.remove>Captured Now</span>
                                    <span wire:loading>Processing...</span>
                                </button>

                                {{-- Reset Button --}}
                                <button 
                                    class="btn btn-secondary"
                                    wire:click="resetFormData"
                                    wire:loading.attr="disabled">

                                    Reset
                                </button>

                            </div>

                        @else

                            <button 
                                class="btn btn-primary w-100"
                                wire:click="searchPayment"
                                wire:loading.attr="disabled">

                                <span wire:loading.remove>Search</span>
                                <span wire:loading>Processing...</span>
                            </button>

                        @endif
                    </div>
                </div>

            </div>

        </div>
    </div>

    {{-- Loader --}}
    <div class="loader-container" wire:loading>
      <div class="loader"></div>
    </div>

</div>