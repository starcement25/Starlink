@extends('admin.layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Ledger Master</h1>
                </div>
                <!-- @if(is_file(public_path("excel_exports/automate_ledger_points/Point_Ledger.csv")))
                    <div class="col-sm-6">
                        <a href="{{asset('excel_exports/automate_ledger_points/Point_Ledger.csv')}}" download>
                            <button class="btn btn-secondary">
                                Point_Ledger.csv
                            </button>
                        </a>
                    </div>
                @endif -->
                <div class="col-sm-12 ">
                    <div class="row mt-4">
                            <div class="form-group col-sm-4">
                                <a href="{{ route('all.ledger.export') }}" class="btn btn-secondary" target="_blank">Export All</a>
                            </div>
                </div>
                <div class="col-sm-12">
                    <form action="{{route('mason.ledger')}}" method="GET" id="filterForm">
                        <div class="row">
                            <div class="form-group col-sm-4">
                                <label for="">&nbsp; </label>
                                <select name="mason" id="mason" class="form-control select2bs4">
                                    <option value="">Select Option</option>
                                        {{-- @if ( !empty($users))
                                         <option value="{{ base64_encode('ALL') }}" <?php if(base64_encode('ALL') == $selectedMason ){ echo "selected"; } ?> >ALL</option> 
                                            @foreach ($users as $key => $user)
                                                <option value="{{ base64_encode($user->id) }}" <?php if(base64_encode($user->id) == $selectedMason ){ echo "selected"; } ?> >{{ $user->name }}-{{ $user->phone }}</option>
                                            @endforeach
                                        @endif --}}
                                </select>

                            </div>
                            <div class="form-group col-sm-2">
                                <label for="">From:</label>
                                <input type="date" name="date_from" class="form-control"
                                    value="{{ $selectedDateFrom ?? '' }}">
                                    @if($errorMsg['date_from'] ?? false)
                                    <span class="text-danger">{{$errorMsg['date_from']}}</span>
                                    @endif
                            </div>

                            <div class="form-group col-sm-2">
                                <label for="">To:</label>
                                <input type="date" name="date_to" class="form-control"
                                    value="{{ $selectedDateTo ?? '' }}">
                                    @if($errorMsg['date_to'] ?? false)
                                    <span class="text-danger">{{$errorMsg['date_to']}}</span>
                                    @endif
                            </div>


                            <div class="form-group col-md-2">
                                <label for="">&nbsp; </label>
                                <button type="submit" class="btn btn-success form-control">Search</button>
                            </div>
                            <div class="form-group col-md-2" >
                                <label for="">&nbsp; </label>
                                <a href="{{route('mason.ledger.export', ['mason' => $selectedMason == null ? 'null' : $selectedMason, 'date_from' => $selectedDateFrom, 'date_to' => $selectedDateTo])}}">
                                    <button type="button" class="btn btn-info form-control">
                                            Export
                                    </button>
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <div class="content px-3">
        @include('flash::message')
        <div class="clearfix"></div>

        <div class="card">

            <div class="card-body table-responsive">
               {!! $dataTable->table() !!}
            </div>

        </div>
        {!! $dataTable->scripts(attributes: ['nonce' => $cspNonce]) !!}
    </div>

@endsection

@push('verify-lifting-blade')
    <script nonce="{{ $cspNonce }}">
        const selectedMason = @json($selectedMason);
        $(document).ready(function(){
            $('#mason').select2({
                placeholder: 'Select Option',
                allowClear: true,
                ajax: {
                    url: "{{ route('mason.dropdown.options') }}", // your server endpoint
                    dataType: 'json',
                    delay: 400, // wait 400ms after typing before sending request
                    data: function (params) {
                        return {
                            q: params.term, // search term (can be 1 char)
                            page: params.page || 1
                        };
                    },
                    processResults: function (data, params) {
                        params.page = params.page || 1;
                        return {
                            results: $.map(data.items, function (item) {
                                return {
                                    id: item.encoded_id, // Select2 internal ID
                                    text: item.text
                                }
                            }), // array of {id, text}
                            pagination: {
                                more: data.more
                            }
                        };
                    },
                    cache: true
                },
                minimumInputLength: 0 // allow even 0 or 1 char search

            });

            // --------------- PRE-SELECT OLD VALUE ---------------
            
            if (selectedMason) {
                $.ajax({
                    url: "{{ route('mason.dropdown.options') }}",
                    data: { q: selectedMason, page: 1 },
                    success: function(res) {

                        // Find the selected option from API response
                        const match = res.items.find(item => item.encoded_id === selectedMason);

                        if (match) {
                            let option = new Option(match.text, match.encoded_id, true, true);
                            $('#mason').append(option).trigger('change');
                        }
                    }
                });
            }
        });
    </script>
@endpush

