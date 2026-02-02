@extends('admin.layouts.app')
@push('third_party_stylesheets')
    @include('admin.layouts.datatables_css')
@endpush
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Support Master</h1>
                </div>
                
            </div>
        </div>
    </section>

    <div class="content px-3">
        @include('flash::message')
        <div class="clearfix"></div>

        <div class="card">
            <div class="card-body table-responsive">
           
             {!! $dataTable->table() !!}

            </div>

        </div>
        @push('third_party_scripts')
            @include('admin.layouts.datatables_js')
            {!! $dataTable->scripts() !!}
        @endpush
    </div>

@endsection

