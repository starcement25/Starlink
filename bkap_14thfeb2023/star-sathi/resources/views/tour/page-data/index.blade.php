@extends('tour.layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Page Data</h1>
                </div>
                <div class="col-sm-6">
                   
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

