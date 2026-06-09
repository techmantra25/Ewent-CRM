<div class="row gy-6">

    <!-- Transactions -->
    <div class="col-lg-12">
      <div class="card h-100">
        <div class="card-header rounded-top shadow-sm">
          <div class="d-flex align-items-center justify-content-between">
            <div>
              <h5 class="card-title m-0 me-2 text-primary">🎉 Dashboard</h5>
              <p class="small mb-0">
                <span class="h6 mb-0">Welcome to the <strong>E-went Portal</strong> 👋</span><br>
                Manage your events, bookings, and performance insights all in one place.
              </p>
            </div>
            <div class="text-end">
              <span class="badge bg-success">Active</span><br>
              {{-- <small>Last login: <span class="text-light">{{ $admin->last_login_at ? $admin->last_login_at->diffForHumans() : 'First login' }}</span></small> --}}
            </div>
          </div>
        </div>
        
        <div class="card-body pt-lg-10">

          <div class="row g-4">

              {{-- Total Vehicles --}}
              <div class="col-xl-3 col-md-6 col-12">

                  <div class="card shadow border-0 h-100 text-white"
                      style="background: linear-gradient(to right, #a8edea, #fed6e3); border-radius:16px;">

                      <div class="card-body">

                          <div class="d-flex justify-content-between align-items-start">

                              <div>

                                  <p class="mb-1 fw-semibold small">

                                      Total Vehicles

                                  </p>

                                  <h3 class="mb-0 fw-bold">

                                      {{ $all_vehicles }}

                                  </h3>

                              </div>

                              <div class="fs-3 text-dark">

                                  <i class="ri-car-line"></i>

                              </div>

                          </div>

                      </div>

                  </div>

              </div>

              {{-- Assigned Vehicles --}}
              <div class="col-xl-3 col-md-6 col-12">

                  <div class="card shadow border-0 h-100 text-white"
                      style="background: linear-gradient(to right, #cbb4d4, #e0c3fc); border-radius:16px;">
                      <div class="card-body">

                          {{-- Top --}}
                          <div class="d-flex justify-content-between align-items-start flex-wrap">

                              <div>

                                  <p class="mb-1 fw-semibold small">

                                      Assigned Vehicles

                                  </p>

                                  <span>
                                    <h3 class="mb-0 fw-bold">
                                        {{ $assigned_vehicles+$overdue_vehicles }} 
                                    </h3>
                                    
                                  </span>
                                  <span class="text-danger">(Overdue: {{$overdue_vehicles}})</span>
                                  <p class="text-dark mb-0"><span><span>(B2C: {{ $b2c_assigned_vehicles + $overdue_vehicles}})</span> (B2B: {{$b2b_assigned_vehicles}})</span> </p>

                              </div>

                              <span class="text-dark small fw-semibold mt-1">

                                  <i class="ri-arrow-up-line"></i>

                                  {{ $assigned_percent+$overdue_percent }}%

                              </span>

                          </div>

                      </div>
                  </div>
              </div>

              {{-- Unassigned Vehicles --}}
              <div class="col-xl-3 col-md-6 col-12">

                  <div class="card shadow border-0 h-100 text-white"
                      style="background: linear-gradient(to right, #fddb92, #d1fdff); border-radius:16px;">

                      <div class="card-body">

                          <div class="d-flex justify-content-between align-items-start">

                              <div>

                                  <p class="mb-1 fw-semibold small">

                                      Unassigned Vehicles

                                  </p>

                                  <h3 class="mb-0 fw-bold">

                                      {{ $unassigned_vehicles }}

                                  </h3>

                              </div>

                              <span class="text-dark small fw-semibold">

                                  <i class="ri-arrow-up-line"></i>

                                  {{ $unassigned_percent }}%

                              </span>

                          </div>

                      </div>

                  </div>

              </div>

              {{-- Overdue Vehicles --}}
              <div class="col-xl-3 col-md-6 col-12">

                  <div class="card shadow border-0 h-100 text-white"
                      style="background: linear-gradient(to right, #fda085, #f6d365); border-radius:16px;">

                      <div class="card-body">

                          <div class="d-flex justify-content-between align-items-start">

                              <div>

                                  <p class="mb-1 fw-semibold small">

                                      Overdue Vehicles

                                  </p>

                                  <h3 class="mb-0 fw-bold">

                                      {{ $overdue_vehicles }}

                                  </h3>

                              </div>

                              <span class="text-dark small fw-semibold">

                                  <i class="ri-error-warning-line"></i>

                                  {{ $overdue_percent }}%

                              </span>

                          </div>

                      </div>

                  </div>

              </div>

          </div>

        </div>
      </div>
    </div>
  </div>