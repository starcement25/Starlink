@extends('tour.layouts.app')

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

          
            <div class="card-header">
                <h5>Create Item</h5>
            </div>
            <form action="{{ route('tour.page.list.item.create', ['id'=> $pageId]) }}" method="post">
            <div class="card-body">
                <div class="row">
                            @csrf
                            <input type="hidden" name="page_id" value="{{ $pageId ?? null }}">
                            <!-- Title Field -->
                            <div class="form-group col-sm-6">
                                {!! Form::label('title', 'Title:') !!}
                                {!! Form::text('title', null, ['class' => ($errors->has('title')) ? 'form-control border-danger' : 'form-control', 'maxlength' => 255]) !!}
                                @error('title')
                                    <span class="text text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                        <!-- Name Field -->
                        <div class="form-group col-sm-6">
                            {!! Form::label('element_name', 'Name (View Purpose):') !!}
                            {!! Form::text('element_name', null, ['class' => ($errors->has('element_name')) ? 'form-control border-danger' : 'form-control',  'maxlength' => 255]) !!}
                            @error('element_name')
                                <span class="text text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                         <!-- Value Field -->
                         <div class="form-group col-sm-6">
                            {!! Form::label('element_value', 'Value:') !!}
                            {!! Form::text('element_value', null, ['class' => ($errors->has('element_value')) ? 'form-control border-danger' : 'form-control',  'maxlength' => 255]) !!}
                            @error('element_value')
                                <span class="text text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- <div class="form-group col-sm-6">
                            {!! Form::label('element_name', 'Name:(To be Shown In Table Header)') !!}
                            {!! Form::text('element_name', null, ['class' => ($errors->has('element_name')) ? 'form-control border-danger' : 'form-control', 'required', 'maxlength' => 255]) !!}
                            @error('element_name')
                                <span class="text text-danger">{{ $message }}</span>
                            @enderror
                        </div> --}}


                        <!-- Element Field -->
                        <div class="form-group col-sm-6">
                            {!! Form::label('element_type', 'Element Type:') !!}
                            {!! Form::select('element_type', $elementType, "", ['class' => ($errors->has('element_type')) ? 'form-control border-danger' : 'form-control', 'required']) !!}
                            @error('element_type')
                                <span class="text text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Order Field -->
                        <div class="form-group col-sm-6">
                            {!! Form::label('order', 'Order:') !!}
                            {!! Form::number('show_order', null, ['class' => ($errors->has('show_order')) ? 'form-control border-danger' : 'form-control', 'required']) !!}
                            @error('show_order')
                                <span class="text text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Is Active-->
                        <div class="form-group col-sm-6">
                            {!! Form::label('is_active', 'Element Status:') !!}
                            {!! Form::select('is_active', [''=>'Select Status', '1'=> 'Active', '0'=> 'Disabled'], "", ['class' => ($errors->has('element_type')) ? 'form-control border-danger' : 'form-control']) !!}
                            @error('is_active')
                                <span class="text text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Is Active-->
                        <div class="form-group col-sm-6">
                            {!! Form::label('is_required', 'Element Required: (Applicable Only for Text Box/ Select.)') !!}
                            {!! Form::select('is_required', [''=>'Select Required', '1'=> 'Yes', '0'=> 'No'], "", ['class' => ($errors->has('element_type')) ? 'form-control border-danger' : 'form-control']) !!}
                            @error('is_required')
                                <span class="text text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                </div>
            </div>

            <div class="card-footer">
                {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
                <a href="{{ route('tour.page.list', ['id' => $pageId ]) }}" class="btn btn-default">Cancel</a>
            </div>

        </form>

        </div>
    </div>
@endsection
@push('user-edit-blade')
    <script>
        $(document).ready(function(){
           
        });
    </script>
@endpush
