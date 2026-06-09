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
        @include('flash::message')
        <div class="clearfix"></div>
        <div class="card">

           

            <div class="card-body">
                <div class="card-header p-1">
                    <h5>Redeemtion Edit</h5>
                </div>
                <div class="row">
                    <table class="table table-borderless">
                    <form action="{{ route('redeemtions.update', ['redeemtion' => $redeemtion->id ]) }}" method="POST" id="statusForm">
                        @csrf
                        <input type="hidden" name="_method" id="_method" value="PATCH">
                        <tr>
                            <td>Mason Name</td>
                            <td>
                                {{ $redeemtion->user->name ?? "" }}
                            </td>
                        </tr>
                        <tr>
                            <td>Gift Name</td>
                            <td>
                                {{ $redeemtion->catalogue->name ?? "" }}<br>
                            </td>
                        </tr>
                        @if(auth()->user()->role === App\Models\User::$adminRole && $redeemtion->status != "2")
                            <tr>
                                <td>Mason Address 1</td>
                                <td>
                                    <input type="text" name="address1" id="address1" class="form-control" value="{{ $redeemtion->address1 ?? '' }}">
                                    @error('address1')
                                        <span class="text text-danger">{{ $message }}</span>
                                    @enderror
                                </td>
                            </tr>
                            <tr>
                                <td>Mason Address 2</td>
                                <td>
                                    <input type="text" name="address2" id="address2" class="form-control" value="{{ $redeemtion->address2 ?? '' }}">
                                    @error('address2')
                                        <span class="text text-danger">{{ $message }}</span>
                                    @enderror
                                </td>
                            </tr>
                            <tr>
                                <td>Mason City</td>
                                <td>
                                    <input type="text" name="city" id="city" class="form-control" value="{{ $redeemtion->city ?? '' }}" >
                                    @error('city')
                                        <span class="text text-danger">{{ $message }}</span>
                                    @enderror
                                </td>
                            </tr>
                            <tr>
                                <td>Mason District</td>
                                <td>
                                    <input type="text" name="district" id="district" class="form-control" value="{{ $redeemtion->district ?? '' }}">
                                    @error('district')
                                        <span class="text text-danger">{{ $message }}</span>
                                    @enderror
                                </td>
                            </tr>
                            <tr>
                                <td>Mason State</td>
                                <td>
                                    <input type="text" name="state" id="state" class="form-control" value="{{ $redeemtion->state ?? '' }}" >
                                    @error('state')
                                        <span class="text text-danger">{{ $message }}</span>
                                    @enderror
                                </td>
                            </tr>
                            <tr>
                                <td>Mason Country</td>
                                <td>
                                    <input type="text" name="country" id="country" class="form-control" value="{{ $redeemtion->country ?? '' }}">
                                    @error('country')
                                        <span class="text text-danger">{{ $message }}</span>
                                    @enderror
                                </td>
                            </tr>
                            <tr>
                                <td>Mason Pincode</td>
                                <td>
                                    <input type="number" name="pincode" id="pincode" class="form-control" value="{{ $redeemtion->pincode ?? '' }}" >
                                    @error('pincode')
                                        <span class="text text-danger">{{ $message }}</span>
                                    @enderror
                                </td>
                            </tr>
                        @endif
                        <tr>
                            <td>Status</td>
                            <td>
                                @if ($redeemtion->status == "2")
                                    <span class="badge badge-danger">Rejected</span>

                                @else
                                    <select name="status" id="status" class="form-control">
                                        <option value="">Select Status</option>
                                        <option value="0" @if (isset($redeemtion->status) && $redeemtion->status == "0") {{ "selected" }} @endif >Pending</option>
                                        <option value="3" @if (isset($redeemtion->status) && $redeemtion->status == "3") {{ "selected" }} @endif>Order Placed</option>
                                        <option value="1" @if (isset($redeemtion->status) && $redeemtion->status == "1") {{ "selected" }} @endif>Delivered</option>
                                        <option value="2" @if (isset($redeemtion->status) && $redeemtion->status == "2") {{ "selected" }} @endif>Rejected</option>
                                    </select>
                                    @error('status')
                                        <span class="text text-danger">{{ $message }}</span>
                                    @enderror
                                @endif
                             </td>
                        </tr>
                        <tr>
                            <td>Remarks</td>
                            <td>
                                @if ($redeemtion->status == "2")
                                    {{ $redeemtion->remarks ?? '' }}
                                @else
                                    <textarea name="remarks" id="remarks_id" cols="30" rows="5" class="form-control">{{ $redeemtion->remarks ?? '' }}</textarea>
                                    @error('remarks')
                                        <span class="text text-danger">{{ $message }}</span>
                                    @enderror
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td>Delivery Date</td>
                            <td>
                                @if ($redeemtion->status == "2")
                                    {{ $redeemtion->delivery_date ?? '' }}

                                @else
                                    <input type="date" name="delivery_date" id="delivery_date" class="form-control" value="{{ $redeemtion->delivery_date ?? "" }}" min= {{ date('Y-m-d',strtotime($redeemtion->created_at)) ?? "" }}>
                                    @error('delivery_date')
                                        <span class="text text-danger">{{ $message }}</span>
                                    @enderror
                                @endif
                             </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>
                                @if ($redeemtion->status != "2")
                                 <button type="submit" class="btn btn-success">Save</button>
                                @endif
                             </td>
                        </tr>
                    </form>
                    </table>
                </div>

            </div>

            <div class="card-footer">
               
                <a href="{{ route('redeemtions.index') }}" class="btn btn-default">Cancel</a>
            </div>

          

        </div>
    </div>
@endsection
@push('page_scripts')
   <script nonce="{{ $cspNonce }}">
        $(document).ready(function(){
           $('#statusForm').on('submit', function(e){
            if($('#status').val() == 2){
                let status = confirm("Are you sure to reject this redeemtion ? Once you rejected it can not be undone.");
                if(!status){
                    e.preventDefault() ;
                }
            }
            
           });
        })  ;  
    </script> 
@endpush

