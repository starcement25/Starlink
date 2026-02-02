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
                <div class="card-header p-1">
                    <h5>Support Edit</h5>
                </div>
                <div class="row">
                    <table class="table table-borderless">
                    <form action="{{ route('supports.update', ['support' => $support->id ]) }}" method="POST">
                        @csrf
                        <input type="hidden" name="_method" id="_method" value="PATCH">
                        <tr>
                            <td>Mason Name</td>
                            <td>
                                {{ $support->order->user->name ?? ""}}
                            </td>
                        </tr>
                        <tr>
                            <td>Gift Name</td>
                            <td>
                                {{ $support->order->catalogue->name ?? "" }}<br>
                            </td>
                        </tr>
                        <tr>
                            <td>Support Type</td>
                            <td> 
                                {{ $support->support_name }}
                                
                            </td>
                        </tr>
                        <tr>
                            <td>Comment</td>
                            <td>
                                {{ $support->comment }}
                            </td>
                        </tr>
                        <tr>
                            <td>Status</td>
                            <td>
                                <select name="status" id="status" class="form-control">
                                    <option value="">Select Status</option>
                                    <option value="1" @if (isset($support->status) && $support->status == "1") {{ "selected" }} @endif >Pending</option>
                                    <option value="2" @if (isset($support->status) && $support->status == "2") {{ "selected" }} @endif>Resolved</option>
                                    <option value="3" @if (isset($support->status) && $support->status == "3") {{ "selected" }} @endif>Rejected</option>
                                </select>
                                @error('status')
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
               
                <a href="{{ route('supports.index') }}" class="btn btn-default">Cancel</a>
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
        });
    </script>
@endpush
