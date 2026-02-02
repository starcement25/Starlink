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
        <!-- <div class="dashboard-content-wrap">
            <div>
                 <lable for="">Logo</lable>
                 <form method="POST" enctype="multipart/form-data" id="image-upload" action="javascript:void(0)" >
                    <img id="preview-image-before-upload" src="{{URL::to('/')}}/public/logo/" alt="preview image" style="max-height: 250px;">
                    <input type="file" name="image" placeholder="Choose image" id="image">
                    <button type="submit" class="btn btn-primary" id="submit">Submit</button>
                 </form>
            </div>
            <div>
                 <lable for="">Booking Percentage</lable>
                 <input class="form-control"  type="number"  name="bpe" style="" id="booking-percentage" value="{{ $booking_percentage->value ?? ''}}">
                 <button class="btn btn-success " id="change-btn" onClick="changeSettings()"><i class="fa fa-save" aria-hidden="true"> Set</i></button>
            </div>

        </div> -->
        <div class="card">
                <div class="card-body">
                  <div class="table-responsive">
                    <table class="table table-striped">
                      <thead>
                        
                      </thead>
                      <tbody class="unic">
                        <tr>
                          <td class="py-1">
                          <lable for="">Logo</lable>
                          </td>
                          <td>
                            <form method="POST" enctype="multipart/form-data" id="image-upload" action="javascript:void(0)" >
                                <img id="preview-image-before-upload" src="{{URL::to('/')}}/public/logo/{{$settings->header_logo}}" alt="preview image" >
                                <input type="file" name="image" placeholder="Choose image" id="image">
                                <input type="hidden" value="header_logo" name="name">
                            </form>
                          </td>
                          <td>
                                <button type="submit" class="btn btn-primary" id="submit" form="image-upload">change</button>
                          </td>
                         
                        </tr>
                        <tr>
                          <td class="py-1">
                          <lable for="">footer Logo</lable>
                          </td>
                          <td>
                            <form method="POST" enctype="multipart/form-data" id="image-upload2" action="javascript:void(0)" >
                                <img id="preview-image-before-upload2" src="{{URL::to('/')}}/public/logo/{{$settings->footer_logo}}" alt="preview image" >
                                <input type="hidden" value="footer_logo" name="name">
                                <input type="file" name="image" placeholder="Choose image" id="image2">
                            </form>
                          </td>
                          <td>
                                <button type="submit" class="btn btn-primary" id="submit" form="image-upload2">change</button>
                          </td>
                         
                        </tr>
                        <tr>
                          <td class="py-1">
                          <lable for="">Booking Percentage</lable>
                          </td>
                          <td>
                          <input class="form-control"  type="number"  name="bpe" style="" id="booking-percentage" value="{{ $settings->booking_percentage}}">
                          </td>
                          <td>
                          <button class="btn btn-success " data-name="booking-percentage" onClick="changeSettings(this)"><i class="fa fa-save" aria-hidden="true"></i></button>
                          </td> 
                        </tr>
                        <tr>
                          <td class="py-1">
                          <lable for="">email</lable>
                          </td>
                          <td>
                          <input class="form-control"  type="text"  name="bpe" style="" id="email" value="{{ $settings->email ?? '' }}">
                          </td>
                          <td>
                          <button class="btn btn-success " data-name="email" onClick="changeSettings(this)"><i class="fa fa-save" aria-hidden="true"></i></button>
                          </td> 
                        </tr>
                        <tr>
                          <td class="py-1">
                          <lable for="">Mobile</lable>
                          </td>
                          <td>
                          <input class="form-control"  type="text"  name="bpe" style="" id="mobile" value="{{ $settings->mobile ?? '' }}">
                          </td>
                          <td>
                          <button class="btn btn-success " data-name="mobile" onClick="changeSettings(this)"><i class="fa fa-save" aria-hidden="true"></i></button>
                          </td> 
                        </tr>
                        <tr>
                          <td class="py-1">
                          <lable for="">Facebook</lable>
                          </td>
                          <td>
                          <input class="form-control"  type="text"  name="bpe" style="" id="facebook" value="{{ $settings->facebook ?? '' }}">
                          </td>
                          <td>
                          <button class="btn btn-success " data-name="facebook" onClick="changeSettings(this)"><i class="fa fa-save" aria-hidden="true"></i></button>
                          </td> 
                        </tr>
                        <tr>
                          <td class="py-1">
                          <lable for="">Google+</lable>
                          </td>
                          <td>
                          <input class="form-control"  type="text"  name="bpe" style="" id="mobile" value="{{ $settings->google_plus ?? '' }}">
                          </td>
                          <td>
                          <button class="btn btn-success " data-name="google_plus" onClick="changeSettings(this)"><i class="fa fa-save" aria-hidden="true"></i></button>
                          </td> 
                        </tr>
                        <tr>
                          <td class="py-1">
                          <lable for="">Instagram</lable>
                          </td>
                          <td>
                          <input class="form-control"  type="text"  name="bpe" style=""  value="{{ $settings->instagram ?? '' }}">
                          </td>
                          <td>
                          <button class="btn btn-success " data-name="instagram" onClick="changeSettings(this)"><i class="fa fa-save" aria-hidden="true"></i></button>
                          </td> 
                        </tr>
                        <tr>
                          <td class="py-1">
                          <lable for="">Tweeter</lable>
                          </td>
                          <td>
                          <input class="form-control"  type="text"  name="bpe" style=""  value="{{ $settings->twitter ?? '' }}">
                          </td>
                          <td>
                          <button class="btn btn-success " id="change-btn" data-name="twitter" onClick="changeSettings(this)"><i class="fa fa-save" aria-hidden="true"></i></button>
                          </td> 
                        </tr>
                        <tr>
                          <td class="py-1">
                          <lable for="">Address</lable>
                          </td>
                          <td>
                          <textarea class="form-control"  type="text"  name="bpe" style=""  value="{{ $settings->address ?? '' }}">{{ $settings->address ?? '' }}</textarea>

                          </td>
                          <td>
                          <button class="btn btn-success "  data-name="address" onClick="changeSettings(this)"><i class="fa fa-save" aria-hidden="true"></i></button>
                          </td> 
                        </tr>
                        <tr>
                          <td class="py-1">
                          <lable for="">Footer About</lable>
                          </td>
                          <td>
                          <textarea class="form-control"  type="text"  name="bpe" style=""  value="{{ $settings->footer_about ?? '' }}">{{ $settings->footer_about ?? '' }}</textarea>

                          </td>
                          <td>
                          <button class="btn btn-success "  data-name="footer_about" onClick="changeSettings(this)"><i class="fa fa-save" aria-hidden="true"></i></button>
                          </td> 
                        </tr>
                        
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
        
    </div>
    
</div>
@endsection
@section('js')
<link rel="stylesheet" href="{{URL::to('/')}}/public/alert/style.css"/>
<script src="{{URL::to('/')}}/public/alert/cute-alert.js"></script>
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
        $('#image2').change(function() {
            
            let reader = new FileReader();
            reader.onload = (e) => {
                $('#preview-image-before-upload2').attr('src', e.target.result);
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
                    
                    cuteToast({
                        type: "success", // or 'info', 'error', 'warning'
                        title:'Saved',
                        message: "Image has been uploaded successfully",
                        timer: 2000
                      });
                },
                error: function(data) {
                    console.log(data);
                }
            });
        });
        $('#image-upload2').submit(function(e) {
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
                    
                    cuteToast({
                        type: "success", // or 'info', 'error', 'warning'
                        title:'Saved',
                        message: "Image has been uploaded successfully",
                        timer: 2000
                      });
                },
                error: function(data) {
                    console.log(data);
                }
            });
        });
        
    }); 
</script>
<script>
   function changeSettings(self)
   {
    var value = self.parentElement.previousElementSibling.querySelector('input,textarea').value;
    var name = self.dataset.name;
   
        if(name != '' && value != '')
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
                    name:name,
                    value:value
                },
                success: function(result){
                    if(result['success'] == '1')
                    {

                      cuteToast({
                        type: "success", // or 'info', 'error', 'warning'
                        title:'Saved',
                        message: "Saved Successfully",
                        timer: 2000
                      })
                    }
                  }});
        }
   }
   
</script>
@endsection