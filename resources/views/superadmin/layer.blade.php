<!DOCTYPE html>
<html lang="en">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title> iParkRv </title>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <!-- endinject -->
  <link rel="shortcut icon" href="{{URL::to('/')}}/public/super_admin/images/favicon.png" />
  <!-- link css start -->
  <link rel="stylesheet" href="{{URL::to('/')}}/public/super_admin/css/vendors/css/materialdesignicons.min.css">
  <link rel="stylesheet" href="{{URL::to('/')}}/public/super_admin/css/vendors/css/themify-icons.css">
  <link rel="stylesheet" href="{{URL::to('/')}}/public/super_admin/css/vendors/css/typicons.css">
  <link rel="stylesheet" href="{{URL::to('/')}}/public/super_admin/css/vendors/css/simple-line-icons.css">
  <link rel="stylesheet" href="{{URL::to('/')}}/public/super_admin/css/vendors/css/font-awesome.min.css">
  <!-- icon css -->
  <link rel="stylesheet" href="{{URL::to('/')}}/public/super_admin/css/select.dataTables.min.css">
  <link rel="stylesheet" href="{{URL::to('/')}}/public/super_admin/css/style.css">
  <link rel="stylesheet" href="{{URL::to('/')}}/public/super_admin/css/custom-style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.12.1/css/jquery.dataTables.css">
  @yield('css_link')
  <!-- link css end -->
  @yield('css_style')
  <style>
    .error{
      color:red;
    }
  </style>
</head>
<body>
  <div class="container-scroller">
    <!-- partial:partials/_navbar.html -->
    <nav class="navbar default-layout header-wrapper col-lg-12 col-12 p-0 fixed-top d-flex align-items-top flex-row">
      <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-start">
        
        <div>
          <a class="navbar-brand brand-logo" href="index.html">
            <img src="{{URL::to('/')}}/public/super_admin/images/dashboard-logo.png" alt="logo" />
          </a>
          <a class="navbar-brand brand-logo-mini" href="index.html">
            <img src="{{URL::to('/')}}/public/super_admin/images/logo-mini.png" alt="logo" />
          </a>
        </div>
      </div>
      <div class="navbar-menu-wrapper d-flex align-items-top"> 
        <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-bs-toggle="offcanvas">
          <span class="mdi mdi-menu"></span>
        </button>
        <ul class="navbar-nav hed-left-menu">
          <li class="nav-item">
            <div class="me-3">
              <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-bs-toggle="minimize">
                <span class="icon-menu"></span>
              </button>
            </div>
          </li>
          
        </ul>
        <ul class="navbar-nav ms-auto hed-right-menu">
          
          <li class="nav-item">
            <form class="search-form" action="#">
              <i class="fa fa-search" aria-hidden="true"></i>
              <input type="search" class="form-control" placeholder="Search Here" title="Search here">
            </form>
          </li>
          
          <li class="nav-item dropdown d-none d-lg-block user-dropdown">
            <a class="nav-link" id="UserDropdown" href="#" data-bs-toggle="dropdown" aria-expanded="false">
              <img class="img-xs rounded-circle" src="{{URL::to('/')}}/public/super_admin/images/face8.jpg" alt="Profile image"> </a>
            <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="UserDropdown">
              <div class="dropdown-header text-center">
                <img class="img-md rounded-circle" src="{{URL::to('/')}}/public/super_admin/images/face8.jpg" alt="Profile image">
                <p class="mb-1 mt-3 font-weight-semibold">{{session()->get('userdata')->user_fn}} </p>
                <p class="fw-light text-muted mb-0">{{session()->get('userdata')->email}}</p>
              </div>
              <a class="dropdown-item"><i class="dropdown-item-icon mdi mdi-account-outline text-primary me-2"></i> My Profile</a>
              <a href="{{route('logout')}}" class="dropdown-item"><i class="dropdown-item-icon mdi mdi-power text-primary me-2"></i>Sign Out</a>
            </div>
          </li>
          <li class="nav-item">
            <a id="fs-doc-button" class="nav-link fullscreen-tigger" href="#"><i class="icon-size-fullscreen"></i></a>
          </li>
          <li class="nav-item">
            <button class="right-sidebar-tigger" type="button">
                <span class="icon-menu"></span>
            </button>
          </li>
        </ul>
        
      </div>
    </nav>
    <!-- partial -->
    <div class="container-fluid page-body-wrapper">
      
      <!-- partial:partials/_sidebar.html -->
      <nav class="sidebar sidebar-offcanvas" id="sidebar">
        <div class="user-panel">
          <div class="user-image">
            <img class="img-fluid" src="{{URL::to('/')}}/public/super_admin/images/face8.jpg" alt="">
          </div>
          <div class="user-info">
            <h4>{{session()->get('userdata')->user_fn}}</h4>
          </div>
        </div>
        
        <ul class="nav">
          <li class="nav-item">
            <a class="nav-link" href="{{route('superadmin.dashboard')}}">
              <span class="menu-icon">
                <img class="normal" src="{{URL::to('/')}}/public/super_admin/images/ad-dashboard-n-icon.png" alt="">
                <img class="hover" src="{{URL::to('/')}}/public/super_admin/images/ad-dashboard-h-icon.png" alt="">
              </span>
              
              <span class="menu-title">Dashboard</span>
            </a>
          </li>
          <li class="nav-item nav-category"> Management</li>
          
          @if(!$user_menus->isEmpty())
            @foreach($user_menus as $menu)
              <li class="nav-item">
                <a class="nav-link" data-bs-toggle="collapse" href="#menu{{$menu->id}}" aria-expanded="false" aria-controls="ui-hosts">
                  <span class="menu-icon">
                    <img class="normal" src="{{URL::to('/')}}/public/super_admin/images/menus/{{$menu->normal_icon}}" alt="" style="width:20px">
                    <img class="hover" src="{{URL::to('/')}}/public/super_admin/images/menus/{{$menu->hover_icon}}" alt="" style="width:20px">
                  </span>
                  <span class="menu-title">{{$menu->name}}</span>
                  <i class="menu-arrow"></i> 
                </a>
                <div class="collapse" id="menu{{$menu->id}}">
                  <ul class="nav flex-column sub-menu">
                    @foreach($user_links as $link)
                      @if($link->link->menu->id == $menu->id)
                        
                        <li class="nav-item"> <a class="nav-link" href="{{route($link->link->link_route)}}">{{$link->link->link_name}}</a></li>
                      @endif
                    @endforeach
                  </ul>
                </div>
              </li>
            @endforeach
          @endif
          
          

          <!--  -->
          
        </ul>
      </nav>

      <!-- partial -->
      <div class="main-panel">
        <div class="content-wrapper">
            @yield('content')
        </div>
        <!-- content-wrapper ends -->
        <!-- partial:partials/_footer.html -->
        <footer class="footer">
          <div class="d-sm-flex justify-content-center">
            <p>COPYRIGHT © 2022 ALL RIGHTS RESERVED.</p>
          </div>
        </footer>
        <!-- partial -->
      </div>
      <!-- main-panel ends -->
      <!-- right sidebar -->
      <div class="right-sidebar-instituties">
        <button class="right-sidebar-tigger" type="button">
            <span class="icon-menu"></span>
        </button>
        <ul>
          <li>
            <span class="icon">
              <img src="images/right-hosts-icon.png" alt="">
            </span>
            <div class="info">
              <h4>10, 000 Hosts</h4>
            </div>
          </li>
          <li>
            <span class="icon">
              <img src="images/right-universities-icon.png" alt="">
            </span>
            <div class="info">
              <h4>Universities</h4>
              <h5>123456868</h5>
            </div>
          </li>
          <li>
            <span class="icon">
              <img src="images/right-a-instituties-icon.png" alt="">
            </span>
            <div class="info">
              <h4>Auto Instituties</h4>
              <h5>123456868</h5>
            </div>
          </li>
          <li>
            <span class="icon">
              <img src="images/right-school-icon.png" alt="">
            </span>
            <div class="info">
              <h4>School</h4>
              <h5>123456868586</h5>
            </div>
          </li>
          <li>
            <span class="icon">
              <img src="images/right-reaseachers-icon.png" alt="">
            </span>
            <div class="info">
              <h4>Reaseachers</h4>
              <h5>123456868586</h5>
            </div>
          </li>
          <li>
            <span class="icon">
              <img src="images/right-admin-user-icon.png" alt="">
            </span>
            <div class="info">
              <h4>Admin User</h4>
              <h5>123456868586</h5>
            </div>
          </li>

        </ul>
      </div>
      <!-- right sidebar end -->
    </div>
    <!-- page-body-wrapper ends -->
  </div>
  <!-- container-scroller -->

  <!-- plugins:js -->
  <script src="{{URL::to('/')}}/public/plugins/jQuery/jquery-2.2.3.min.js"></script>
<!-- Bootstrap 3.3.6 -->
<script src="{{URL::to('/')}}/public/bootstrap/js/bootstrap.min.js"></script>
  <script src="{{URL::to('/')}}/public/super_admin/js/vendor.bundle.base.js"></script>
  <script src="{{URL::to('/')}}/public/super_admin/js/bootstrap-datepicker.min.js"></script>
  <script src="{{URL::to('/')}}/public/super_admin/js/progressbar.min.js"></script>
  <script src="{{URL::to('/')}}/public/super_admin/js/Chart.min.js"></script>
  <!-- endinject -->
  
  <!-- End plugin js for this page -->
  <!-- inject:js -->
  <script src="{{URL::to('/')}}/public/super_admin/js/off-canvas.js"></script>
  <script src="{{URL::to('/')}}/public/super_admin/js/hoverable-collapse.js"></script>
  <script src="{{URL::to('/')}}/public/super_admin/js/template.js"></script>
  <script src="{{URL::to('/')}}/public/super_admin/js/settings.js"></script>
  <script src="{{URL::to('/')}}/public/super_admin/js/todolist.js"></script>
  <!-- endinject -->
  <!-- Custom js for this page-->
  <script src="{{URL::to('/')}}/public/super_admin/js/jquery.cookie.js" type="text/javascript"></script>
  <script src="{{URL::to('/')}}/public/super_admin/js/dashboard.js"></script>
  <script src="{{URL::to('/')}}/public/super_admin/js/Chart.roundedBarCharts.js"></script>
  <!-- End custom js for this page-->
  <script src="{{URL::to('/')}}/public/super_admin/js/external.js"></script>
  <script src="{{URL::to('/')}}/public/js/jquery.validate.js"></script>
  <script src="{{URL::to('/')}}/public/js/jquery.validate.min.js"></script>
  <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.js"></script>
  @yield('js')
</body>

</html>

