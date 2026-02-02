@extends('admin.layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-12">
                    <h1>Edit Catalogue</h1>
                </div>
            </div>
        </div>
    </section>

    <div class="content px-3">

       

        <div class="card">

            {!! Form::model($catalogue, ['files'=>true, 'route' => ['catalogues.update', $catalogue->id], 'method' => 'patch']) !!}

            <div class="card-body">
                <div class="row">
                    @include('admin.catalogue.fields')
                </div>
            </div>

            <div class="card-footer">
                {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
                <a href="{{ route('catalogues.index') }}" class="btn btn-default">Cancel</a>
            </div>

            {!! Form::close() !!}

        </div>
    </div>
@endsection
