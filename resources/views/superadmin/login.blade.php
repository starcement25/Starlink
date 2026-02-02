<!DOCTYPE html>
<html lang="en">
<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Ipark</title>
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
  <!-- link css end -->
  <style>
    .tt-hint{
      color:black;
    }
    label.error{
      color:red;
    }
  </style>
</head>
<body>
  <section class="login-wrapper">
    <div class="container">
      <div class="row">
        <div class="col-12 col-md-8 col-lg-6 offset-md-2 offset-lg-3">
          <div class="login-form-wrap">
            <div class="logo">
              <img class="img-fluid" src="{{URL::to('/')}}/public/super_admin/images/dashboard-logo.png" alt="">
            </div>
            <form id="loginform" method="post" action="{{route('submitLogin')}}">
            @csrf
              <div class="form-group">
                <label>Email</label>
                <input type="email" class="form-control" id="" placeholder="|" name="username">
              </div>
              <div class="form-group">
                <label>Password</label>
                <input type="password" class="form-control" id="" placeholder="|" name="password">
                <span toggle="#password-field" class="fa fa-fw fa-eye field-icon toggle-password"></span>
              </div>
              <div class="row mb-3">
                <div class="col-12 col-md-6">
                  <div class="form-check">
                      <input type="checkbox" class="form-check-input" id="Remember_Me">
                      <label class="form-check-label" for="Remember_Me">Remember Me</label>
                  </div>
                </div>
                <div class="col-12 col-md-6">
                  <div class="login-forgot-pass">
                    <a class="forgot-password-btn" href="{{route('forgot-password')}}">Forgot Password?</a>
                  </div>
                </div>
              </div>
              <div class="form-group">
                  <input class="btn btn-primary" type="submit" name="" value="Sign In">
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- plugins:js -->
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
  
  <script>
    $.validator.setDefaults({
      submitHandler: function() {
        //alert("submitted!");
        var form = document.getElementById("loginform");
        form.submit();
      }
    });

    $().ready(function() {
      $("#loginform").validate({
        rules: {
          username: "required",
          password: "required",
          username: {
            required: true,
            email: true
          },
        },
        messages: {
          username: "Please enter your name",				
          username: "Please enter a valid email address"
        }
      });
	});
  </script>
</body>

</html>

