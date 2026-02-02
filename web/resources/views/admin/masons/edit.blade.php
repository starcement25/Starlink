@extends('admin.layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-12">
                    {{-- <h1>Edit Users</h1> --}}
                </div>
            </div>
        </div>
    </section>

    <div class="content px-3">

     

        <div class="card">

            {!! Form::model($mason, ['route' => ['masons.update', $mason->id], 'method' => 'patch', 'files'=> true]) !!}
            <div class="card-header">
                <h5>Edit Contractor</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @include('admin.masons.fields')
                </div>
            </div>

            <div class="card-footer">
                {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
                <a href="{{ route('masons.index') }}" class="btn btn-default">Cancel</a>
            </div>

            {!! Form::close() !!}

        </div>
    </div>
@endsection
@push('user-edit-blade')
    <script>
        $(document).ready(function(){
            $("#dealers").select2();
            
            $('#role').on('change', function(){
                if($(this).val() == 2){
                    // Role Mason
                    $('#aadharField').css({"display":"block"}) ;
                    $('#aadhaar_no').attr("required", true);
                } else{
                    $('#aadharField').css({"display":"none"}) ;
                    $('#aadhaar_no').attr("required", false);

                }
               
            });
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
            $('#status').on('change', function(){
                if($('#status').val() === '0')
                {
                    $('#disable_reason_field').show();
                }
                else
                {
                    $('#disable_reason_field').hide();
                }
            });
            $('#branch').on('change',function(){
                let branchId=$('#branch').val();
                let option = '<option value=""> Select Dealers </option>';
                //document.write(branchId);
                let ajaxUrl= "{{route('dealers.fetch.branch_id', ['branch_id' => ':id'])}}" ;
                ajaxUrl = ajaxUrl.replace(':id',branchId);
                $.ajax({
                    type: "GET", 
                    url: ajaxUrl,
                    dataType: "json",
                    success: function(response)
                    {
                        if(response.status)
                        {         
                           let data = response.data;
                           data.forEach(function(data){
                            //alert(data.name);
                            option+= '<option value="'+data.id+'"> '+data.name+'  </option>' ;
                           }); 
                           $('#dealers').html(option);
                        }
                        else{
                            //alert(response.message);
                        }
                    }
                });
            });
        });
    </script>
@endpush
