@extends('tour.layouts.page-layout')

@section('content')
@if(session()->has('Success'))
  <div class="alert alert-success alert-dismissible fade show" role="alert">
    {{session()->get('Success')}} &nbsp; 
  </div>
@endif 

<div class="col-md-6 right">
    <h1>DEALER TOUR- DUBAI'23</h1>
    <h6>21st to 27th August'2023</h6>
    <img src="{{ asset('resources/tour/page/images/img-1.png') }}" alt="">
  </div>
 
  <div class="col-md-6">
    <form method="post" action="{{ route('page.submit') }}">
        @csrf
      <input type="hidden" name="page_id" value="{{ $pageId ?? "" }}">
      <div class="row formBx">
        @if (!empty($contents))
           
        @foreach ($contents as $item)
        @if( $item['type'] == "heading")
          <div class="col-md-12">
            <hr>
          </div>
          @endif
            <div class="col-md-12  @if( $item['type'] == "heading" || $item['type'] == "label") mb-1 @else mb-3 @endif">
             <?php echo  $item['element'] ;?>
            </div>
         @endforeach
            
        @endif
        <div class="col-md-12 mb-3">
          <button type="submit" class="btn submitBtn">Submit</button>
        </div>

      </div>
    </form>

  </div>
@endsection
@push('page-template')
  <script>
      $(document).ready(function(){
        getDealers();
        $('#checkBtn').click(function() {
          checked = $("input[type=checkbox]:checked").length;

          if(!checked) {
            alert("You must check at least one checkbox.");
            return false;
          }

      });

      $('#input_datalist_dealer_code').on('change', function(){
        getByDealerCode() ;
      })
      });
      function setRanking(rank_id){
        let elementName = rank_id.slice(7, -2) ;
        $('#rank_'+elementName).val($('#'+rank_id).val());
      //  alert($('#rank_'+elementName).val()) ;
      }
      function getDealers() {
        $.ajax({
            type: "GET", 
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: "{{ route('get.dealers') }}",
            dataType: "json",
            success: function(response)
            {
                if(response.success)
                {
                  let option = '<option value="">' ;
                  for(let i=0 ; i< response.data.length ; i++){
                    option += '<option value="'+response.data[i]+'">' ;
                  }
                  $('#datalist_dealer_code').empty();
                  $('#datalist_dealer_code').html(option);
                }
                else{
                  alert(response.message);
                }
            }
        });
      }  
      function getByDealerCode() {

      //alert($('#input_datalist_dealer_code').val());
        $.ajax({
            type: "GET", 
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: "{{ route('dealer.info') }}",
            data:{"dealerCode": $('#input_datalist_dealer_code').val()},
            dataType: "json",
            success: function(response)
            {
                if(response.success)
                {
                  $('#phone_no').val(response.data.phone);
                  $('#branch').val(response.data.branch.name);
                  $('#dealer_name').val(response.data.name);
                }
                else{
                  alert(response.message);
                }
            }
        });
      }  
  </script>    
@endpush