@extends('star-sathi-dealer.layouts.app')
@push('title')
    <title>Star Link | ASM</title>
@endpush
@push('third_party_styles')
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.4/css/select2.min.css'><link rel="stylesheet">
@endpush
@section('content')

    <div class=content>
        <div class="wrapper-1">
            <div class="wrapper-2">
                @if($status == 1)
                    <h1>Accepted</h1>
                @elseif($status == 2)
                    <h1 class="clr-rd">Rejected</h1>
                @else
                    <h1 class="clr-yl">Warning!</h1>
                @endif
                <p>{{$msg}}</p>
                <!-- <p>you should receive a confirmation email soon </p> -->
            </div>
        </div>
    </div>

@endsection
@push('third_party_scripts')
    <script src='https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.4/js/select2.min.js' defer></script>
@endpush
