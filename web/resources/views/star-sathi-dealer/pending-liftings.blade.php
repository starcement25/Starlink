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
            <h4>Pending Liftings</h4>
            </div>
        </div>
    </div>
    <div class="content px-3 pb-3">
        <a href="{{route('dealer.dashboard')}}">
            <button type="button" class="btn btn-primary">
                <i class="fa fa-arrow-left" aria-hidden="true"></i>
                Dashboard
            </button>
        </a>
    </div>
    <div class="content px-3">
        @include('flash::message')
        <div class="clearfix"></div>

        <div class="card">
            <div class="card-body table-responsive">
                <form action="" method="GET" id="filterForm">
                    <div class="row pb-4">
                        <div class="form-group col-sm-2">
                            <label for="">From Date: </label>
                            <input type="date" name="fromDate" id="fromDate" class="form-control" value = {{$fromDataVal}}>
                        </div>
                        <div class="form-group col-md-2">
                            <label for="">To Date: </label>
                            <input type="date" name="toDate" id="toDate" class="form-control" value="{{$toDataVal}}">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="">Mason name & Phone: </label>
                            <select id="searchByMason" class="form-select" name="mason">
                                <option value="">All Mason</option>
                                @foreach($masons as $mason)
                                        <option value="{{base64_encode($mason->user->phone ?? '')}}" <?php if($masonDataVal == $mason->user->phone ?? ''){ echo 'selected'; } ?> >{{$mason->user->name ?? ""}} - {{$mason->user->phone ?? ''}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-2">
                            <label for="">&nbsp; </label>
                            <button type="submit" class="btn btn-success form-control">Search</button>
                        </div>
                    </div>
                </form>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th scope="col">Products</th>
                                <th scope="col">No of Bags</th>
                                <th scope="col">Lifting Date</th>
                                <th scope="col">Mason Name</th>
                                <th scope="col">Mason Phone</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(count($pendingLists) > 0)
                                @foreach($pendingLists as $pendingList)
                                    <tr>
                                        <td>{{$pendingList['products']}}</td>
                                        <td>{{$pendingList['no_of_bags']}}</td>
                                        <td>{{$pendingList['lifting_date']}}</td>
                                        <td>{{$pendingList['mason_name']}}</td>
                                        <td>{{$pendingList['mason_phone']}}</td>
                                        <td>
                                            <div class="pending-btn">   
                                                <form method="GET" action="{{route('dealer.view.lifting.accept', ['lifting_id' => encrypt($pendingList['lifting_id'])])}}">
                                                    <button type="submit" class="btn btn-success"><i class="fa fa-check" aria-hidden="true"></i></button>
                                                </form>
                                                <form method="GET" action="{{route('dealer.view.lifting.reject', ['lifting_id' => encrypt($pendingList['lifting_id'])])}}">
                                                    <button type="submit" class="btn btn-danger"><i class="fa fa-times" aria-hidden="true"></i></button>
                                                </form>
                                                <a href="{{route('dealer.pending.liftings.edit', ['id' => encrypt($pendingList['lifting_id'])])}}">
                                                    <button type="submit" class="btn3"><i class="fas fa-edit"></i></button>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="6" class="text-center">No Pending Liftings Founds.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
            </div>

        </div>
    </div>
@endsection

@push('third_party_scripts')
    <script src='https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.4/js/select2.min.js' defer></script>
    <script>
        $(document).ready(function(){
            $("#searchByMason").select2();   
        });
    </script>
@endpush