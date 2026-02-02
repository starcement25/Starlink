@extends('tour.layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Page Content</h1>
                </div>
                <div class="col-sm-6">
                    <a class="btn btn-primary float-right"
                    href="{{ route('tour.page.list.item.create', ['id'=> $pageId]) }}">
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
                        <th>Element Type</th>
                        <th>Order</th>
                        <th>Title</th>
                        <th>Active</th>
                        <th></th>
                    </thead>

                
                <tbody>
                    @if (!empty($contents))
                    @foreach ($contents as $content)
                        <tr>
                            <td>{{ \App\Utils\Helper::getElementType($content->element_type) }}</td>
                            <td>{{ $content->show_order }}</td>
                            <td>{{ $content->title }}</td>
                            <td> 
                                <span class="badge @if($content->is_active) badge-success @else badge-danger @endif">
                                @if($content->is_active) 
                                    Active
                                @else 
                                    Disable
                                @endif
                            </span>
                            </td>
                            <td>
                                <a href="{{ route('tour.page.list.item.edit', ['id'=> $content->id]) }}" class="btn btn-default btn-xs">
                                    <i class="far fa-edit"></i>
                                </a>
                               
                            </td>
                        </tr>
                    @endforeach
                  
                    @endif
                </tbody>
            </table>
             
            
            </div>

        </div>
       
    </div>

@endsection

