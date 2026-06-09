@extends('admin.layouts.app')
@push('third_party_stylesheets')
    @include('admin.layouts.datatables_css')
@endpush
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>ASM</h1>
                </div>
                {{-- <div class="col-sm-6">
                    <a class="btn btn-primary float-right"
                       href="{{ route('asm.create') }}">
                        Add New
                    </a>
                </div> --}}
                <div class="col-sm-6">
                    <a class="btn btn-primary float-right ml-1"
                       href="{{ route('asm.create') }}">
                        Add New
                    </a>
                    <a class="btn btn-primary float-right ml-1"
                       href="{{ route('asm.upload.show') }}">
                        Import
                    </a>
                    <a class="btn btn-primary float-right ml-1"
                       href="{{ route('asm.export') }}">
                        Export
                    </a>
                </div>
            </div>
        </div>
    </section>

    <div class="content px-3">
        @include('flash::message')
        <div class="clearfix"></div>

        <div class="card">
            <div class="card-body table-responsive">
             {{--    @include('admin.user.table') --}}
             {!! $dataTable->table() !!}

            </div>

        </div>
        @push('third_party_scripts')
            @include('admin.layouts.datatables_js')
            {!! $dataTable->scripts(attributes: ['nonce' => $cspNonce]) !!}
        @endpush
    </div>

@endsection

