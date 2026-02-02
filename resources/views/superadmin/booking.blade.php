@extends('superadmin.layer')
@section('content')
<div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="dashboard-content-wrap">
            <div class="dashboard-header">
                <div class="dashboard-title">
                        <h3>Bookings</h3> 
                </div>      
            </div>
        </div>
    </div>
       <!---->
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
               
                <div class="table-responsive">
                <br>
                    @if(session()->has('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {!! session()->get('success') !!}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    <table class="table table-striped" id="table_id">
                        <thead>
                        <tr>
                            <th>
                             Image
                            </th>
                            <th>
                             Location
                            </th>
                            <th>
                            Host
                            </th>
                            <th>
                            User
                            </th>
                            <th>
                            from
                            </th>
                            <th>
                            To
                            </th>
                            
                            <th>
                            Booked On
                            </th>
                            <th>
                             No of Night
                            </th>
                            <th>
                             cost
                            </th>
                            <th>
                            payment Status
                            </th>
                            <th>
                            Status
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        @if(!$bookings->isEmpty())
                            @foreach($bookings as $booking)
                                <tr>
                                    <td class="py-1">
                                        <img src="{{URL::to('/')}}/public/img/locations/{{$booking->location->id}}-1.jpg" alt="image">
                                    </td>
                                    <td>
                                    {{strtoupper($booking->location->location_name)}}
                                    </td>
                                    <td>
                                       {{$booking->location->user->user_fn}}
                                    </td>
                                    <td>
                                       {{$booking->user->user_fn}}
                                    </td>
                                    @php 
                                        $fd = $booking->from_date;
                                        $fd = strtotime($fd);
                                        $fd = date('d-m-Y', $fd);

                                        $td = $booking->to_date;
                                        $td = strtotime($td);
                                        $td = date('d-m-Y', $td);

                                        $bd = $booking->created_at;
                                        $bd = strtotime($bd);
                                        $bd = date('d-m-Y', $bd);
                                    @endphp
                                    <td>
                                       {{$fd}}
                                    </td>
                                    <td>
                                       {{$td}}
                                    </td>
                                    <td>
                                       {{$bd}}
                                    </td>
                                    <td>
                                       {{$booking->tot_stay}}
                                    </td>
                                    <td>
                                       ${{$booking->tot_cast}}
                                    </td>
                                    <td>
                                       
                                    </td>
                                    <td>
                                       @if($booking->status == 1)
                                            <code style="color:green">Booked</code>
                                       @elseif($booking->status == 0)
                                            <code style="color:red">Canceled</code>
                                       @elseif($booking->status == 3)
                                            <code style="color:green">Cancel Rejected</code>
                                       
                                        @elseif($booking->status == 2)
                                        <button style="margin:10px" onclick="cancelAction(this)" type="submit" data-booking="{{$booking->id}}" data-value="0" class="btn btn-success  delete" title="cancel" >Approved</i></button>
                                        <button style="margin:10px" onclick="cancelAction(this)" type="submit" data-booking="{{$booking->id}}" data-value="3" class="btn btn-danger  delete" title="cancel" >Reject</i></button>
                                       @else
                                            <code style="color:orange">Checked Out</code>
                                       @endif
                                    </td>
                                    
                                </tr>
                            @endforeach
                        @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('js')
<link rel="stylesheet" href="{{URL::to('/')}}/public/alert/style.css"/>
<script src="{{URL::to('/')}}/public/alert/cute-alert.js"></script>
<script>
    function cancelAction(self)
    {
        
        var booking_id = self.dataset.booking;
        var value = self.dataset.value;
        var msg = "";
        var color = "";
        if(value == 3)
        {
            msg = "Approved";
            color = "green";
        }
        if(value == 4)
        {
            msg = "Rejected";
            color = "red";
        }
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: "{{ route('superadmin.cancel-action') }}",
            method: 'post',
            data: {
                id:booking_id,
                value:value
            },
            success: function(result){
                    if(result['success'] == '1')
                    {

                        cuteToast({
                        type: "success", // or 'info', 'error', 'warning'
                        title:'Saved',
                        message: msg,
                        timer: 2000
                        });
                        self.parentElement.innerHTML = '<code style="color:'+color+'"> Cancel '+msg+'</code>';
                    }
                }
        });


    }
</script>
<script>
    $(document).ready( function () {
    $('#table_id').DataTable();

    

} );
</script>

@endsection