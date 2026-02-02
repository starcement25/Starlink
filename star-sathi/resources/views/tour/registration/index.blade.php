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
                <div class="table-responsive">
                    <table class="table" id="registration-table">
                        <thead>
                            @if (!empty($headers))
                                @foreach ($headers as $header)
                                    <th>{{ ucfirst($header) }}</th>
                                    
                                @endforeach
                                
                            @endif
                        </thead>
                        <tbody>
                            @if (!empty($values))
                                @foreach ($values as $items)
                                <tr>
                                        @foreach ($items as $item)
                                            <td>{{ $item }}</td>
                                        @endforeach
                                </tr>
                                @endforeach
                            
                            @endif
                       
                        </tbody>
                    </table>
                </div>
                
            
            </div>

        </div>
    </div>

@endsection

