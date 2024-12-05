<div id="layoutSidenav_nav">
    <nav class="sidenav shadow-right sidenav-light">
        <div class="sidenav-menu">
            @if(\Auth::user()->type === 'SO')

            <div class="nav accordion" id="accordionSidenav">
                <!-- Sidenav Menu Heading (Home)-->
                 <div class="sidenav-menu-heading">Home</div>
                 <!-- Sidenav Accordion (Home) -->
                 <a class="nav-link" href="{{ url('/home') }}">
                     <div class="nav-link-icon"><i class="fas fa-fw fa-home"></i></div>
                     Home
                 </a>
                 <a class="nav-link" href="{{url('/mtc/order')}}">
                     <div class="nav-link-icon"><i class="fas fa-wrench"></i></div>
                     Maintenance Order
                 </a>
             </div>
@else
<div class="nav accordion" id="accordionSidenav">
    <!-- Sidenav Menu Heading (Home)-->
     <div class="sidenav-menu-heading">Home</div>
     <!-- Sidenav Accordion (Home) -->
     <a class="nav-link" href="{{ url('/home') }}">
         <div class="nav-link-icon"><i class="fas fa-fw fa-home"></i></div>
         Home
     </a>

     <a class="nav-link" href="{{url('/dies/list')}}">
         <div class="nav-link-icon"><i class="fas fa-pallet"></i></div>
         PM & Daily Report
     </a>
     <a class="nav-link" href="{{url('/task')}}">
        <div class="nav-link-icon"><i class="fas fa-tasks"></i></div>
        Task List
    </a>
     @if(\Auth::user()->role === 'Super Admin' || \Auth::user()->role === 'IT')

     <a class="nav-link" href="{{url('/mtc/order')}}">
         <div class="nav-link-icon"><i class="fas fa-wrench"></i></div>
         Maintenance Order
     </a>
     @endif
     <!-- Sidenav Accordion (Inventory) -->
     <a class="nav-link collapsed" href="javascript:void(0);" data-bs-toggle="collapse" data-bs-target="#collapseInventory" aria-expanded="false" aria-controls="collapseInventory">
         <div class="nav-link-icon"><i class="fas fa-info"></i></div>
         Info
         <div class="sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
     </a>
     <div class="collapse" id="collapseInventory" data-bs-parent="#accordionSidenav">
         <nav class="sidenav-menu-nested nav">
             <a class="nav-link" href="{{ url('/inventory/raw-material') }}">Actual Production</a>
         </nav>
     </div>

     @if(\Auth::user()->role === 'Super Admin' || \Auth::user()->role === 'IT')
      <!-- Sidenav Menu Heading (Master)-->
      <div class="sidenav-menu-heading">Master</div>
      <!-- Sidenav Accordion (Master)-->
      <a class="nav-link collapsed" href="javascript:void(0);" data-bs-toggle="collapse" data-bs-target="#collapsemaster" aria-expanded="false" aria-controls="collapsemaster">
          <div class="nav-link-icon"><i class="fas fa-database"></i></div>
          Master Data
          <div class="sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
      </a>
      <div class="collapse" id="collapsemaster" data-bs-parent="#accordionSidenav">
          <nav class="sidenav-menu-nested nav">
             <a class="nav-link" href="{{url('/master/product')}}">Master Product</a>
          </nav>
          <nav class="sidenav-menu-nested nav">
             <a class="nav-link" href="{{url('/master/stroke')}}">Master Stroke</a>
          </nav>
      </div>

      @endif
     @if(\Auth::user()->role === 'IT')
     <!-- Sidenav Menu Heading (Core)-->
     <div class="sidenav-menu-heading">Configuration</div>
     <!-- Sidenav Accordion (Utilities)-->
     <a class="nav-link collapsed" href="javascript:void(0);" data-bs-toggle="collapse" data-bs-target="#collapseUtilities" aria-expanded="false" aria-controls="collapseUtilities">
         <div class="nav-link-icon"><i data-feather="tool"></i></div>
         Master Configuration
         <div class="sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
     </a>
     <div class="collapse" id="collapseUtilities" data-bs-parent="#accordionSidenav">
         <nav class="sidenav-menu-nested nav">
             <a class="nav-link" href="{{url('/dropdown')}}">Dropdown</a>
             <a class="nav-link" href="{{url('/rule')}}">Rules</a>
             <a class="nav-link" href="{{url('/user')}}">User</a>
         </nav>
     </div>
     @endif
 </div>
            @endif


        </div>
        <!-- Sidenav Footer-->
        <div class="sidenav-footer">
            <div class="sidenav-footer-content">
                <div class="sidenav-footer-subtitle">Logged in as:</div>
                <div class="sidenav-footer-title">{{ auth()->user()->name }}</div>
            </div>
        </div>
    </nav>
</div>
