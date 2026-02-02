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
                <div class="card-header">{{ __('Bulk Update') }}</div>
                {!! Form::open(['route'=> 'point.bulk.upload', 'method'=> 'POST' ,'id'=> 'uploadForm', 'files' => true]) !!}
                <div class="card-body">
                    <div id="message">

                    </div>
                    @if(session()->has('point_import'))
                        <div class="mb-2">
                            <div id="progress" class="progress">
                                <div id="progressbar" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 100%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">Please wait....</div>
                            </div>
                        </div>
                        <div class="mb-3" id="uploadField" style="display: none">
                        
                            {!! Form::label('csvFile', 'Csv File', ['class' => 'form-label']) !!}
                         
                            {!! Form::file('csvFile', ['class' => 'form-control', 'required'=> true, 'accept'=>".csv"]) !!}
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
                        {!! Form::file('csvFile', ['class' => 'form-control', 'required'=> true, 'accept'=>".csv"]) !!}
                            @error('csvFile')
                                <span class="text-danger">  {{  $message }} </span>
                            @enderror
                        </div>
                    @endif

                   
                    
                    
                    <div class="row mb-0">
                        <div class="col-md-6 offset-md-4">
                            <button type="submit" class="btn btn-primary">
                                {{ __('Submit') }}
                            </button>
                            
                           
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="{{ route('point.list') }}" class="btn btn-default">Cancel</a>
                    <a href="{{ asset('format/mason-point-format.csv') }}" download="mason-point-format">Download Format</a>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>
@endsection
@push('mason-bulk-upload-blade')
    <script>
        var timer ;
        $(document).ready(function(){
          //  getProgress();
            $('#uploadForm').on('submit', function(e){
                e.preventDefault();
                $('#uploadField').css({'display':'none'});
                $('#progress').css({'display':''});
                
                formData = new FormData();
                formData.append("csvFile", $('#csvFile').prop("files")[0]);
              
                $.ajax({
                    url: "{{ route('point.bulk.upload') }}",
                    headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: formData,
                    dataType: "json",
                    type: "POST",
                    cache: false,
                    processData: false,
                    contentType: false,
                    async: true,
                    enctype: "multipart/form-data",
                    success: function(response) {
                      //  start_import();

                        if(response.success){
                            if(response.import_status){
                                clearTimeout(timer);
                                $('#progress').css({'display':'none'}) ;
                                $('#uploadField').css({'display':''}) ;
                                $('#message').html('<div class="alert alert-success">'+response.message+'</div>');
                                $('#csvFile').val("");
                            }
                        }else{
                            clearTimeout(timer);
                            $('#progress').css({'display':'none'}) ;
                            $('#uploadField').css({'display':''}) ;
                            $('#message').html('<div class="alert alert-danger">'+response.message+'</div>');
                            $('#csvFile').val("");
                        }
                            
                        
                    console.log(response);
                    }
                });

            });
        });
        function getProgress() {
      //  $('#process').css('display', 'block');
         
            console.log('getProgress');
            $.ajax({
                url: "{{ route('redeemtion.upload.progress') }}",
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
                                $('#uploadField').css({'display':''}) ;
                                $('#message').html('<div class="alert alert-success">'+response.message+'</div>');
                                $('#csvFile').val("");
                        }
                   }
                    // console.log(response);
                    // let total_data     = response.total_line;
                    // let progress       = response.progress;
					// let width          = Math.round((progress / total_data) * 100);
					// $('#process_data').text(response.progress);
					// $('#total_data').text(response.total_line);
					// $('.progress-bar').css('width', width + '%');
					// $('#percentage').text(width + '%');

                    // if(total_data !== progress ){
                      
                      
                    // }
                    // if(width >= 100)
					// {
					// 	// clearInterval(clear_timer);
                    //     clearTimeout(timer);
					// 	$('#docs').val('');
					// 	$('#message').html('<div class="alert alert-success">Data Successfully Imported</div>');
					// 	$('#submit').attr('disabled',false);
					// 	$('#submit').val('Import');
                    //     $('.progress-bar').css('width',  '0%');
                    //     $('#process').css('display', 'none');
					// }
                }
            });
        }
      
        
    </script>
    
@endpush