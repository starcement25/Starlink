@extends('admin.layouts.app')
@push('third_party_stylesheets')
    @include('admin.layouts.datatables_css')
@endpush
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Dashboard</h1>
                </div>
                <div class="col-sm-6">
                    {{-- <a class="btn btn-primary float-right ml-1"
                        href="{{ route('customer-stock.export') }}">
                        Export
                    </a> --}}
                </div>
            </div>
        </div>
    </section>

    <div class="content px-3">
        @include('flash::message')
        <div class="clearfix"></div>

        <div class="card">
            <div class="card-body table-responsive">
                {{-- <div class="row">
                    <div class="form-group col-sm-4">
                        fd
                    </div>
                </div> --}}
                <form action="{{ route('admin.employee.dashboard') }}" method="GET" id="filterForm">
                    
                    <div class="row">
                        <div class="form-group col-sm-2">
                            <label for="">Select Zone: </label>
                            <select name="zone" id="zone" class="form-control select2bs4">
                                <option value="">All Zones</option>
                                    @if ( !empty($zones))
                                        @foreach ($zones as $zone)
                                            <option value="{{ $zone->id }}" @if($zoneSelect == $zone->id) {{'selected'}} @endif>{{ $zone->name }}</option>
                                        @endforeach
                                    @endif
                            </select>
                           
                        </div>
                        <div class="form-group col-sm-2">
                            <label for="">Select Branch: </label>
                            <select name="branch" id="branch" class="form-control select2bs4">
                                <option value="">All Branches</option>
                                    @if ( !empty($branches))
                                        @foreach ($branches as $branch)
                                            <option value="{{ $branch->id }}" @if($branchSelect == $branch->id) {{'selected'}} @endif>{{ $branch->name }}</option>
                                        @endforeach
                                    @endif
                            </select>
                           
                        </div>
                        <div class="form-group col-sm-2">
                            <label for="">From Date: </label>
                            <input type="date" name="fromDate" id="fromDate" class="form-control" value = {{$fromDataVal}}>
                        </div>
                        <div class="form-group col-md-2">
                            <label for="">To Date: </label>
                            <input type="date" name="toDate" id="toDate" class="form-control" value="{{$toDataVal}}">
                        </div>
                        <div class="form-group col-md-2">
                            <label for="">&nbsp; </label>
                            <button type="submit" class="btn btn-success form-control">Search</button>
                        </div>
                       
                       
                    </div>
                    
                        
                   
                </form>
               {{--  @include('admin.product.table') --}}
               {!! $dataTable->table() !!}
            </div>

        </div>
        @push('third_party_scripts')
            @include('admin.layouts.datatables_js')
            {!! $dataTable->scripts() !!}
        @endpush
    </div>

@endsection

