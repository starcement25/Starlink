@extends('admin.layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Mason Points</h1>
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
                <table class="table table-sm">
                    <thead>
                        <th>Name</th>
                        <th>Contact</th>
                        <th>Points</th>
                        <th>Mason Category</th>
                    </thead>
                    <tbody>
                        @if (!empty($users))
                              @foreach ($users as $user)
                                  <tr>
                                    <td>{{ $user->name ?? "" }}</td>
                                    <td>{{ $user->phone ?? "" }}</td>
                                    <td>{{ $user->points ?? ""  }}</td>
                                    <td>{{ $user->mason_category->name ?? ""  }}</td>
                                  </tr>
                              @endforeach  
                        @endif
                    </tbody>
                    
                </table>

             {{-- {!! $dataTable->table() !!} --}}

            </div>

        </div>
       {{--  {!! $dataTable->scripts() !!} --}}
    </div>

@endsection

