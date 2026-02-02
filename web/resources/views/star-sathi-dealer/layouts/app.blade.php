<!doctype html>
<!--[if lt IE 7]><html class="ie6" lang="en"><![endif]-->
<!--[if IE 7]><html class="ie7" lang="en"><![endif]-->
<!--[if IE 8]><html class="ie8" lang="en"><![endif]-->
<!--[if gt IE 8]><!-->
<!--<![endif]-->


<html lang="en">

<head>
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, , maximum-scale=1, user-scalable=no">
  <!-- for ratina ready mobile devices -->
  @stack('title')

  <!-- Fav Icon -->
  {{-- <link rel="icon" type="image/ico" href="favicon.ico"> --}}

  <!-- Fonts -->
  <link rel="stylesheet"
    href="https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.1.0/css/line-awesome.min.css">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/css/all.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" rel="stylesheet">

  <!--Style-->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
  <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

  <link rel="stylesheet" href=" https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">

  <link rel="stylesheet" href="{{ asset('resources/dealer/css/main.css') }}">
  <link rel="stylesheet" href="{{ asset('resources/dealer/css/responsive.css') }}">

  <!--[if lt IE 8]>
		<link rel="stylesheet" href="css/ie.css">
  	<![endif]-->

  <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    @stack('third_party_styles')
</head>

<body>
  <div class="bodyWrap">

    <section class="Itineraries">
      <div class="container-xl">
        {{-- Notification --}}

          <div class = "icons">

              <div class = "notification">

                  <div class="accordion" id="accordionExample">
                      <div class="accordion-item">
                        <h2 class="accordion-header" id="headingOne">
                          <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                              <i class="fas fa-bell" id="notification_bell"></i>
                              <div class="number" id="unread-msg-count" @if(\App\Utils\Helper::getUserUnreadMsgCount() == 0) hidden @endif>{{\App\Utils\Helper::getUserUnreadMsgCount()}}</div>
                          </button>
                        </h2>
                        <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                          <div class="accordion-body">

                                  <ul id="notifications-msg">

                                  </ul>

                                  <div id="see-more" hidden><button class="dropdown-item" id="see-more-btn">See More..</button></div>

                          </div>
                        </div>
                      </div>




                    </div>


              </div>
          </div>



        {{-- Notification --}}
          <div class="row my-5 ">
            <!-- <h2 class="text-center">Star Link</h2> -->
            <img src="{{asset('logo/star_cement_logo.png')}}" alt="" style="width: 250px; margin: 0 auto;">
          </div>

      <hr>
      </hr>

      @yield('content')

      </div>
    </section>



  </div>


  <!-- Feedback -->


  <!-- Javascript -->
  <!-- <script src="{{ asset('resources/dealer/js/jquery-2.1.3.min.js')}}"></script> -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
    crossorigin="anonymous"></script>

  <script src="{{ asset('resources/dealer/js/main.js') }}"></script>
  @stack('third_party_scripts')
  <script>
    $(document).ready(function(){
        let universalPageCount = 1;
        let getNotificationCount = 0;
        function getNotifications(pageCount)
        {
            let url = '{{route("dealer.notification", ["page" => "pagecont"])}}';
            url = url.replace("pagecont", pageCount);
            // alert(url);
            $.ajax({
                url: url,
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                // data: mailData,
                dataType: "json",
                type: "GET",
                cache: false,
                processData: false,
                contentType: false,
                async: true,
                // enctype: "multipart/form-data",
                success: function(response) {
                    if(response.status){
                        getNotificationCount++;
                        messages = $("#notifications-msg").html();
                        if(response.data.notifications.length > 0)
                        {
                            // for(msg of response.data.notifications)
                            // {
                            //     messages += '<li><span class="dropdown-item">'+msg+'</span></li>';
                            // }
                            response.data.notifications.forEach( (val) => {
                                messages += '<li><span class="dropdown-item">'+val.msg+'</span></li>';
                            });
                            $("#notifications-msg").html(messages);
                            if(response.data.see_more)
                            {
                                $("#see-more").attr("hidden", false);
                            }
                            else
                            {
                                $("#see-more").attr("hidden", true);
                            }
                            if(response.data.unread_msg_count > 0)
                            {
                                $("#unread-msg-count").show();
                                $("#unread-msg-count").text(response.data.unread_msg_count);
                            }
                            else
                            {
                                $("#unread-msg-count").hide();
                            }
                            universalPageCount = Number(response.data.current_page) + 1;
                        }
                        else{
                            $("#notifications-msg").html('<li><span class="dropdown-item">Looks Like your all caught up!</span></li>');
                        }
                    }else{
                        if(getNotificationCount === 0)
                        {
                            messages = '<button><span class="dropdown-item">'+response.msg+'</span></button>';
                            $("#notifications-msg").html(messages);
                        }
                    }


                // console.log(response);
                }
            });
        }
        $("#notification_bell").click(() => {
            getNotifications(universalPageCount);
        });
        $("#see-more-btn").click(() => {
            getNotifications(universalPageCount);
        });

    });
</script>


</body>

</html>