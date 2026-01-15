<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">

  <!-- ! Hide app brand if navbar-full -->
  <div class="app-brand demo">
    <a href="{{url('/')}}" class="app-brand-link">
      {{-- <span class="app-brand-logo demo me-1">
        @include('_partials.macros',["height"=>20])
      </span> --}}
     <img src="{{asset('assets/img/new-logo.png')}}" alt="" style="width: 50px; height: auto;">
      <span class=" demo menu-text fw-semibold ms-2" style="color: #f3f3f5;">Go e-Went</span>
    </a>

    <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
      <i class="menu-toggle-icon d-xl-block align-middle"></i>
    </a>
  </div>

  <div class="menu-inner-shadow"></div>

  <ul class="menu-inner py-1 ps">

    <li class="menu-item {{ (request()->is('organization/dashboard*')) ? 'open' : '' }}">
      <a href="{{route('organization.dashboard')}}" class="menu-link">
        <i class="menu-icon tf-icons ri-home-smile-line"></i>
        <div>Dashboards</div>
      </a>
    </li>
    <li class="menu-item {{ request('type') === 'models' ? 'open' : '' }}">
        <a href="{{ route('organization.dashboard', ['type' => 'models']) }}" class="menu-link">
           <i class="menu-icon tf-icons ri-motorbike-line"></i>
            <div>Models</div>
        </a>
    </li>
    <li class="menu-item {{ request('type') === 'riders' ? 'open' : '' }}">
        <a href="{{ route('organization.dashboard', ['type' => 'riders']) }}" class="menu-link">
            <i class="menu-icon tf-icons ri-user-3-line"></i>
            <div>Riders</div>
        </a>
    </li>
    <li class="menu-item {{ request('type') === 'deposit_history' ? 'open' : '' }}">
        <a href="{{ route('organization.dashboard', ['type' => 'deposit_history']) }}" class="menu-link">
            <i class="menu-icon tf-icons ri-arrow-down-circle-line"></i>
            <div>Deposit History</div>
        </a>
    </li>
    <li class="menu-item {{ request('type') === 'invoice' ? 'open' : '' }}">
        <a href="{{ route('organization.dashboard', ['type' => 'invoice']) }}" class="menu-link">
            <i class="menu-icon tf-icons ri-file-list-3-line"></i>
            <div>Invoice History</div>
        </a>
    </li>

    <li class="menu-item {{ request('type') === 'payment' ? 'open' : '' }}">
        <a href="{{ route('organization.dashboard', ['type' => 'payment']) }}" class="menu-link">
            <i class="menu-icon tf-icons ri-bank-card-line"></i>
            <div>Payment History</div>
        </a>
    </li>
     
  {{-- @endif --}}
    <div class="ps__rail-x" style="left: 0px; bottom: 0px;">
      <div class="ps__thumb-x" tabindex="0" style="left: 0px; width: 0px;"></div>
    </div>
    <div class="ps__rail-y" style="top: 0px; right: 4px;">
      <div class="ps__thumb-y" tabindex="0" style="top: 0px; height: 0px;"></div>
    </div>
  </ul>
</aside>
