@extends('admin.layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            
        </div>
    </section>

    <div class="content px-3">
        @include('flash::message')
        <div class="clearfix"></div>
        
        <div class="card">
            <div class="card-header">
                <div class="row p-0">
                    <div class="col-sm-6">
                        <h5>Liftings History</h5> 
                    </div>
                    <div class="col-sm-6">
                        <a class="btn btn-primary float-right"
                           href="{{ route('liftings.create') }}">
                            Add New
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body table-responsive">
              {{--   @include('admin.lifting.table') --}}
            
                {!! $dataTable->table() !!}
              
              
            </div>

        </div>
    </div>
    {!! $dataTable->scripts() !!}
@endsection

