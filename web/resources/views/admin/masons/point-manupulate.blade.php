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
                    <h5>Point Manupulation</h5>
                </div>
                <div class="row">
                    <table class="table table-borderless">
                    <form action="{{ route('point.save') }}" method="post">
                        @csrf
                        <input type="hidden" name="user" id="user" value="{{ $userId ?? "" }}">
                        <tr>
                            <td>Contractor Name</td>
                            <td>
                                {{ $user->name ?? ""}}
                            </td>
                        </tr>
                        <tr>
                            <td>Engineer Details</td>
                            <td>
                                Name: {{ $user->by_created->name ?? "" }}<br>
                                Name: {{ $user->by_created->emp_code ?? "" }}<br>
                                Email: {{ $user->by_created->email ?? "" }}<br>
                                Phone: {{ $user->by_created->phone ?? "" }}<br>
                            </td>
                        </tr>
                        <tr>
                            <td>Action Type</td>
                            <td>
                                <select name="type" id="type" class="form-control">
                                    <option value="">Select Action</option>
                                    <option value="1" @if( (old('type') ?? 0) == 1 ) selected @endif>Point Add</option>
                                    <option value="2" @if( (old('type') ?? 0) == 2 ) selected @endif>Point Deduct</option>
                                </select>
                                @error('type')
                                    <span class="text text-danger">{{ $message }}</span>
                                @enderror
                            </td>
                        </tr>
                        @if($user->status == 1)
                            <tr id="user_disable_field" style="{{ (old('type') ?? 0) == 2 ? 'visibility: visible' : 'visibility: hidden' }}">
                                <td>Do you want to disable user ?</td>
                                <td>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="user_disable" id="user_disable_id" value="1" @if( (old('user_disable') ?? 0) == 1 ) checked @endif>
                                        <label class="form-check-label" for="inlineRadio1">Yes</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="user_disable" id="user_disable_id" value="0" @if( (old('user_disable') ?? 0) == 0 ) checked @endif>
                                        <label class="form-check-label" for="inlineRadio2">No</label>
                                    </div>
                                    @error('user_disable')
                                        <span class="text text-danger">{{ $message }}</span>
                                    @enderror
                                </td>
                            </tr>
                        @endif
                        <tr>
                            <td>Point</td>
                            <td>
                                 <input type="number" name="point" id="point" class="form-control" value="{{old('point') ?? ""}}">
                                 @error('point')
                                    <span class="text text-danger">{{ $message }}</span>
                                 @enderror
                             </td>
                        </tr>
                        <tr>
                            <td>Description</td>
                            <td>
                                 <input type="text" name="description"  class="form-control" value="{{old('description') ?? ""}}">
                                 @error('description')
                                    <span class="text text-danger">{{ $message }}</span>
                                 @enderror
                             </td>
                        </tr>
                        <tr>
                            <td>Remarks</td>
                            <td>
                                 <input type="text" name="remarks"  class="form-control" value="{{old('remarks') ?? ""}}">
                                 @error('remarks')
                                    <span class="text text-danger">{{ $message }}</span>
                                 @enderror
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
               
                <a href="{{ route('point.list') }}" class="btn btn-default">Cancel</a>
            </div>

          

        </div>
    </div>
@endsection
@push('mason-create-blade')
    <script>
        $(document).ready(function(){
            $('#marital_status').on('change', function(){
                if($(this).val() == 1){
                    // Role Mason
                    $('#spouseField').css({"display":"block"}) ;
                    $('#spouseDobField').css({"display":"block"}) ;
                } else{
                    $('#spouseField').css({"display":"none"}) ;
                    $('#spouseDobField').css({"display":"none"}) ;

                }
               
            });
            $('#type').on('change', function(){
                if($(this).val() == 2){
                    // Role Mason
                    $('#user_disable_field').css({"visibility":"visible"}) ;
                } else{
                    $('#user_disable_field').css({"visibility":"hidden"}) ;

                }
               
            });
        });
    </script>
@endpush
