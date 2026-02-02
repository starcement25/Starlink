@extends('admin.layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-12">
                 
                </div>
            </div>
        </div>
    </section>

    <div class="content px-3">

        <div class="card">

           

            <div class="card-body">
                @include('flash::message')
                <div class="card-header">
                    <h5>Edit Settings</h5>
                </div>
                <div class="row">
                    <table class="table table-borderless">
                        <form action="{{ route('settings.update',['setting' =>$setting->id]) }}" method="post" enctype="multipart/form-data">
                            @csrf
                            @method('PATCH')
                            {{-- <input type="hidden" name="user" id="user" value="{{ $setting->id ?? "" }}"> --}}
                            <tr>
                                <td>{{strtoupper($setting->setting_name)}} @if($setting->input_type == "image"){{"(414 X 896)"}}@endif</td>
                                <td>
                                    @if($setting->input_type == "number")
                                        <input type="number" name="setting_value" value="{{$setting->setting_value}}" class="form-control" step="0.1">
                                        @error('setting_value')
                                            <span class="text text-danger">{{ $message }}</span>
                                        @enderror
                                    @endif
                                    @if($setting->input_type == "text")
                                        <input type="text" name="setting_value" value="{{$setting->setting_value}}" class="form-control">
                                        @error('setting_value')
                                            <span class="text text-danger">{{ $message }}</span>
                                        @enderror
                                    @endif
                                    @if($setting->input_type == "email")
                                        <input type="email" name="setting_value" value="{{$setting->setting_value}}" class="form-control">
                                        @error('setting_value')
                                            <span class="text text-danger">{{ $message }}</span>
                                        @enderror
                                    @endif
                                    @if($setting->input_type == "image")
                                    <input class="form-control" name="setting_value" type="file" accept="image/*">
                                        @error('setting_value')
                                            <span class="text text-danger">{{ $message }}</span>
                                        @enderror
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td></td>
                                <td>
                                    <button type="submit" class="btn btn-success">Save</button>
                                </td>
                            </tr>
                        </form>
                    </table>
                </div>

            </div>

            <div class="card-footer">
               
                <a href="{{ route('settings.index') }}" class="btn btn-default">Cancel</a>
            </div>

          

        </div>
    </div>
@endsection
