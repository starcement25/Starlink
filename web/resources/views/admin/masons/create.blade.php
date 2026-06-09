@extends('admin.layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-12">
                    <h1>Create Contractor</h1>
                </div>
            </div>
        </div>
    </section>

    <div class="content px-3">

        <div class="card">

            {!! Form::open(['route' => 'masons.store', 'files'=>true]) !!}

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
@push('mason-create-blade')
    <script nonce="{{ $cspNonce }}">
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
