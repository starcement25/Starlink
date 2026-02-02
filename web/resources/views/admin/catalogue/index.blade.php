@extends('admin.layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Catalogues</h1>
                </div>
                <div class="col-sm-6">
                    {{-- <a class="btn btn-primary float-right ml-1"
                        href="{{ route('catalogue.images.show') }}">
                        Upload Images
                    </a> --}}
                    <span>&nbsp;</span>
                    <a class="btn btn-primary float-right ml-1"
                        href="{{ route('catalogue.upload.show') }}">
                        Import
                    </a> 
                    <a class="btn btn-primary float-right ml-1"
                       href="{{ route('catalogue.bulk-update.status.show') }}">
                        Bulk Update
                    </a>
                    <a class="btn btn-primary float-right ml-1"
                       href="{{ route('catalogue.export') }}">
                        Export
                    </a>
                    <a class="btn btn-primary float-right"
                       href="{{ route('catalogues.create') }}">
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
               {{--  @include('admin.product.table') --}}
               {!! $dataTable->table() !!}
            </div>

        </div>
        {!! $dataTable->scripts() !!}
    </div>

@endsection

