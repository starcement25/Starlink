@extends('tour.layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Pages</h1>
                </div>
                <div class="col-sm-6">
                    <a class="btn btn-primary float-right"
                       href="{{ route('tour.pages.create') }}">
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
                <table class="table table-sm">
                    <thead>
                        <th>Id</th>
                        <th>Page Name</th>
                        <th>Url</th>
                        <th></th>
                    </thead>
                    <tbody>
                        @if(!empty($pages))
    
                            @foreach ($pages as $page)
                                <tr>
                                    <td>{{ $page->id }}</td>
                                    <td>{{ $page->name }}</td>
                                    <td>{{ url('tour/web/pages/'.$page->slug) }}</td>
                                    <td>
                                        <a href="{{ route('tour.pages.edit', ['page'=> $page->id]) }}" class="btn btn-default btn-xs">
                                            <i class="far fa-edit"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
              
               
                {{--   {!! $dataTable->table() !!} --}}
               
            </div>

        </div>
       {{--  {!! $dataTable->scripts() !!} --}}
    </div>

@endsection

