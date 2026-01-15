<div>
    
    <style>
      .card-icon {
        font-size: 2rem;
        padding: 10px;
        border-radius: 50%;
        display: inline-block;
        margin-right: 10px;
      }
  
      .deposit-icon {
        background-color: #ffeeba;
        color: #856404;
      }
  
      .rental-icon {
        background-color: #f8d7da;
        color: #721c24;
      }
  
      .refund-icon {
        background-color: #d1ecf1;
        color: #0c5460;
      }
  
      .summary-card {
        min-height: 100px;
      }
    </style>
  
    <!-- Summary Cards -->
      <div class="row text-white mb-4">
          <div class="col-auto my-auto">
              <div class="h-100">
                <h5 class="mb-0">Vehicle Payment Summary</h5>
                <div>
                     <small class="text-dark fw-medium">Dashboard </small>
                     <small class="text-light fw-medium arrow"><a href="{{route('admin.vehicle.list')}}">Vehicles</a></small>
                     @if($model)
                        <small class="text-light fw-medium arrow"><a href="{{route('admin.payment.vehicle.summary',[$model->id])}}">{{$model->title}}</a> </small>
                     @endif
                     @if($vehicle)
                        <small class="text-light fw-medium arrow">{{$vehicle->vehicle_number}}</small>
                     @endif
                </div>
              </div>
          </div>
          <div class="col-lg-4 col-md-6 my-sm-auto ms-sm-auto me-sm-0 mx-auto mt-3">
              <div class="nav-wrapper position-relative end text-end">
                <!-- Back Button -->
                <a class="btn btn-dark btn-sm" href="javascript:history.back();" role="button">
                  <i class="ri-arrow-go-back-line ri-16px me-0 me-sm-2 align-baseline"></i>
                  Back
                </a>
              </div>
          </div>
          <div class="col-lg-12 justify-content-left">
              <div class="row">
                  @if(session()->has('message'))
                      <div class="alert alert-success" id="flashMessage">
                          {{ session('message') }}
                      </div>
                  @endif
                  @if(session()->has('error'))
                      <div class="alert alert-danger" id="flashMessage">
                          {{ session('error') }}
                      </div>
                  @endif
              </div>
          </div>
      </div>
  
    <!-- Data Table -->
    <div class="col-lg-12 col-md-6 mb-md-0 mb-4">
      <div class="row">
        <div class="col-12">
          <div class="card my-4">
            <div class="card-header pb-0">
              <div class="row">
                  <div class="col-lg-2 col-2"></div>
                  
                  <div class="col-lg-10 col-10 my-auto text-end">
                      <div class="d-flex align-items-center justify-content-end flex-wrap gap-2">
                        <div style="max-width: 250px;
                              margin-bottom: 20px;" class="text-start text-uppercase">
                            <label for="vehicle" class="form-label small mb-1">Vehicle</label>
                            <input type="text" wire:model="vehicle_number" class="form-control border border-2 p-2 custom-input-sm" placeholder="Search by vehicle" wire:keyup="keyVehicle($event.target.value)">
                        </div>
                          <div style="max-width: 250px;
                              margin-bottom: 20px;" class="text-start text-uppercase">
                                   <label for="startDate" class="form-label small mb-1">Models</label>
                              <select
                                  class="form-select border border-2 p-2 custom-input-sm" wire:model="model_id" wire:change="FilterModel($event.target.value)">
                                  <option value="" selected hidden>Select model</option>
                                  @foreach($models as $model_item)
                                  <option value="{{ $model_item->id }}">{{$model_item->category->title}}|{{ $model_item->title }}</option>
                                  @endforeach
                              </select>
                          </div>
                          <!-- Start Date -->
                          <div style="max-width: 250px;
                              margin-bottom: 20px;" class="text-start text-uppercase">
                              <label for="startDate" class="form-label small mb-1">Start Date</label>
                              <input type="date" wire:model="start_date" id="startDate" wire:change="updateStartDate($event.target.value)" class="form-control border-2 p-2 custom-input-sm">
                          </div>
                              
                          <div style="max-width: 250px;
                              margin-bottom: 20px;" class="text-start text-uppercase">
                              <label for="endDate" class="form-label small mb-1">End Date</label>
                              <input type="date" wire:model="end_date" id="endDate" wire:change="updateEndDate($event.target.value)" class="form-control border-2 p-2 custom-input-sm">
                          </div>
                          <!-- Search Button -->
                          {{-- <button type="button" wire:click="searchButtonClicked"
                              class="btn btn-dark text-white custom-input-sm">
                              <i class="ri-search-line"></i>
                          </button> --}}
                      
                          <!-- Reset Button -->
                          <a href="javascript:void(0)"
                              class="btn btn-danger text-white custom-input-sm" wire:click="resetPageField">
                              <i class="ri-restart-line"></i>
                          </a>
                      
                          <!-- Export Button -->
                          <button type="button" wire:click="exportAll"
                              class="btn btn-primary text-white custom-input-sm">
                              <i class="ri-download-2-line me-1"></i> Export All
                          </button>
                  
                      </div>
                  </div>
              </div>
                
              <div class="card-body">
                <div class="accordion accordion-arrow-left">
                  <div class="accordion-item">
                    <div class="accordion-collapse">
                      <div class="accordion-body table-responsive text-nowrap p-0">
                        <table class="table table-striped">
                          <thead>
                              <tr>
                                <th class="h6">Vehicle</th>
                                <th class="h6">Start Date</th>
                                <th class="h6">End Date</th>
                                <th class="h6">Rent</th>
                                <th class="h6">Rent Status</th>
                                <th class="h6">unassigned at</th>
                                <th class="h6">Rider</th>
                              </tr>
                          </thead>
                          <tbody>
                            @forelse ($history as $item)
                                <tr>
                                  <td class="">
                                      <small class="text-dark"> {{$item->stock?$item->stock->vehicle_number:"N/A"}}</small><br>
                                      <small><code>{{ $item->stock && $item->stock->product ? $item->stock->product->title : "N/A" }}</code></small>

                                  </td>
                                  <td class="">
                                      <small class="text-muted">{{ date('d M y h:i A', strtotime($item->start_date)) }}</small>
                                  </td>
                                  <td class="">
                                      <small class="text-muted">{{ $item->end_date?date('d M y h:i A', strtotime($item->end_date)):"" }}</small>
                                  </td>
                                  <td class="">
                                      @if($item->order && $item->order->user_type === "B2C")
                                          <small class="text-primary fw-bold">
                                              {{ env('APP_CURRENCY', '₹') }}{{ number_format($item->order->rental_amount, 2) }}
                                          </small>
                                      @else
                                        <span class="text-muted">B2B</span>
                                      @endif
                                  </td>
                                  <td class="">
                                    <small class="text-{{$item->status=='overdue'?"danger":"muted"}}">{{ ucwords($item->status)}}</small>
                                  </td>
                                  <td class="">
                                      <small class="text-muted">
                                          @if($item->status === 'returned' && $item->exchanged_at && $item->end_date)
                                              @php
                                                  $endDate = \Carbon\Carbon::parse($item->end_date);
                                                  $returnedDate = \Carbon\Carbon::parse($item->exchanged_at);
                                                  $days = $returnedDate->diffInDays($endDate); // absolute number
                                              @endphp

                                              {{ \Carbon\Carbon::parse($item->exchanged_at)->format('d M y h:i A') }}
                                              @if($item->order && $item->order->user_type === "B2C")
                                                <br>
                                                (
                                                    @if($returnedDate->lt($endDate))
                                                        <span class="text-success">{{ round($days) }} days before</span>
                                                    @elseif($returnedDate->gt($endDate))
                                                      <span class="text-danger"> {{ abs(round($days)) }} days after</span>
                                                    @else
                                                      <span class="text-primary"> On time</span>
                                                    @endif
                                                )
                                              @endif
                                          @else
                                              ----
                                          @endif
                                      </small>
                                  </td>


                                  <td class="">
                                    @if($item->user)
                                        <p class="m-0"><strong>{{$item->user->name}}</strong></p>
                                        <p class="m-0 text-sm text-success"><small>{{$item->user->country_code}} {{$item->user->mobile}}</small></p>
                                        <p class="m-0 text-sm text-success"><small>{{$item->user->email}}</small></p>
                                        @if($item->order && $item->order->user_type === "B2B")
                                          <p class="badge rounded-pill badge-center bg-label-primary">
                                              ORG: <span class="text-dark"><a href="{{route('admin.organization.dashboard',$item->user->organization_details->id)}}">{{ optional($item->user->organization_details)->name ?? 'N/A' }}</a> </span>
                                          </p>
                                        @endif
                                    @else
                                        <p class="m-0">N/A</p>
                                        </p>
                                    @endif
                                    
                                  </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7">
                                      <div class="alert alert-danger">
                                        Data not found!
                                      </div>
                                    </td>
                                </tr>
                            @endforelse
                          </tbody>
                        </table>
                        <div class="d-flex justify-content-end mt-2">
                          {{ $history->links() }}
                      </div>
                      </div>
                    </div>
                  </div>
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
  