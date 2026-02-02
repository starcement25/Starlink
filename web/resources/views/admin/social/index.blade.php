@extends('admin.layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                   {{--  <h1>Pages</h1> --}}
                </div>
                <div class="col-sm-6">
                   {{--  <a class="btn btn-primary float-right"
                       href="{{ route('pages.create') }}">
                        Add New
                    </a> --}}
                </div>
            </div>
        </div>
    </section>

    <div class="content px-3">
        @include('flash::message')
        <div class="clearfix"></div>

        <div class="card">
            <div class="card-header">
                <h5>Links</h5>
            </div>
            <div class="card-body table-responsive">
                @include('admin.social.table')
             {{--   {!! $dataTable->table() !!} --}}
            </div>

        </div>
      {{--   {!! $dataTable->scripts() !!} --}}
    </div>

@endsection

