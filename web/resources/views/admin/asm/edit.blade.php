@extends('admin.layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-12">
                    {{-- <h1>Edit Users</h1> --}}
                </div>
            </div>
        </div>
    </section>

    <div class="content px-3">



        <div class="card">

            {!! Form::model($asm, ['route' => ['asm.update', $asm->id], 'method' => 'patch', 'files'=> true]) !!}
            <div class="card-header">
                <h5>Edit Mason</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @include('admin.asm.fields')
                </div>
            </div>

            <div class="card-footer">
                {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
                <a href="{{ route('asm.index') }}" class="btn btn-default">Cancel</a>
            </div>

            {!! Form::close() !!}

        </div>
    </div>
@endsection

@push('user-edit-blade')
    <script>
        $(document).ready(function(){
            $("#branches").select2();
        });
    </script>
@endpush
