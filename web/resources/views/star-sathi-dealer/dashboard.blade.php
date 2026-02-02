@extends('star-sathi-dealer.layouts.app')
@push('title')
    <title>Star Link | Dealer</title>
@endpush
@section('content')    
      <div class="row my-5 justify-content-md-center">
        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
          <a href="{{route('dealer.pending.liftings')}}">
            <div class="popBx p-3 shadow">
            <h4>Pending Liftings</h4>
            </div>
          </a>
        </div>
      </div>
      <div class="row my-5 justify-content-md-center">
        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
          <a href="{{route('dealer.accept.liftings')}}">
            <div class="popBx p-3 shadow">
              <h4>Approved Liftings</h4>
            </div>
          </a>
        </div>
      </div>
      <div class="row my-5 justify-content-md-center">
        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
          <a href="{{route('dealer.reject.liftings')}}">
            <div class="popBx p-3 shadow">
              <h4>Rejected Liftings</h4>
            </div>
          </a>
        </div>
      </div>



        <!-- <div class="row">
          <div class="col">

            <div class=" p-3 ">
             
              <h4> Feedback</h4>
            </div>

            <form>
              <textarea class="form-control" id="exampleFormControlTextarea1" rows="6"></textarea>
              <button type="submit" class="btn btnPopup mt-2">Submit</button>
            </form>

          </div>
        </div> -->
@endsection


      