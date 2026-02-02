@extends('admin.layouts.app')
@push('third_party_stylesheets')
    @include('admin.layouts.datatables_css')
@endpush
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Branches</h1>
                </div>
                <div class="col-sm-6">
                    <a class="btn btn-primary float-right ml-1"
                       href="{{ route('branch.upload.show') }}">
                       Import
                    </a>
                   <span>&nbsp;</span>
                    <a class="btn btn-primary float-right"
                       href="{{ route('branch.create') }}">
                        Add New
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
               {{--  @include('admin.branch.table') --}}
               {!! $dataTable->table() !!}
               
            </div>

        </div>
        @push('third_party_scripts')
            @include('admin.layouts.datatables_js')
            {!! $dataTable->scripts() !!}
        @endpush
    </div>

@endsection

