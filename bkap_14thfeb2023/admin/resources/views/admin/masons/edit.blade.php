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
                <h5>Edit Mason</h5>
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
        });
    </script>
@endpush
