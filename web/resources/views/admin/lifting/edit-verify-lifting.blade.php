@extends('admin.layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-12">
                    <h1>Edit Lifting</h1>
                </div>
            </div>
        </div>
    </section>

    <div class="content px-3">

        @include('flash::message')
       

        <div class="card">

            {!! Form::model($lifting, ['route' => ['verify.liftings.edit', $lifting], 'method' => 'PATCH','files' => true]) !!}

            <div class="card-body">
                <div class="row">
                    @include('admin.lifting.edit-verify-fields')
                </div>
            </div>

            <div class="card-footer">
                {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
                <a href="{{ route('verify.liftings') }}" class="btn btn-default">Cancel</a>
            </div>

            {!! Form::close() !!}

        </div>
    </div>
@endsection
