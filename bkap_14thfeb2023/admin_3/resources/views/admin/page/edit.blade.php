@extends('admin.layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-12">
                    <h1>Edit Page</h1>
                </div>
            </div>
        </div>
    </section>

    <div class="content px-3">

       

        <div class="card">

            {!! Form::model($staticPage, ['files'=>true, 'route' => ['pages.update', $staticPage->id], 'method' => 'patch']) !!}

            <div class="card-body">
                <div class="row">
                    @include('admin.page.fields')
                </div>
            </div>

            <div class="card-footer">
                {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
                <a href="{{ route('pages.index') }}" class="btn btn-default">Cancel</a>
            </div>

            {!! Form::close() !!}

        </div>
    </div>
@endsection
@push('page-blade')
<script>
    $(document).ready(function(){
        $('#value').summernote({
            height: 500,
        })
    })
</script>
    
@endpush
