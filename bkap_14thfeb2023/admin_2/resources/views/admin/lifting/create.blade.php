@extends('admin.layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            
        </div>
    </section>

    <div class="content px-3">

        <div class="card">
            <div class="card-header">
                <h5>Create Lifting</h5>
            </div>

            {!! Form::open(['route' => 'liftings.store', 'files'=> true]) !!}

            <div class="card-body">

                <div class="row">
                    @include('admin.lifting.fields')
                </div>

            </div>

            <div class="card-footer">
                {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
                <a href="{{ route('liftings.index') }}" class="btn btn-default">Cancel</a>
            </div>

            {!! Form::close() !!}

        </div>
    </div>
@endsection
