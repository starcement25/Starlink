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
                        <h5>Point Ledger</h5>
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
                            <select name="mason" id="mason" class="form-control select2bs4">
                                <option value="">Select Option</option>
                                    @if ( !empty($users))
                                    <option value="ALL">ALL</option>
                                        @foreach ($users as $key => $user)
                                            <option value="{{ $user->id }}">{{ $user->name }}-{{ $user->phone }}</option>
                                        @endforeach
                                    @endif
                            </select>

                        </div>

                        <div class="form-group col-md-2">
                            <label for="">&nbsp; </label>
                            <button type="submit" class="btn btn-success form-control">Search</button>
                        </div>
                        <div class="form-group col-md-2" >
                            <label for="">&nbsp; </label>
                            <button type="button" class="btn btn-info form-control" onclick="htmlTableToExcel('xlsx')">Export</button>
                        </div>


                    </div>



                </form>

                <table  class="table table-sm" id="ledger-table">
                    <thead>
                        <tr>
                            {{-- <th>Mason Name</th> --}}
                            <th>Date</th>
                            <th>Order No</th>
                            <th>Name</th>
                            <th>Phone No.</th>
                            <th>Branch</th>
                            <th>TE Code</th>
                            <th>TE Name</th>
                            <th>Description</th>
                        {{--     <th>Remarks</th> --}}
                            <th>Credit Point</th>
                            <th>Debit Point</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
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

            });
            $('#mason').select2();
    });
    function loadData(){
        $.ajax({
            type: "GET",
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: "{{ route('fetch.ledger') }}",
            data:{ user: $('#mason').val() },
            dataType: "json",
            success: function(response)
            {
                if(response.success)
                {
                    let data = response.data;
                    let  tr = '' ;
                    let mason_details;
                    let masonName;
                    let masonPhone;
                    let masonBranch;
                    let teCode;
                    let teName;
                   for(let i=0 ; i< data.length; i++){
                    // console.log(i);
                    mason_details=JSON.parse(data[i].mason_details);
                    // if(mason_details == null)
                    // {
                    //     console.log(mason_details);
                    // }
                    masonName = mason_details == null ? "" : mason_details.name;
                    masonPhone = mason_details == null ? "" : mason_details.phone;
                    masonBranch = mason_details == null ? "" : mason_details.branch == null ? "" : mason_details.branch.name;
                    teCode = mason_details == null ? "" : mason_details.te == null ? "" : mason_details.te.code;
                    teName = mason_details == null ? "" : mason_details.te == null ? "" : mason_details.te.name;
                    tr += '<tr>'+
                                '<td>'+data[i].created_at+'</td>'+
                                '<td>'+data[i].order_id+'</td>'+
                                '<td>'+masonName+'</td>'+
                                '<td>'+masonPhone+'</td>'+
                                '<td>'+masonBranch+'</td>'+
                                '<td>'+teCode+'</td>'+
                                '<td>'+teName+'</td>'+
                                '<td>'+data[i].description+'</td>'+
                                '<!--<td>'+data[i].remarks+'</td>-->'+
                                '<td>'+data[i].credit_point+'</td>'+
                                '<td>'+data[i].debit_point+'</td>'+
                          '</tr>'
                   }

                $('#ledger-table tbody').html(tr);
                }
                else{
                  alert(response.message);
                }
            }
        });
    }
    function htmlTableToExcel(type){
        var data = document.getElementById('ledger-table');
        var excelFile = XLSX.utils.table_to_book(data, {sheet: "sheet1"});
        XLSX.write(excelFile, { bookType: type, bookSST: true, type: 'base64' });
        XLSX.writeFile(excelFile, 'ExportedFile:HTMLTableToExcel.' + type);
    }

</script>
@endpush

