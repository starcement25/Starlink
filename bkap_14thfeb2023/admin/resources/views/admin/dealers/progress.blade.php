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
                <div class="card-header">{{ __('Upload Dealers') }}</div>
            
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


                   
                </div>
               
            </div>
        </div>
    </div>
</div>
@endsection
@push('employee-bulk-upload-blade')
    <script>
        var timer ;
        $(document).ready(function(){
            getProgress();
        });
        function getProgress() {
      //  $('#process').css('display', 'block');
         
            console.log('getProgress');
            $.ajax({
                url: "{{ route('employee.upload.progress') }}",
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
                              window.location.href = "{{ route('employee.upload.show') }}";
                              
                        }
                   }
                   else{
                            clearTimeout(timer);
                            $('#progress').css({'display':'none'}) ;
                            alert(response.message);
                            window.location.href = "{{ route('employee.upload.show') }}";
                   }
                   
                }
            });
        }
  
      
        
    </script>
    
@endpush