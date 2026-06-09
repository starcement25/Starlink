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
                        @if(auth()->user()->role === App\Models\User::$adminRole && in_array($redeemtion->status,[App\Models\UserCatalogueRedeemtion::STATUS_PENDING, App\Models\UserCatalogueRedeemtion::STATUS_ORDER_PLACED]))
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
                                @if (!in_array($redeemtion->status, [App\Models\UserCatalogueRedeemtion::STATUS_PENDING, App\Models\UserCatalogueRedeemtion::STATUS_ORDER_PLACED ]))
                                    <span class="badge badge-secondary">{{$redeemtion->getStatus()}}</span>

                                @else
                                    <select name="status" id="status" class="form-control">
                                        <option value="">Select Status</option>
                                        <option value="0" @if (isset($redeemtion->status) && $redeemtion->status == "0") {{ "selected" }} @endif >Pending</option>
                                        <option value="3" @if (isset($redeemtion->status) && $redeemtion->status == "3") {{ "selected" }} @endif>Order Placed</option>
                                        <option value="1" @if (isset($redeemtion->status) && $redeemtion->status == "1") {{ "selected" }} @endif>Delivered</option>
                                        <option value="5" @if (isset($redeemtion->status) && $redeemtion->status == "5") {{ "selected" }} @endif disabled>Delivery Acknowledment</option>
                                        <option value="6" @if (isset($redeemtion->status) && $redeemtion->status == "6") {{ "selected" }} @endif disabled>Complain / Feedback</option>
                                        @if($isRoleAbleToRejectRedemption)
                                        <option value="2" @if (isset($redeemtion->status) && $redeemtion->status == "2") {{ "selected" }} @endif>Rejected</option>
                                        @endif
                                        <option value="4" @if (isset($redeemtion->status) && $redeemtion->status == "4") {{ "selected" }} @endif>Undelivered</option>
                                    </select>
                                    @error('status')
                                        <span class="text text-danger">{{ $message }}</span>
                                    @enderror
                                @endif
                             </td>
                        </tr>
                         
                        <tr id="tracking_div" @if(!in_array($redeemtion->status, [App\Models\UserCatalogueRedeemtion::STATUS_ORDER_PLACED])) style="display:none;" @endif>
                            <td>Order Tracking URL</td>
                            <td>
                                    <input type="url" name="order_tracking_url" id="order_tracking_url" class="form-control" value="{{ $redeemtion->order_tracking_url }}">  
                            </td>
                         
                        </tr>
                       
                        <tr>
                            <td>Remarks</td>
                            <td>
                                @if (!in_array($redeemtion->status, [App\Models\UserCatalogueRedeemtion::STATUS_PENDING, App\Models\UserCatalogueRedeemtion::STATUS_ORDER_PLACED ]))
                                    {{ $redeemtion->remarks ?? '' }}
                                @else
                                    <textarea name="remarks" id="remarks_id" cols="30" rows="5" class="form-control">{{ $redeemtion->remarks ?? '' }}</textarea>
                                    @error('remarks')
                                        <span class="text text-danger">{{ $message }}</span>
                                    @enderror
                                @endif
                            </td>
                        </tr>
                            @if ($redeemtion->feedback!='')
                        <tr>
                            <td>Feedback</td>
                            <td>
                               
                                    {{ $redeemtion->feedback ?? '' }}
                             
                                
                            </td>
                        </tr>
                            @endif
                        <tr>
                            <td>Delivery Date</td>
                            <td>
                                @if (!in_array($redeemtion->status, [App\Models\UserCatalogueRedeemtion::STATUS_PENDING, App\Models\UserCatalogueRedeemtion::STATUS_ORDER_PLACED ]))
                                    {{ $redeemtion->delivery_date ?? '' }}

                                @else
                                    <!-- <input type="date" name="delivery_date" id="delivery_date" class="form-control" value="{{ $redeemtion->delivery_date ?? "" }}" min= {{ date('Y-m-d',strtotime($redeemtion->created_at)) ?? "" }}> -->
                                    <input type="date" name="delivery_date" id="delivery_date" class="form-control" value="{{ $redeemtion->delivery_date ?? "" }}" >
                                    @error('delivery_date')
                                        <span class="text text-danger">{{ $message }}</span>
                                    @enderror
                                @endif
                             </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>
                                @if (in_array($redeemtion->status, [App\Models\UserCatalogueRedeemtion::STATUS_PENDING, App\Models\UserCatalogueRedeemtion::STATUS_ORDER_PLACED ]))
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

           $('#status').on('change', function(){
            if($('#status').val() == 3){
                $('#tracking_div').css({'display': ''});
                $('#order_tracking_url').attr('required', true);
            }else{
                $('#tracking_div').css({'display': 'none'});
               $('#order_tracking_url').attr('required', false);
            }

            // Rejected.
            if($('#status').val() == 2){
                
                $('#remarks_id').attr('required', true);
            }else{
               
               $('#remarks_id').attr('required', false);
               $('#order_tracking_url').attr('required', false);
            }
           });
        })  ;  
    </script> 
@endpush

