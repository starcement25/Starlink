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
                    <form action="{{ route('supports.update', ['support' => $redeemtion->id ]) }}" method="POST">
                        @csrf
                        <input type="hidden" name="_method" id="_method" value="PATCH">
                        <tr>
                            <td>Mason Name</td>
                            <td>
                                {{ $redeemtion->user->name ?? ""}}
                            </td>
                        </tr>
                        <tr>
                            <td>Gift Name</td>
                            <td>
                                {{ $redeemtion->catalogue->name ?? "" }}<br>
                            </td>
                        </tr>
                        <tr>
                            <td>Support Type</td>
                            <td>
                                <select name="support_type" id="support_type" class="form-control">
                                    <option value="">Select Action</option>
                                    <option value="1" @if (isset($redeemtion->suppport_type) && $redeemtion->suppport_type == "1") {{ "selected" }} @endif >Not Delivered</option>
                                    <option value="2" @if (isset($redeemtion->suppport_type) && $redeemtion->suppport_type == "2") {{ "selected" }} @endif >Defective</option>
                                </select>
                                @error('support_type')
                                    <span class="text text-danger">{{ $message }}</span>
                                @enderror
                            </td>
                        </tr>
                        <tr>
                            <td>Comment</td>
                            <td>
                                <input type="text" class="form-control" name="comment" id="comment" value="{{ $redeemtion->comment ?? "" }}">
                                @error('comment')
                                    <span class="text text-danger">{{ $message }}</span>
                                @enderror
                            </td>
                        </tr>
                        {{-- <tr>
                            <td>Status</td>
                            <td>
                                <select name="suppport_type" id="suppport_type" class="form-control">
                                    <option value="">Select Status</option>
                                    <option value="0" @if (isset($redeemtion->status) && $redeemtion->status == "0") {{ "selected" }} @endif >Pending</option>
                                    <option value="1" @if (isset($redeemtion->status) && $redeemtion->status == "1") {{ "selected" }} @endif>Delivered</option>
                                    <option value="2" @if (isset($redeemtion->status) && $redeemtion->status == "2") {{ "selected" }} @endif>Rejected</option>
                                </select>
                             </td>
                        </tr> --}}
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
               
                <a href="{{ route('supports.list') }}" class="btn btn-default">Cancel</a>
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
