@extends('admin.layouts.app')
@push('third_party_stylesheets')
    @include('admin.layouts.datatables_css')
@endpush
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Mason Dashboard</h1>
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
               {{--  @include('admin.product.table') --}}
               {!! $dataTable->table() !!}
            </div>

        </div>
        @push('third_party_scripts')
            @include('admin.layouts.datatables_js')
            {!! $dataTable->scripts(attributes: ['nonce' => $cspNonce]) !!}
        @endpush
    </div>

@endsection

