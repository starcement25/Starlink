@extends('admin.layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            
        </div>
    </section>

    <div class="content px-3">
        @include('flash::message')
        <div class="clearfix"></div>
        
        <div class="card">
            <div class="card-header">
                <div class="row p-0">
                    <div class="col-sm-6">
                        <h5>Verify Liftings</h5> 
                    </div>
                    <div class="col-sm-6">
                       
                    </div>
                </div>
            </div>
            <div class="card-body table-responsive">
                <form action="{{ route('verify.liftings') }}" method="GET" id="filterForm">
                    <div class="row">
                        <div class="form-group col-sm-4">
                            <label for="">Select Mason: </label>
                            <select name="mason" id="mason" class="form-control">
                                <option value="">Select Option</option>
                                    @if ( !empty($users))
                                        @foreach ($users as $key => $user)
                                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                                        @endforeach
                                    @endif
                            </select>
                           
                        </div>
                        <div class="form-group col-sm-2">
                            <label for="">From Date: </label>
                            <input type="date" name="fromDate" id="fromDate" class="form-control">
                        </div>
                        <div class="form-group col-md-2">
                            <label for="">From Date: </label>
                            <input type="date" name="toDate" id="toDate" class="form-control">
                        </div>
                        <div class="form-group col-md-2">
                            <label for="">&nbsp; </label>
                            <button type="submit" class="btn btn-success form-control">Search</button>
                        </div>
                        <div class="form-group col-md-2" id="verifyDiv" style="display:none;">
                            <label for="">&nbsp; </label>
                            <button type="button" class="btn btn-info form-control" onclick="changeBulkStatus()">Verify All</button>
                        </div>
                       
                       
                    </div>
                    
                        
                   
                </form>
            
                <table  class="table" id="lifting-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Product Name</th>
                            <th>Quantity</th>
                           {{--  <th>Image</th> --}}
                            <th>Point</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                </table>
              
              
            </div>

        </div>
    </div>
   
@endsection
@push('verify-lifting-blade')
    <script>
        $(document).ready(function(){
            $('#filterForm').on('submit', function(e){
                e.preventDefault() ;
                loadData();
            })

    });
    function loadData(){
        table = $('#lifting-table').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 20,
            ajax: {
                url: "{{ route('verify.liftings') }}",
                type: "GET",
                data: {_token: "{{csrf_token()}}", user: $('#mason').val(), fromDate: $('#fromDate').val(), toDate: $('#toDate').val()}
            },
            columns: [
                {data: 'lifting_date', name: 'lifting_date'},
                {data: 'product.name', name: 'product.name'},
                {data: 'qty',     name: 'qty'},
              /*   {data: 'img', name: 'img'}, */
                {data: 'reward.point', name: 'reward.point'},
                {data: 'status', name: 'status'},
                {data: 'action',     name: 'action', orderable: false, searchable: false},
            ],
            createdRow: function( row, data, dataIndex ) {
                $( row ).find('td:eq(4)').attr('id', 'verifyBadge'+data.id);
            },
            "bStateSave": true,
            "bDestroy": true,
            initComplete: function (settings, json) {
             if(json.recordsTotal > 0){
                $('#verifyDiv').css({'display':''});
             }
            },
            drawCallback: function (settings) {
                $('#verifyDiv').css({'display':'none'});
                // called on every server request
                // below function is compulsory put here with table id param
                //initDTCheckBox('tblState');
            }
        });
    }
    function changeStatus(params) {
       
        $.ajax({
            type: "POST", 
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: "{{ route('verify.submit') }}",
            data:{liftingId: params, updateType: $('#switch'+params).prop('checked') },
            dataType: "json",
            success: function(response)
            {
                if(response.status)
                {
                 $('#verifyBadge'+params).html(response.extra) ;
                }
                else{
                  alert(response.message);
                }
            }
        });

    }
    function changeBulkStatus() {
     
        $.ajax({
            type: "POST", 
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: "{{ route('verify.bulk.submit') }}",
            data:{user: $('#mason').val(), fromDate: $('#fromDate').val(), toDate: $('#toDate').val() },
            dataType: "json",
            success: function(response)
            {
                if(response.status)
                {
                    loadData();
                }
                else{
                   alert(response.message);
                }
            }
        });

    }
</script>
@endpush

