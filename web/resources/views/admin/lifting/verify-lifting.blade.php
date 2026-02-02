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
                        @foreach($downloadableFiles as $downloadableFile)
                            <a href="{{$downloadableFile['filePath']}}" download>
                                <button class="btn btn-secondary">
                                    {{$downloadableFile['fileName']}}
                                </button>
                            </a>
                       @endforeach
                    </div>
                </div>
            </div>
            <div class="card-body table-responsive">
                <form action="{{ route('verify.liftings') }}" method="GET" id="filterForm">
                    <div class="row">
                        <div class="form-group col-sm-3">
                            <label for="">Select Mason: </label>
                            <select name="mason" id="mason" class="form-control custom-select select2bs4">
                                <option value="">Select Option</option>
                                                                  
                            </select>
                           
                        </div>
                        <div class="form-group col-sm-2">
                            <label for="">From Date: </label>
                            <input type="date" name="fromDate" id="fromDate" class="form-control">
                        </div>
                        <div class="form-group col-md-2">
                            <label for="">To Date: </label>
                            <input type="date" name="toDate" id="toDate" class="form-control">
                        </div>
                        <div class="form-group col-md-1">
                            <label for="">&nbsp; </label>
                            <button type="submit" class="btn btn-success form-control">Search</button>
                        </div>
                        <div class="form-group col-md-2" >
                            <label for="">&nbsp; </label>
                            <button id="export" type="button" class="btn btn-info form-control" onclick="download()">
                                {{-- <span id="progressCount" hidden></span> --}}
                                <span id="snipping_loader" class="spinner-border spinner-border-sm" role="status" aria-hidden="true" hidden></span>
                                <span id="export_btn_text">Export</span>
                            </button>
                        </div>
                        @if(\Auth::check() && \Auth::user()->role == 5)
                            <div class="form-group col-md-2">
                                <label for="">&nbsp; </label>
                                <a href="{{route("verify.liftings.bulk.update")}}" target="_blank">
                                    <button type="button" class="btn btn-success form-control">Bulk Update</button>
                                </a>
                            </div>
                        @endif
                    </div>
                    
                        
                   
                </form>
            
                <table  class="table" id="lifting-table">
                    <thead>
                        <tr>
                            <th>Lifting ID</th>
                            <th>Date</th>
                            <th>Dealer Code</th>
                            <th>Dealer SAP Code</th>
                            <th>Dealer</th>
                            <th>Mason</th>
                            <th>Mason Mobile</th>
                            <th>Mason Branch</th>
                            <th>TE Code</th>
                            <th>TE Name</th>
                            <th>TE Phone</th>
                            <th>Zone</th>
                            <th>Product Name</th>
                            <th>Approved Quantity</th>
                            <th>Mason Submitted Quantity</th>
                            <th>Dealer Edited Qty</th>
                            <th>BD Edited Qty</th>
                            <!-- <th>Approved Qty</th> -->
                            <th>Last Modified By</th>
                            <th>Last Modified Date and Time</th>
                            <th>Auto Approved</th>
                            <th>Lifting By</th>
                            <th>Lifting Creation Date and Time</th>
                           {{--  <th>Image</th> --}}
                            <th>Point</th>
                            <th>Attachment</th>
                            <th>Status</th>
                            <th>Star Saathi / ASM Status</th>
                            <th>Stock Availability</th>
                            <th>Action Taken At</th>
                            <th>Verified By</th>
                            <th>Verified At</th>
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
            // $('#mason').select2();
            // $('#mason').one('select2:open', function(e) {
            //     $('input.select2-search__field').prop('placeholder', 'enter mason name or phone no.');
            //     // jQuery.noConflict();
            //     $('input.select2-search__field').keydown(()=>{
            //         let searchVal = $('input.select2-search__field').val();
            //         console.log(searchVal);
            //         searchMason(searchVal);
            //     });
            // });
            $('#filterForm').on('submit', function(e){
                e.preventDefault() ;
                if($('#fromDate').val() != "" && $('#toDate').val() !=""){
                    loadData();
                }else{
                    alert("From & To Date Is Required") ;
                }
                
            })

            $('#mason').select2(
            {
                
                    placeholder: 'Select Option',
                    allowClear: true,
                    ajax: {
                        url: "{{ route('mason.dropdown.options') }}", // your server endpoint
                        dataType: 'json',
                        delay: 400, // wait 400ms after typing before sending request
                        data: function (params) {
                            return {
                                q: params.term, // search term (can be 1 char)
                                page: params.page || 1,
                                path: "{{ request()->path() }}"
                            };
                        },
                        processResults: function (data, params) {
                            params.page = params.page || 1;
                            return {
                                results: $.map(data.items, function (item) {
                                    return {
                                        id: item.encoded_id, // Select2 internal ID
                                        text: item.text
                                    }
                                }), // array of {id, text}
                                pagination: {
                                    more: data.more
                                }
                            };
                        },
                        cache: true
                    },
                    minimumInputLength: 0 // allow even 0 or 1 char search

            });
        });
        function searchMason(searchVal)
        {
            let url = "{{route('verify.liftings.mason.search')}}";
            url += "/"+searchVal;
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                //  xhrFields: {
                //      responseType: 'blob',
                //  },
                type: 'GET',
                url: url,
                success: function(response) {
                    let masonOptions = "";
                    for(mason of response.data.masons)
                    {
                        masonOptions += "<option value="+mason.id+" > "+mason.name+" - "+mason.phone+" </option>";
                    }
                    $("#mason").html(masonOptions);
                }
            });
        }
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
                    {data: 'lifting_id', name: 'lifting_id'},
                    {data: 'lifting_date', name: 'lifting_date'},
                    {data: 'user.emp_code', name: 'user.emp_code', "defaultContent": ""},
                    {data: 'user.sap_code', name: 'user.sap_code', "defaultContent": ""},
                    {data: 'user.name', name: 'user.name', "defaultContent": ""},
                    {data: 'mason.name', name: 'mason.name', "defaultContent": ""},
                    {data: 'mason.phone', name: 'mason.phone', "defaultContent": ""},
                    {data: 'mason.branch', name: 'mason.branch', "defaultContent": ""},
                    {data: 'te.emp_code', name: 'te.emp_code', "defaultContent": ""},
                    {data: 'te.name', name: 'te.name', "defaultContent": ""},
                    {data: 'te.phone', name: 'te.phone', "defaultContent": ""},
                    {data: 'zone', name: 'zone', "defaultContent": ""},
                    {data: 'product.name', name: 'product.name', "defaultContent": ""},
                    {data: 'qty',     name: 'qty'},
                    {data: 'mason_submitted_qty',     name: 'mason_submitted_qty'},
                    {data: 'dealer_editted_qty',     name: 'dealer_editted_qty'},
                    {data: 'bd_editted_qty',     name: 'bd_editted_qty'},
                    /*{data: 'approved_qty',     name: 'approved_qty'},*/
                    {data: 'last_modified_by',     name: 'last_modified_by'},
                    {data: 'last_modified_date_time',     name: 'last_modified_date_time'},
                    {data: 'autolifting_approval',     name: 'autolifting_approval'},
                    {data: 'lifting_by',     name: 'lifting_by'},
                    {data: 'lifting_creation_date_and_time',     name: 'lifting_creation_date_and_time'},
                /*   {data: 'img', name: 'img'}, */
                    {data: 'reward.point', name: 'reward.point', "defaultContent": ""},
                    {data: 'reward.attachment', name: 'reward.attachment', "defaultContent": ""},
                    {data: 'status', name: 'status'},
                    {data: 'star_saathi_status', name: 'star_saathi_status'},
                    {data: 'available_stock', name: 'available_stock'},
                    {data: 'action_taken_at', name: 'action_taken_at'},
                    {data: 'verified_by', name: 'verified_by'},
                    {data: 'verified_by_at', name: 'verified_by_at'},
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
        
        function download() {
            // $('#export').prop('disabled', true);
            $('#snipping_loader').prop('hidden', false);

            let ajaxResult = "";
            $.ajax({
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                // xhrFields: {
                //     responseType: 'blob',
                // },
                type: 'POST',
                url: "{{ route('verify.liftings.download') }}",
                data: {
                    user: $('#mason').val(),
                    fromDate: $('#fromDate').val(),
                    toDate: $('#toDate').val()
                },
                async: true,
                success: function(result, status, xhr) {
                    var disposition = xhr.getResponseHeader('content-disposition');
                    var matches = /"([^"]*)"/.exec(disposition);
                    var filename = (matches != null && matches[1] ? matches[1] : 'verify-lifting.csv');

                    // The actual download
                    var blob = new Blob([result], {
                        type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                    });
                    var link = document.createElement('a');
                    link.href = window.URL.createObjectURL(blob);
                    link.download = filename;

                    document.body.appendChild(link);

                    link.click();
                    document.body.removeChild(link);
                    // console.log(result);
                    $('#export').prop('disabled', false);
                    $('#snipping_loader').prop('hidden', true);
                    // $('#progressCount').prop('hidden', true);
                    $('#export_btn_text').html('Export Excel');
                },
                error: function(result) {
                    ajaxResult = result;
                    
                }
            });
            
        }
    </script>
@endpush

