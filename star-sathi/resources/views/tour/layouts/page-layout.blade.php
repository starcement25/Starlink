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
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <!-- for ratina ready mobile devices -->
  <title>Star_saathi</title>

  <!-- Fav Icon -->
  <link rel="icon" type="image/ico" href="{{asset('resources/tour/page/image/favicon.ico')}}">

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

  <link rel="stylesheet" href="{{asset('resources/tour/page/css/main.css')}}">
  <link rel="stylesheet" href="{{asset('resources/tour/page/css/responsive.css')}}">

  <!--[if lt IE 8]>
		<link rel="stylesheet" href="css/ie.css">
  	<![endif]-->

  <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>

<body>

  <section class="homePage">
    <div class="container-xxl">
      <div class="row flex-row-reverse">
        @yield('content')
       {{--  <div class="col-md-6 right">
          <h1>DEALER TOUR- DUBAI'23</h1>
          <h6>21st to 27th August'2023</h6>
          <img src="{{ asset('resource/tour/page/images/img-1.png') }}" alt="">
        </div>
        <div class="col-md-6">
          <form>
            <div class="row formBx">

              <div class="col-md-12 mb-3">
                <select class="form-select" aria-label="Default select example">
                  <option selected> Spl Instructions </option>
                  <option value="1">One</option>
                  <option value="2">Two</option>
                  <option value="3">Three</option>
                </select>

                <!-- <input type="text" class="form-control" id="" placeholder="Enter Code"> -->
              </div>

              <div class="col-md-12">
                <hr>
              </div>
              <div class="col-md-12 mb-3">
                <label for="exampleDataList" class="form-label">Dealer Code</label>
                <input class="form-control" list="datalistOptions" id="exampleDataList" placeholder="Dealer Code...">
                <datalist id="datalistOptions">
                  <option value="1239">
                  <option value="456">
                  <option value="789">
                  <option value="1011">
                  <option value="1213">
                </datalist>
                <!-- <input type="text" class="form-control" id="" placeholder=""> -->
              </div>

              <div class="col-md-12 mb-3">
                <input type="text" class="form-control" id="" placeholder="Branch">
              </div>

              <div class="col-md-12 mb-3">
                <input type="text" class="form-control" id="" placeholder="Dealer Name">
              </div>

              <div class="col-md-12 mb-3">
                <input type="text" class="form-control" id="" placeholder="Phn no ">
              </div>
              <div class="col-md-12">
                <hr>
              </div>

              <div class="col-md-12 mb-3">
                <label for="exampleDataList" class="form-label"> Type of business you are handling: </label>

                <div class="form-check col-md-12">
                  <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault1">
                  <label class="form-check-label" for="flexCheckDefault1">
                    Cement
                  </label>
                </div>

                <div class="form-check col-md-12">
                  <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault2">
                  <label class="form-check-label" for="flexCheckDefault2">
                    Steel
                  </label>
                </div>

                <div class="col-md-12 mb-1">
                  <label class="form-check-label" for="flexCheckDefault77">
                    Aggregates
                  </label>
                  <div class="row">
                    <div class="col-md-4">
                      <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault70">
                        <label class="form-check-label" for="flexCheckDefault70">
                          Sand
                        </label>
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault71">
                        <label class="form-check-label" for="flexCheckDefault71">
                          Stone schips
                        </label>
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault72">
                        <label class="form-check-label" for="flexCheckDefault72">
                          Bricks
                        </label>
                      </div>
                    </div>
                  </div>


                </div>



                <div class="form-check">
                  <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault3">
                  <label class="form-check-label" for="flexCheckDefault3">
                    Paint
                  </label>
                </div>

                <div class="form-check">
                  <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault4">

                  <label class="form-check-label" for="flexCheckDefault4">
                    Others
                  </label>
                  <input type="text" class="form-control" id="" placeholder="Pls specify">
                </div>
              </div>

              <div class="col-md-12">
                <hr>
              </div>



              <div class="col-md-12 mb-1">
                <label for="" class="form-label"> What is your Monthly average volume of the business
                  handling: </label>
              </div>

              <div class=" col-md-12 mb-3">
                <input type="text" class="form-control" id="" placeholder="Cement ……. MT">
              </div>

              <div class=" col-md-12 mb-3">
                <input type="text" class="form-control" id="" placeholder="Steel …… MT">
              </div>

              <div class=" col-md-12 mb-3">
                <input type="text" class="form-control" id=""
                  placeholder="Agreegates (Sand, Stone schips, Bricks) ……… CFT">
              </div>

              <div class=" col-md-12 mb-3">
                <input type="text" class="form-control" id="" placeholder="Paint …….. Litre">
              </div>

              <div class=" col-md-12 mb-3">
                <input type="text" class="form-control" id="" placeholder="Others (Pls Specify)">
              </div>

              <div class="col-md-12">
                <hr>
              </div>

              <div class="col-md-12 mb-1">
                <label for="" class="form-label">
                  What is the fast moving product amongst your business Provide Rank (1 to 5) </label>
              </div>


              <div class=" col-md-12 mb-3">
                <div>
                  <label for="" class="form-label"> Cement </label>
                </div>
                <div class="full-stars-example-two">

                  <div class="rating-group">
                    <input disabled checked class="rating__input rating__input--none" name="rating3" id="rating3-none"
                      value="0" type="radio">
                    <label aria-label="1 star" class="rating__label" for="rating3-1"><i
                        class="rating__icon rating__icon--star fa fa-star"></i></label>

                    <input class="rating__input" name="rating3" id="rating3-1" value="1" type="radio">
                    <label aria-label="2 stars" class="rating__label" for="rating3-2"><i
                        class="rating__icon rating__icon--star fa fa-star"></i></label>

                    <input class="rating__input" name="rating3" id="rating3-2" value="2" type="radio">
                    <label aria-label="3 stars" class="rating__label" for="rating3-3"><i
                        class="rating__icon rating__icon--star fa fa-star"></i></label>

                    <input class="rating__input" name="rating3" id="rating3-3" value="3" type="radio">
                    <label aria-label="4 stars" class="rating__label" for="rating3-4"><i
                        class="rating__icon rating__icon--star fa fa-star"></i></label>

                    <input class="rating__input" name="rating3" id="rating3-4" value="4" type="radio">
                    <label aria-label="5 stars" class="rating__label" for="rating3-5"><i
                        class="rating__icon rating__icon--star fa fa-star"></i></label>

                    <input class="rating__input" name="rating3" id="rating3-5" value="5" type="radio">
                  </div>

                </div>
              </div>

              <div class=" col-md-12 mb-3">
                <div>
                  <label for="" class="form-label"> Steel </label>
                </div>

                <div class="full-stars-example-two">
                  <div class="rating-group">
                    <input disabled checked class="rating__input rating__input--none" name="rating4" id="rating4-none"
                      value="0" type="radio">
                    <label aria-label="1 star" class="rating__label" for="rating4-1"><i
                        class="rating__icon rating__icon--star fa fa-star"></i></label>
                    <input class="rating__input" name="rating4" id="rating4-1" value="1" type="radio">
                    <label aria-label="2 stars" class="rating__label" for="rating4-2"><i
                        class="rating__icon rating__icon--star fa fa-star"></i></label>
                    <input class="rating__input" name="rating4" id="rating4-2" value="2" type="radio">
                    <label aria-label="3 stars" class="rating__label" for="rating4-3"><i
                        class="rating__icon rating__icon--star fa fa-star"></i></label>
                    <input class="rating__input" name="rating4" id="rating4-3" value="3" type="radio">
                    <label aria-label="4 stars" class="rating__label" for="rating4-4"><i
                        class="rating__icon rating__icon--star fa fa-star"></i></label>
                    <input class="rating__input" name="rating4" id="rating4-4" value="4" type="radio">
                    <label aria-label="5 stars" class="rating__label" for="rating4-5"><i
                        class="rating__icon rating__icon--star fa fa-star"></i></label>
                    <input class="rating__input" name="rating4" id="rating4-5" value="5" type="radio">
                  </div>

                </div>
              </div>

              <div class=" col-md-12 mb-3">
                <div>
                  <label for="" class="form-label"> Aggregates (Sand, Stone schips, Bricks) </label>
                </div>
                <div class="full-stars-example-two">
                  <div class="rating-group">
                    <input disabled checked class="rating__input rating__input--none" name="rating5" id="rating5-none"
                      value="0" type="radio">
                    <label aria-label="1 star" class="rating__label" for="rating5-1"><i
                        class="rating__icon rating__icon--star fa fa-star"></i></label>
                    <input class="rating__input" name="rating5" id="rating5-1" value="1" type="radio">
                    <label aria-label="2 stars" class="rating__label" for="rating5-2"><i
                        class="rating__icon rating__icon--star fa fa-star"></i></label>
                    <input class="rating__input" name="rating5" id="rating5-2" value="2" type="radio">
                    <label aria-label="3 stars" class="rating__label" for="rating5-3"><i
                        class="rating__icon rating__icon--star fa fa-star"></i></label>
                    <input class="rating__input" name="rating5" id="rating5-3" value="3" type="radio">
                    <label aria-label="4 stars" class="rating__label" for="rating5-4"><i
                        class="rating__icon rating__icon--star fa fa-star"></i></label>
                    <input class="rating__input" name="rating5" id="rating5-4" value="4" type="radio">
                    <label aria-label="5 stars" class="rating__label" for="rating5-5"><i
                        class="rating__icon rating__icon--star fa fa-star"></i></label>
                    <input class="rating__input" name="rating5" id="rating5-5" value="5" type="radio">
                  </div>

                </div>
              </div>

              <div class=" col-md-12 mb-3">
                <div>
                  <label for="" class="form-label"> Paint </label>
                </div>

                <div class="full-stars-example-two">
                  <div class="rating-group">
                    <input disabled checked class="rating__input rating__input--none" name="rating6" id="rating6-none"
                      value="0" type="radio">
                    <label aria-label="1 star" class="rating__label" for="rating6-1"><i
                        class="rating__icon rating__icon--star fa fa-star"></i></label>
                    <input class="rating__input" name="rating6" id="rating6-1" value="1" type="radio">
                    <label aria-label="2 stars" class="rating__label" for="rating6-2"><i
                        class="rating__icon rating__icon--star fa fa-star"></i></label>
                    <input class="rating__input" name="rating6" id="rating6-2" value="2" type="radio">
                    <label aria-label="3 stars" class="rating__label" for="rating6-3"><i
                        class="rating__icon rating__icon--star fa fa-star"></i></label>
                    <input class="rating__input" name="rating6" id="rating6-3" value="3" type="radio">
                    <label aria-label="4 stars" class="rating__label" for="rating6-4"><i
                        class="rating__icon rating__icon--star fa fa-star"></i></label>
                    <input class="rating__input" name="rating6" id="rating6-4" value="4" type="radio">
                    <label aria-label="5 stars" class="rating__label" for="rating6-5"><i
                        class="rating__icon rating__icon--star fa fa-star"></i></label>
                    <input class="rating__input" name="rating6" id="rating6-5" value="5" type="radio">
                  </div>

                </div>

              </div>

              <div class=" col-md-12 mb-3">
                <div>
                  <label for="" class="form-label"> Others </label>
                  <div class="full-stars-example-two">
                    <div class="rating-group">
                      <input disabled checked class="rating__input rating__input--none" name="rating7" id="rating7-none"
                        value="0" type="radio">
                      <label aria-label="1 star" class="rating__label" for="rating7-1"><i
                          class="rating__icon rating__icon--star fa fa-star"></i></label>
                      <input class="rating__input" name="rating7" id="rating7-1" value="1" type="radio">
                      <label aria-label="2 stars" class="rating__label" for="rating7-2"><i
                          class="rating__icon rating__icon--star fa fa-star"></i></label>
                      <input class="rating__input" name="rating7" id="rating7-2" value="2" type="radio">
                      <label aria-label="3 stars" class="rating__label" for="rating7-3"><i
                          class="rating__icon rating__icon--star fa fa-star"></i></label>
                      <input class="rating__input" name="rating7" id="rating7-3" value="3" type="radio">
                      <label aria-label="4 stars" class="rating__label" for="rating7-4"><i
                          class="rating__icon rating__icon--star fa fa-star"></i></label>
                      <input class="rating__input" name="rating7" id="rating7-4" value="4" type="radio">
                      <label aria-label="5 stars" class="rating__label" for="rating7-5"><i
                          class="rating__icon rating__icon--star fa fa-star"></i></label>
                      <input class="rating__input" name="rating7" id="rating7-5" value="5" type="radio">
                    </div>

                  </div>
                </div>

              </div>

              <div class="col-md-12">
                <hr>
              </div>

              <div class="col-md-12 mb-1">
                <label for="" class="form-label">
                  What is the payment cycle for your business to the companies you are dealing with: ( No Of days)
                </label>
              </div>


              <div class=" col-md-6 mb-3">
                <label for="" class="form-label"> Cement </label>
                <input type="text" class="form-control" id="" placeholder="">
              </div>

              <div class=" col-md-6 mb-3">
                <label for="" class="form-label"> Steel </label>
                <input type="text" class="form-control" id="" placeholder="">
              </div>

              <div class=" col-md-6 mb-3">
                <label for="" class="form-label"> Aggregates </label>
                <label for="" class="form-label"> Sand, Stone schips, Bricks </label>
                <input type="text" class="form-control" id="" placeholder="">
              </div>

              <div class=" col-md-6 mb-3">
                <label for="" class="form-label"> Paint </label>
                <input type="text" class="form-control" id="" placeholder="">
              </div>

              <div class=" col-md-12 mb-3">
                <label for="" class="form-label"> Others </label>
                <input type="text" class="form-control" id="" placeholder="">
              </div>

              <div class="col-md-12">
                <hr>
              </div>

              <div class="col-md-12 mb-3">
                <div class="col-md-12"><label for="exampleDataList" class="form-label"> What is the source of funds for
                    your business: </label></div>

                <div class="form-check form-check-inline ">
                  <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault1">
                  <label class="form-check-label" for="flexCheckDefault1">
                    Bank
                  </label>
                </div>

                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault2">
                  <label class="form-check-label" for="flexCheckDefault2">
                    NBFC
                  </label>
                </div>


                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault3">
                  <label class="form-check-label" for="flexCheckDefault3">
                    Self
                  </label>
                </div>

                <div class="form-check">
                  <input class="form-check-input" type="checkbox" value="" id="">

                  <label class="form-check-label mb-2" for="">
                    Other sources
                  </label>
                  <input type="text" class="form-control" id="" placeholder="Other sources">
                </div>
              </div>

              <div class="col-md-12 mb-3">
                <label for="exampleDataList" class="form-label"> Do you get Over Draft facility from any banks </label>

                <div class="form-check col-md-12">
                  <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault1">
                  <label class="form-check-label" for="flexCheckDefault1">
                    Yes
                  </label>
                </div>

                <div class="form-check col-md-12">
                  <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault2">
                  <label class="form-check-label" for="flexCheckDefault2">
                    No
                  </label>
                </div>


              </div>

              <div class="col-md-12 mb-3">
                <button type="submit" class="btn submitBtn">Submit</button>
              </div>

            </div>
          </form>

        </div>
 --}}
      </div>
    </div>
  </section>




  <!-- Javascript -->
  <script src="{{ asset('resources/tour/page/js/jquery-2.1.3.min.js') }}"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
    crossorigin="anonymous"></script>

  <script src="{{ asset('resources/tour/page/js/main.js') }}"></script>
  @stack('page-template')

</body>

</html>