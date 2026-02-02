@extends('star-sathi-dealer.layouts.app')
@push('title')
    <title>Star Link | Dealer</title>
@endpush
@push('third_party_styles')
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.4/css/select2.min.css'><link rel="stylesheet">
@endpush
@section('content') 
    <div class="row my-5 justify-content-md-center">
        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
            <div class="popBx p-3 shadow">
            <h4>Accept Pending Lifting</h4>
            </div>
        </div>
    </div>
    <div class="content px-3 pb-3">
        <a href="{{route('dealer.pending.liftings')}}">
            <button type="button" class="btn btn-primary">
                <i class="fa fa-arrow-left" aria-hidden="true"></i>
                Back
            </button>
        </a>
    </div>
    <div class="content px-3">
        @include('flash::message')
        @error('qty')
            <div class="alert alert-danger" role="alert">{{$message}}</div>
        @enderror

        <div class="card">
            <div class="card-body table-responsive">
                <form action="{{route('dealer.lifting.accept', ['lifting_id' => encrypt($lifting->id)])}}" method="post" id="filterForm">
                    @csrf
                    <div class="row pb-4">
                        <div class="form-group col-sm-2">
                            <label for="">Product: </label>
                            <input type="text" name="product_id" class="form-control" value = "{{$lifting->product->name}}" readonly="true">
                        </div>
                        <div class="form-group col-md-2">
                            <label for="">Lifting Date: </label>
                            <input type="text" name="lifting_date" class="form-control" value= "{{$lifting->lifting_date}}" readonly="true">
                        </div>
                        <div class="form-group col-md-2">
                            <label for="">Mason Name: </label>
                            <input type="text" name="mason_name" class="form-control" value= "{{$lifting->reward[0]->mason->name}}" readonly="true">
                        </div>
                        <div class="form-group col-md-2">
                            <label for="">Mason Phone: </label>
                            <input type="text" name="mason_phone" class="form-control" value= "{{$lifting->reward[0]->mason->phone}}" readonly="true">
                        </div>
                        <div class="form-group col-md-2">
                            <label for="">No of Bags: </label>
                            <input type="number" name="qty" class="form-control" value= "{{$lifting->qty}}" readonly="true">
                        </div>
                        <div class="form-group col-md-2">
                            <label for="">&nbsp; </label>
                            <button type="submit" class="btn btn-success form-control">Submit</button>
                        </div>
                    </div>
                </form>
            </div>

        </div>
    </div>
@endsection

@push('third_party_scripts')
    <script src='https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.4/js/select2.min.js' defer></script>
@endpush