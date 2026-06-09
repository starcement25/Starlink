@extends('admin.layouts.app')
@push('third_party_stylesheets')
    @include('admin.layouts.datatables_css')
@endpush
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Contractors</h1>
                </div>
                {{-- <div class="col-sm-6">
                    <a class="btn btn-primary float-right"
                       href="{{ route('masons.create') }}">
                        Add New
                    </a>
                </div> --}}
                <div class="col-sm-6">
                    <a class="btn btn-primary float-right ml-1"
                       href="{{ route('mason.upload.show') }}">
                        Import
                    </a>
                    <a class="btn btn-primary float-right ml-1"
                       href="{{ route('mason.export', ['fromDate' => $fromDataVal ?? null, 'toDate' => $toDataVal ?? null, 'status' => $statusFilter ?? null, 'filter_by' => $filterBy ?? null]) }}">
                        Export
                    </a>
                    <a class="btn btn-primary float-right ml-1"
                       href="{{ route('mason.aadhaar.show') }}">
                        Import Aadhaar Docs
                    </a>
                </div>
            </div>
        </div>
    </section>

    <div class="content px-3">
        @include('flash::message')
        <div class="clearfix"></div>

        <div class="card">
            <div class="card-body table-responsive">
                <form action="{{ route('masons.index') }}" method="GET" id="filterForm">
                    <div class="row">
                        {{-- <div class="form-group col-sm-2">
                            <label for="">Select Branch: </label>
                            <select name="branch" id="branch" class="form-control select2bs4">
                                <option value="">All Branches</option>
                                    @if ( !empty($branches))
                                        @foreach ($branches as $branch)
                                            <option value="{{ $branch->id }}" @if($branchSelect == $branch->id) {{'selected'}} @endif>{{ $branch->name }}</option>
                                        @endforeach
                                    @endif
                            </select>
                           
                        </div> --}}
                        <div class="form-group col-sm-3">
                            <label for="">Filter by: </label>
                            <select name="filter_by" id="id_filter_by" class="form-control select2bs4">
                                <option value="">Select</option>
                                <option value="1" @if(($filterBy ?? null) == 1) selected @endif>Contractor Creation Date</option>
                                <option value="2" @if(($filterBy ?? null) == 2) selected @endif>Contractor Disable Date </option>
                            </select>
                           
                        </div>
                        <div class="form-group col-sm-2">
                            <label for="">From Date: </label>
                            <input type="date" name="fromDate" id="fromDate" class="form-control" value = {{$fromDataVal ?? ''}}>
                        </div>
                        @error('fromDate')
                            <span class="text text-danger">{{ $message }}</span>
                        @enderror
                        <div class="form-group col-md-2">
                            <label for="">To Date: </label>
                            <input type="date" name="toDate" id="toDate" class="form-control" value="{{$toDataVal ?? ''}}">
                        </div>
                        @error('toDate')
                            <span class="text text-danger">{{ $message }}</span>
                        @enderror
                        <div class="form-group col-sm-2" id="status_filter" style="{{$filterBy != null && $filterBy == 2 ? 'display:none' : 'display:block'}}">
                            <label for="">Status: </label>
                            <select name="status" id="id_status" class="form-control select2bs4">
                                <option value="">Select Status</option>
                                <option value="1" @if(($statusFilter ?? -1) == 1) selected @endif>Active</option>
                                <option value="0" @if(($statusFilter ?? -1) == 0) selected @endif>Disable </option>
                            </select>
                           
                        </div>
                        <div class="form-group col-md-2">
                            <label for="">&nbsp; </label>
                            <button type="submit" class="btn btn-success form-control">Search</button>
                        </div>
                       
                       
                    </div>
                    
                        
                   
                </form>
             {{--    @include('admin.user.table') --}}
             {!! $dataTable->table() !!}

            </div>

        </div>
        @push('third_party_scripts')
            @include('admin.layouts.datatables_js')
            {!! $dataTable->scripts(attributes: ['nonce' => $cspNonce]) !!}
        @endpush
    </div>

@endsection

@push('mason-create-blade')
    <script nonce="{{ $cspNonce }}">
        $(document).ready(function(){
            $('#id_filter_by').on('change', function(){
                if($(this).val() == 2){
                    // alert("mason creation");
                    $('#status_filter').hide()
                } else{
                    $('#status_filter').show()
                }
               
            });
        });
    </script>
@endpush


