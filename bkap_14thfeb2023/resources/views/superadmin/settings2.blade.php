@extends('superadmin.layer')
@section('content')
<div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="dashboard-content-wrap">
            <div class="dashboard-header">
                <div class="dashboard-title">
                        <h3> Setings</h3>
                        
                </div>      
            </div>
        </div>
    </div>
    
</div>
<div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="dashboard-content-wrap">
                 <div>
                 <lable for="">Logo</lable>
                 <form method="POST" enctype="multipart/form-data" id="image-upload" action="javascript:void(0)" >
                    <img id="preview-image-before-upload" src="{{URL::to('/')}}/public/logo/{{$logo->value}}" alt="preview image" style="max-height: 250px;">
                    <input type="file" name="image" placeholder="Choose image" id="image">
                    <button type="submit" class="btn btn-primary" id="submit">Submit</button>
                 </form>
            </div>
             <div>
                 <lable for="">Booking Percentage</lable>
                 <input class="form-control"  type="number"  name="bpe" style="" id="booking-percentage" value="{{ $booking_percentage->value ?? ''}}">
                 <button class="btn btn-success " id="change-btn" onClick="changeSettings()"><i class="fa fa-save" aria-hidden="true"> Set</i></button>
            </div>

        </div>
    </div>
    
</div>
@endsection
@section('js')
<script type = "text/javascript" >
    $(document).ready(function(e) {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $('#image').change(function() {
            
            let reader = new FileReader();
            reader.onload = (e) => {
                $('#preview-image-before-upload').attr('src', e.target.result);
            }
            reader.readAsDataURL(this.files[0]);
        });
        $('#image-upload').submit(function(e) {
            e.preventDefault();
            var formData = new FormData(this);
            $.ajax({
                type: 'POST',
                url: "{{ route('superadmin.change-logo') }}",
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                success: (data) => {
                    this.reset();
                    alert('Image has been uploaded successfully');
                },
                error: function(data) {
                    console.log(data);
                }
            });
        });
    }); 
</script>
<script>
   function changeSettings()
   {
       
    var value = $('#booking-percentage').val();
        if(!isNaN(value))
        {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{ route('superadmin.change-settings') }}",
                method: 'post',
                data: {
                    name:'booking_percentage',
                    value:value
                },
                success: function(result){
                    if(result['success'] == '1')
                    {
                        alert("Changed Successfully");
                    }
                  }});
        }
   }
</script>
@endsection