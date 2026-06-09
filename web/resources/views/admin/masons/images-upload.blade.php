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
                <div class="card-header">{{ __('Upload Contractor\'s Aadhaar Documents') }}</div>
               {{--  <span class="badge badge-success">
                    22
                </span> --}}
                {!! Form::open(['route'=> 'mason.aadhaar.upload', 'method'=> 'POST' ,'id'=> 'uploadForm', 'files' => true]) !!}
                <div class="card-body">
                    <div id="message">

                    </div>
                 
                        <div class="mb-2">
                            <div id="progress" class="progress" style="display: none;">
                                <div id="progressbar" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 100%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">Please wait Do Not Refresh....</div>
                            </div>
                        </div>
                        <div class="mb-3" id="uploadField">
                        
                            {!! Form::label('images', 'Upload Documents (Max 20 Docs at a time)', ['class' => 'form-label']) !!}
                         
                            {!! Form::file('images', ['class' => 'form-control', 'multiple'=> true, 'id'=>'images', 'required'=> true, 'accept'=>"image/*"]) !!}
                            @error('images')
                                <span class="text-danger">  {{  $message }} </span>
                            @enderror
                        </div>
                

                   
                    
                    
                    <div class="row mb-0">
                        <div class="col-md-6 offset-md-4">
                            <button type="submit" class="btn btn-primary">
                                {{ __('Submit') }}
                            </button>
                           
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="{{ route('masons.index')}}" class="btn btn-default">Cancel</a>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>
@endsection
@push('mason-aadhaar-upload-blade')
    <script nonce="{{ $cspNonce }}">
        var timer ;
        $(document).ready(function(){
            $('#uploadForm').on('submit', function(e){
                e.preventDefault();
               
               // getProgress();
               
                let flag = true;
                var formData = new FormData(this);
                let TotalFiles = $('#images')[0].files.length; //Total files
                let files = $('#images')[0];
                if(TotalFiles > 20){
                    flag = false ;
                    alert('Only 20 Files allowed at a time to upload.') ;
                }
                for (let i = 0; i < TotalFiles; i++) {
                // formData.append('files' + i, files.files[i]);
                let sizeKb = parseFloat(files.files[i].size / 1024) ;
                let sizeMb = parseFloat(sizeKb / 1024) ;
                    if(sizeMb > 1){
                        flag = false ;
                        break;
                    
                    }else{
                        formData.append('my_files[]', files.files[i]);
                    }
                    
                }
                formData.delete('images');
                if(flag){
                    $('#uploadField').css({'display':'none'});
                    $('#progress').css({'display':''});

                        $.ajax({
                        url: "{{ route('mason.aadhaar.upload') }}",
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
                                    $('#tickets').val("");
                                }
                            }else{
                                clearTimeout(timer);
                                $('#progress').css({'display':'none'}) ;
                                $('#uploadField').css({'display':''}) ;
                                $('#message').html('<div class="alert alert-danger">'+response.message+'</div>');
                                $('#tickets').val("");
                            }
                                
                            
                    // console.log(response);
                        }
                    });
                }
                else{
                    alert("File Size can not be more than 1 Mb.") ;
                }


            });

        });
  
      
        
    </script>
    
@endpush