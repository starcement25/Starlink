@extends('admin.layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-12">
                    <h1>Transfer Contractors</h1>
                </div>
            </div>
        </div>
    </section>

    <div class="content px-3">
        @include('flash::message')
        <form action="{{route('transfer.masons')}}" method="POST">
            @csrf
            <div class="card">
                    
                <div class="card-body">
                    <div class="row">
                        <div class="form-group col-sm-5">
                            <label for="transfer_from_te_id">Transfer From BDE</label>
                            <select name="transfer_from_te_id" class="form-control custom-select select2bs4" id="id_transfer_from_te_id" required>
                                <option value="">Select BDE</option>
                                @foreach($teList as $te)
                                    <option value="{{$te->id}}">{{$te->name." - ".$te->phone}}</option>
                                @endforeach
                            </select>
                            @error('transfer_from_te_id')
                                <span class="text-danger">{{$message}}</span>
                            @enderror
                        </div>
                        <div class="col-sm-2">
                            <label for="transfer_btn">Transfer<span id="selected_mason_count"> </span></label>
                            <button type="submit" class="form-control btn btn-success">Transfer</i></button>
                        </div>
                        <div class="form-group col-sm-5">
                            <label for="transfer_to_te_id">Transfer To BDE</label>
                            <select name="transfer_to_te_id" class="form-control custom-select select2bs4" id="id_transfer_to_te_id" required>
                                <option value="">Select BDE</option>
                                @foreach($teList as $te)
                                    <option value="{{$te->id}}">{{$te->name." - ".$te->phone}}</option>
                                @endforeach
                            </select>
                            @error('transfer_to_te_id')
                                <span class="text-danger">{{$message}}</span>
                            @enderror
                        </div>
                        <div class="form-group col-sm-5">
                            <label for="transfer_from_te_id">Select Contractors</label>
                            <select class="form-control custom-select selectpicker" name="masons[]" id="id_masons" multiple data-live-search="true" data-live-search-placeholder="Search by Contractor name or phone number" data-checkbox="true" title="No Contractors" data-actions-box="true" required>
                                
                            </select>   
                            @error('masons')
                                <span class="text-danger">{{$message}}</span>
                            @enderror                   
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('js')
    <script nonce="{{ $cspNonce }}">
        const TE_ROLE = 1;
        const MASON_ROLE = 2;
        let oldTeFromVal = "";
        let oldTeToVal = "";
        let changeBy = "";
        function searchUser(selectID, selectedValue, userRole, searchVal="", ignoreVal = "")
        {
            if(selectID != 'id_transfer_to_te_id')
            {
                changeBy = "te_to";
            }
            else
            {
                changeBy = "te_from";
            }
            let url = "{{route('ajax.search-user')}}";
            url += "?user_role="+userRole+"&searchVal="+searchVal+"&ignore="+ignoreVal; // replace placeholder with searchVal
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                //  xhrFields: {
                //      responseType: 'blob',
                //  },
                type: 'GET',
                url: url,
                success: function(response) {
                    let teOptions = `<option value="">Select TE</option>`;
                    for(te of response.data.teLists)
                    {
                        teOptions += "<option value="+te.id+" > "+te.name+" - "+te.phone+" </option>";
                    }
                    $(`#${selectID}`).html(teOptions);
                    $(`#${selectID}`).val(selectedValue).trigger('change');
                    changeBy = "";
                }
            });
        }
        function getTEMasons(selectID, teID)
        {
            let url = "{{route('ajax.get-te-masons', ':id')}}";
            url = url.replace(":id", teID); // replace placeholder with searchVal
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                //  xhrFields: {
                //      responseType: 'blob',
                //  },
                type: 'GET',
                url: url,
                success: function(response) {
                    
                    let masonOptions = "";
                    for(mason of response.data.masonLists)
                    {
                        masonOptions += "<option value="+mason.id+" > "+mason.name+" - "+mason.phone+" </option>";
                    }
                    $('.bootstrap-select .filter-option').text('Select Masons');
                    $(`#${selectID}`).html(masonOptions);
                    // // Refresh the selectpicker to reflect the added options
                    $(`#${selectID}`).selectpicker('refresh');
                }
            });
        }
        $(document).ready(function(){
            $("#id_transfer_from_te_id").select2();
            $("#id_transfer_to_te_id").select2();
            $('#id_transfer_from_te_id').one('select2:open', function(e) {
                $('input.select2-search__field').prop('placeholder', 'Enter TE name or phone number');
                
            });
            $('#id_transfer_to_te_id').one('select2:open', function(e) {
                $('input.select2-search__field').prop('placeholder', 'Enter TE name or phone number');
                
            });
            $('#id_transfer_from_te_id').change(function(){
                oldTeFromVal = $('#id_transfer_from_te_id').val();
                if(changeBy !== "te_to")
                {
                    searchUser('id_transfer_to_te_id', oldTeToVal, TE_ROLE, "", $('#id_transfer_from_te_id').val());
                    getTEMasons('id_masons', $('#id_transfer_from_te_id').val());
                }
                
            });
            $('#id_transfer_to_te_id').change(function(){
                oldTeToVal = $('#id_transfer_to_te_id').val();
                if(changeBy !== "te_from")
                {
                    searchUser('id_transfer_from_te_id', oldTeFromVal, TE_ROLE, "", $('#id_transfer_to_te_id').val());
                }
            });
            $(`#id_masons`).change(function(){
                // Get all selected options and retrieve their text
                let selectedTexts = [];
                $('#id_masons option:selected').each(function() {
                    selectedTexts.push($(this).text());
                });
                //show selected mason count
                if(selectedTexts.length > 1)
                {
                    $('#selected_mason_count').text(" "+(selectedTexts.length)+" masons");  // Update the UI
                }else if(selectedTexts.length == 1){
                    $('#selected_mason_count').text(" "+(selectedTexts.length)+" mason");  // Update the UI
                }else{
                    $('#selected_mason_count').text("");  // Update the UI
                }
                 // Limit the number of characters displayed
                let truncatedText = selectedTexts.join(', ');
                if (truncatedText.length > 45) {
                    truncatedText = truncatedText.substring(0, 45) + '...'; // Limit to 50 characters
                }
                $('.bootstrap-select .filter-option').text(truncatedText);
            });
        });
    </script>
@endpush
