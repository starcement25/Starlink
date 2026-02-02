@extends('admin.layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Settings</h1>
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
                        <th>Setting</th>
                        <th>Setting Value</th>
                        <th></th>
                    </thead>
                    <tbody>
                        @if (!empty($settings))
                              @foreach ($settings as $setting)
                                  <tr>
                                    <td>{{ strtoupper($setting->setting_name) ?? "" }}</td>
                                    <td>
                                        @if($setting->input_type == "image")
                                            @if(!empty($setting->setting_value))
                                                <img src="{{asset($setting->setting_value)}}" width="50" height="50">
                                            @else
                                                <img src="{{url("default.jpg")}}" width="50" height="50">
                                            @endif
                                        @else
                                            {{ $setting->setting_value ?? "" }}
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{route('settings.edit',['setting'=>$setting->id])}}" class="btn btn-default btn-xs">
                                            <i class="far fa-edit"></i>
                                        </a>
                                    </td>
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

