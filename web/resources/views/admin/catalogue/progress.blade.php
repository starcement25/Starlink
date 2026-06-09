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

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Bulk Upload/Update Catalogues') }}</div>
                {!! Form::open(['route'=> 'catalogue.upload.save', 'method'=> 'POST' ,'id'=> 'uploadForm', 'files' => true]) !!}
                <div class="card-body">
                    <div id="message">

                    </div>
                    <div class="mb-2">
                        <div id="progress" class="progress">
                            <div id="progressbar" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 100%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">Please wait....</div>
                        </div>
                      {{--   <div id="pid" style="display: none">
                            <a href="{{ route('employee.upload.show') }}"><button class="btn btn-success">New Upload</button></a>
                        </div> --}}
                    </div>
                    {{-- @if(session()->has('employee_import'))
                        
                        <div class="mb-3" id="uploadField" style="display: none">
                        
                            {!! Form::label('csvFile', 'Csv File', ['class' => 'form-label']) !!}
                             {!! Form::file('csvFile', ['class' => 'form-control']) !!}
                            @error('csvFile')
                                <span class="text-danger">  {{  $message }} </span>
                            @enderror
                        </div> 
                    @else
                        <div class="mb-2">
                            <div id="progress" class="progress" style="display: none;">
                                <div id="progressbar" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 100%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">Please wait....</div>
                            </div>
                        </div>
                        <div class="mb-3" id="uploadField">
                        
                            {!! Form::label('csvFile', 'Csv File', ['class' => 'form-label']) !!}
                        {!! Form::file('csvFile', ['class' => 'form-control']) !!}
                            @error('csvFile')
                                <span class="text-danger">  {{  $message }} </span>
                            @enderror
                        </div>
                    @endif --}}

                   
                    
                    
                    {{-- <div class="row mb-0">
                        <div class="col-md-6 offset-md-4">
                            <button type="submit" class="btn btn-primary">
                                {{ __('Submit') }}
                            </button>
                        </div>
                    </div> --}}
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>
@endsection
@push('branch-bulk-upload-blade')
    <script nonce="{{ $cspNonce }}">
        var timer ;
        $(document).ready(function(){
            getProgress();
        });
        function getProgress() {
      //  $('#process').css('display', 'block');
         
            console.log('getProgress');
            $.ajax({
                url: "{{ route('catalogue.upload.progress') }}",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "POST",
                dataType:"json",
                contentType: false,
                processData: false,
                async: false,
                success: function (response) {
                    console.log(response);
                   if(response.success){
                        if(!response.import_status){
                            timer = setTimeout('getProgress()', 4000);
                        }else{
                            clearTimeout(timer);
                            $('#progress').css({'display':'none'}) ;
                              alert(response.message);
                              window.location.href = "{{ route('catalogue.upload.show') }}";
                              
                        }
                   }
                   else{
                            clearTimeout(timer);
                            $('#progress').css({'display':'none'}) ;
                            alert(response.message);
                            window.location.href = "{{ route('catalogue.upload.show') }}";
                   }
                   
                }
            });
        }
  
      
        
    </script>
    
@endpush