<div>
    <style>
        .custom-select-icon-style::before {
            font-size: 16px;
            font-weight: 700;
            padding: 0px 3px;
        }
        .active-rider-type{
            background-color: #8c57ff;
            color: #fff;
        }
        .check-icon {
            width: 20px;
            height: 20px;
            border-radius: 6px;
            border: 1px solid;
            background-color: #fff; /* light gray default */
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.25s ease;
        }

        .check-icon.checked {
            background-color: #8b5cf6; /* purple (Tailwind’s violet-500) */
            box-shadow: 0 0 6px rgba(139, 92, 246, 0.5);
        }

        .check-icon.checked::after {
            content: "✓";
            color: white;
            font-size: 12px;
            font-weight: bold;
        }
        .custom-search-input {
            max-width: 300px;
            margin-right: 8px;
            height: 10px;
            border-radius: 25px;
        }

    </style>
    <div class="d-flex justify-content-start align-items-center mb-2">
      <div class="btn-group" role="group" aria-label="Basic example">
        <button type="button" class="btn {{$user_type=="B2C" ? 'active-rider-type' : 'btn-label-secondary'}} waves-effect btn-sm" wire:click="changeUserType('B2C')" >B2C</button>
        <button type="button" class="btn {{$user_type=="B2B" ? 'active-rider-type' : 'btn-label-secondary'}} waves-effect btn-sm" wire:click="changeUserType('B2B')">B2B</button>
      </div>
    </div>
    <div class="row">
      <!-- Left Side (Customer Tabs) -->
      <div class="col-md-8">
        <div class="card shadow-sm">
          <div class="card-header p-3">
            <ul class="nav nav-tabs card-header-tabs" role="tablist">
                  @php
                      $overdueCount = 1365;
                  @endphp
              <li class="nav-item" wire:click="changeTab('all')">
                <a class="nav-link {{$tab=='all'?'active':''}}" data-bs-toggle="tab" href="#tab-all" role="tab">
                  All <span class="badge rounded-pill badge-center h-px-20 w-px-{{ strlen($all_users->total()) }}0 bg-label-secondary ms-1_5 pt-50">{{ $all_users->total() }}</span>
                </a>
              </li>
              <li class="nav-item" wire:click="changeTab('unassigned')">
                <a class="nav-link {{$tab=='unassigned'?'active':''}}" data-bs-toggle="tab" href="#tab-unassigned" role="tab">
                  Unassigned <span class="badge rounded-pill badge-center h-px-20 w-px-{{ strlen($unassigned_users->total()) }}0 bg-label-warning ms-1_5 pt-50">{{ $unassigned_users->total() }}</span>
                </a>
              </li>
              <li class="nav-item" wire:click="changeTab('assigned')">
                <a class="nav-link {{$tab=='assigned'?'active':''}}" data-bs-toggle="tab" href="#tab-assigned" role="tab">
                  Assigned <span class="badge rounded-pill badge-center h-px-20 w-px-{{ strlen($assigned_users->total()) }}0 bg-label-success ms-1_5 pt-50">{{ $assigned_users->total() }}</span>
                </a>
              </li>
              <li class="nav-item" wire:click="changeTab('overdue')">
                <a class="nav-link {{$tab=='overdue'?'active':''}}" data-bs-toggle="tab" href="#tab-overdue" role="tab">
                  Overdue <span class="badge rounded-pill badge-center h-px-20 w-px-{{ strlen($overdue_users->total()) }}0 bg-label-danger ms-1_5 pt-50">{{ $overdue_users->total() }}</span>
                </a>
              </li>
              <li class="nav-item" wire:click="changeTab('custom select')">
                <a class="nav-link {{$tab=='custom select'? 'active' :''}}" data-bs-toggle="tab" href="#tab-custom select" role="tab">
                  Custom Select 
                <i class="menu-icon tf-icons ri-group-line custom-select-icon-style"></i>
                </a>
              </li>
            </ul>
          </div>

          <div class="tab-content p-3">
            <!-- All Customers -->
            <div class="tab-pane fade {{$tab=='all'?'show active':''}}" id="tab-all" role="tabpanel">
              <ul class="list-group list-group-flush">
                  @forelse ($all_users as $all_user)
                    @php
                      $vehicleStatus = FetchUserVehicleStatus($all_user->id);
                    @endphp
                      <li class="list-group-item d-flex align-items-center cursor-pointer py-0">
                          <div>
                              <h6 class="mb-0"><a href="{{ route('admin.customer.details', $all_user->id) }}" target="_blank"> {{ ucwords($all_user->name) }}</a></h6>
                              <small class="text-muted">{{ $all_user->country_code }}{{ $all_user->mobile }} </small>
                              @if($user_type=="B2B")
                              | <small class="badge rounded-pill badge-center bg-label-danger">
                                  ORG: <span class="text-dark"><a href="{{route('admin.organization.dashboard', $all_user->organization_details->id)}}" class="text-danger"> {{ucwords($all_user->organization_details->name)}} </a></span>
                              </small>
                              @endif
                          </div>
                          <span class="badge bg-label-{{ $vehicleStatus['color'] }} rounded-pill ms-auto"
                              data-bs-toggle="tooltip"
                              data-bs-placement="top"
                              data-bs-html="true"
                              data-bs-original-title="{!! $vehicleStatus['tooltip'] !!}">
                            {{ ucwords($vehicleStatus['status']) }}
                        </span>
                      </li>
                  @empty
                      <div class="alert alert-warning">No riders will appear here.</div>
                  @endforelse
              </ul>
              <div class="d-flex justify-content-end mt-3 paginator">
                  {{ $all_users->links() }}
              </div>
            </div>

            <!-- Unassigned -->
            <div class="tab-pane fade {{$tab=='unassigned'?'show active':''}}" id="tab-unassigned" role="tabpanel">
              <ul class="list-group list-group-flush">
                  @forelse ($unassigned_users as $unassigned_user)
                    @php
                      $vehicleStatus = FetchUserVehicleStatus($unassigned_user->id);
                    @endphp
                      <li class="list-group-item d-flex align-items-center cursor-pointer py-0">
                          <div>
                              <h6 class="mb-0"><a href="{{ route('admin.customer.details', $unassigned_user->id) }}" target="_blank"> {{ ucwords($unassigned_user->name) }}</a></h6>
                              <small class="text-muted">{{ $unassigned_user->country_code }}{{ $unassigned_user->mobile }}</small>
                              @if($user_type=="B2B")
                              | <small class="badge rounded-pill badge-center bg-label-danger">
                                  ORG: <span class="text-dark"><a href="{{route('admin.organization.dashboard', $unassigned_user->organization_details->id)}}" class="text-danger"> {{ucwords($unassigned_user->organization_details->name)}} </a></span>
                              </small>
                              @endif
                          </div>
                          <span class="badge bg-label-{{ $vehicleStatus['color'] }} rounded-pill ms-auto"
                              data-bs-toggle="tooltip"
                              data-bs-placement="top"
                              data-bs-html="true"
                              data-bs-original-title="{!! $vehicleStatus['tooltip'] !!}">
                            {{ ucwords($vehicleStatus['status']) }}
                        </span>
                      </li>
                  @empty
                      <div class="alert alert-warning">No riders will appear here.</div>
                  @endforelse
              </ul>
              <div class="d-flex justify-content-end mt-3 paginator">
                  {{ $unassigned_users->links() }}
              </div>
            </div>

            <!-- Assigned -->
            <div class="tab-pane fade {{$tab=='assigned'?'show active':''}}" id="tab-assigned" role="tabpanel">
              <ul class="list-group list-group-flush">
                  @forelse ($assigned_users as $assigned_user)
                    @php
                      $vehicleStatus = FetchUserVehicleStatus($assigned_user->id);
                    @endphp
                      <li class="list-group-item d-flex align-items-center cursor-pointer py-0">
                          <div>
                              <h6 class="mb-0"><a href="{{ route('admin.customer.details', $assigned_user->id) }}" target="_blank"> {{ ucwords($assigned_user->name) }}</a></h6>
                              <small class="text-muted">{{ $assigned_user->country_code }}{{ $assigned_user->mobile }}</small>
                              @if($user_type=="B2B")
                              | <small class="badge rounded-pill badge-center bg-label-danger">
                                  ORG: <span class="text-dark"><a href="{{route('admin.organization.dashboard', $assigned_user->organization_details->id)}}" class="text-danger"> {{ucwords($assigned_user->organization_details->name)}} </a></span>
                              </small>
                              @endif
                          </div>
                          <span class="badge bg-label-{{ $vehicleStatus['color'] }} rounded-pill ms-auto"
                              data-bs-toggle="tooltip"
                              data-bs-placement="top"
                              data-bs-html="true"
                              data-bs-original-title="{!! $vehicleStatus['tooltip'] !!}">
                            {{ ucwords($vehicleStatus['status']) }}
                        </span>
                      </li>
                  @empty
                      <div class="alert alert-warning">No riders will appear here.</div>
                  @endforelse
              </ul>
              <div class="d-flex justify-content-end mt-3 paginator">
                {{$assigned_users->links()}}
              </div>
            </div>

            <!-- Overdue -->
            <div class="tab-pane fade {{$tab=='overdue'?'show active':''}}" id="tab-overdue" role="tabpanel">
              <ul class="list-group list-group-flush">
                  @forelse ($overdue_users as $overdue_user)
                    @php
                      $vehicleStatus = FetchUserVehicleStatus($overdue_user->id);
                    @endphp
                      <li class="list-group-item d-flex align-items-center cursor-pointer py-0">
                          <div>
                              <h6 class="mb-0"><a href="{{ route('admin.customer.details', $overdue_user->id) }}" target="_blank"> {{ ucwords($overdue_user->name) }}</a></h6>
                              <small class="text-muted">{{ $overdue_user->country_code }}{{ $overdue_user->mobile }}</small>
                              @if($user_type=="B2B")
                              | <small class="badge rounded-pill badge-center bg-label-danger">
                                  ORG: <span class="text-dark"><a href="{{route('admin.organization.dashboard', $overdue_user->organization_details->id)}}" class="text-danger"> {{ucwords($overdue_user->organization_details->name)}} </a></span>
                              </small>
                              @endif
                          </div>
                          <span class="badge bg-label-{{ $vehicleStatus['color'] }} rounded-pill ms-auto"
                              data-bs-toggle="tooltip"
                              data-bs-placement="top"
                              data-bs-html="true"
                              data-bs-original-title="{!! $vehicleStatus['tooltip'] !!}">
                            {{ ucwords($vehicleStatus['status']) }}
                        </span>
                      </li>
                  @empty
                      <div class="alert alert-warning">No riders will appear here.</div>
                  @endforelse
              </ul>
              <div class="d-flex justify-content-end mt-3 paginator">
                {{$overdue_users->links()}}
              </div>
            </div>
            <!-- Custom Select -->
            <div class="tab-pane fade {{$tab=='custom select'?'show active':''}}" id="tab-custom_select" role="tabpanel"> 
              
              <div class="d-flex justify-content-end align-items-center mb-2 container mx-1">
                
                <!-- Search Input -->
               <div style="position: relative; width: 100%; justify-content: end; display: flex; align-items: center;" class="px-3">
                  <input 
                    type="text" 
                    id="searchInput"
                    class="form-control w-100 custom-search-input me-2 pe-4" 
                    placeholder="Search riders..." 
                    wire:keyup="searchUsers($event.target.value)"
                  />
                  <span class="badge badge-center rounded-pill bg-label-danger d-{{$search?"block":"none"}} cursor-pointer" wire:click='clearSearch'>
                    <i class="icon-base ri ri-close-line"></i>
                  </span>
                </div>
                <!-- Select All Badge -->
                <p 
                    class="badge bg-{{ count($selectedUsers) === \App\Models\User::where('user_type', $user_type)
                        ->where('name', 'like', "%{$search}%")
                        ->count() ? 'primary' : 'danger' }} rounded-pill cursor-pointer mb-0"
                    wire:click="toggleSelectAll"
                >
                    <i class="menu-icon tf-icons ri-group-line custom-select-icon-style"></i>
                    {{ count($selectedUsers) === \App\Models\User::where('user_type', $user_type)
                        ->where('name', 'like', "%{$search}%")
                        ->count() ? 'Selected All' : 'Select All' }}
                </p>

            </div>
              <ul class="list-group list-group-flush">
                  @forelse ($all_users as $all_user)
                    @php
                      $vehicleStatus = FetchUserVehicleStatus($all_user->id);
                    @endphp
                    <li class="list-group-item d-flex align-items-center cursor-pointer py-0"
                        wire:key="user-{{ $all_user->id }}"
                        wire:click="toggleUserSelection({{ $all_user->mobile }})">
                        <!-- Custom Checkbox -->
                        <div class="check-icon me-3 {{ in_array($all_user->mobile, $selectedUsers) ? 'checked' : '' }}"></div>

                        <!-- User Info -->
                        <div>
                            <h6 class="mb-0">
                                <a href="{{ route('admin.customer.details', $all_user->id) }}" target="_blank">
                                    {{ ucwords($all_user->name) }}
                                </a>
                            </h6>
                            <small class="text-muted">{{ $all_user->country_code }}{{ $all_user->mobile }}</small>
                            @if($user_type == "B2B")
                                | <small class="badge rounded-pill badge-center bg-label-danger">
                                    ORG:
                                    <span class="text-dark">
                                        <a href="{{ route('admin.organization.dashboard', $all_user->organization_details->id) }}" class="text-danger">
                                            {{ ucwords($all_user->organization_details->name) }}
                                        </a>
                                    </span>
                                </small>
                            @endif
                        </div>

                        <span class="badge bg-label-{{ $vehicleStatus['color'] }} rounded-pill ms-auto"
                            data-bs-toggle="tooltip"
                            data-bs-placement="top"
                            data-bs-html="true"
                            data-bs-original-title="{!! $vehicleStatus['tooltip'] !!}">
                            {{ ucwords($vehicleStatus['status']) }}
                        </span>
                    </li>
                  @empty
                      <div class="alert alert-warning">No riders will appear here.</div>
                  @endforelse
              </ul>
               <div class="d-flex justify-content-end mt-3 paginator">
                {{$all_users->links()}}
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Right Side (Send Message) -->
      <div class="col-md-4">
        <div class="card shadow-sm">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Send Message</h5>
            <span class="badge bg-primary">Selected: {{ count($selectedUsers) }} Riders</span>
          </div>
          <div class="card-body">
            <form id="pushNotificationForm" wire:submit.prevent="sendPushNotificationForm">
              <div>
                  <label for="messageText" class="form-label">Message</label>
                  <textarea 
                      class="form-control" 
                      id="messageText" 
                      rows="5" 
                      placeholder="Type your message..." 
                      wire:keyup="messageText($event.target.value)"></textarea>
              </div>
              <small class="form-label d-block mt-1">
                  Selected rider(s) will receive this message.
              </small>
              <button 
                  type="button" 
                  class="btn btn-success w-100 mt-3" 
                  id="sendBtn"
                  @if(count($selectedUsers) === 0 || !$message) disabled @endif>
                  <i class="fas fa-paper-plane me-2"></i>Send
              </button>
          </form>

             {{-- ✅ Success Alert --}}
            @if (session()->has('success'))
              <div class="alert alert-success mt-3" id="successAlert">
                {{ session('success') }}
              </div>
            @endif

              {{--  Static table for sent messages --}}
              <div class="table-responsive text-nowrap mt-3">
                <table class="table table-sm mb-0">
                  <tbody>
                    @forelse($sendNotifications as $notification)
                    <tr>
                      <td>
                        <div class="fw-bold text-dark">{{ $notification->message }}</div>
                        <div class="small text-muted mt-1">
                          <i class="ri-time-line me-1"></i> {{ $notification->created_at->format('d M Y, h:i A') }}
                        </div>
                        <div class="small text-muted">
                          <i class="ri-group-line me-1"></i> Sent to <strong>{{ $notification->recipient_count }} riders</strong>
                        </div>
                        <div class="mt-1">
                          <span class="badge bg-label-primary me-1">{{ ucfirst($notification->rider_type) }}</span>
                          <span class="badge bg-label-warning">{{ $notification->status }}</span>
                        </div>
                      </td>
                    </tr>
                    @empty
                    <tr>
                      <td>
                        <div class="text-muted">No notifications sent yet.</div>
                      </td>
                    </tr>
                    @endforelse
                  </tbody>
                </table>
              </div>


          </div>
        </div>
      </div>

    </div>
    <div class="loader-container" wire:target="changeTab, toggleUserSelection, toggleSelectAll,changeUserType,clearSearch, sendPushNotificationForm" wire:loading>
      <div class="loader"></div>
    </div>
</div>
@section('page-script')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })

    Livewire.on('clear-search-input', () => {
        document.getElementById('searchInput').value = '';
    });
    Livewire.on('notification-send', () => {
      document.getElementById('messageText').value = '';
    });
</script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.getElementById('sendBtn').addEventListener('click', function() {
        Swal.fire({
            title: 'Are you sure?',
            text: "Do you want to send this message to the selected rider(s)?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, send it!'
        }).then((result) => {
            if (result.isConfirmed) {
                // Trigger Livewire form submission
                @this.call('sendPushNotificationForm');
            }
        });
    });
</script>

@endsection