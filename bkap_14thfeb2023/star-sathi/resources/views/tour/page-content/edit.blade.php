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
                <h5>Edit Item</h5>
            </div>
            <form action="{{ route('tour.page.list.item.update', ['id'=> $item->id]) }}" method="post">
            <div class="card-body">
                <div class="row">
                            @csrf
                            <input type="hidden" name="id" value="{{ $contentId ?? null }}">
                            <!-- Title Field -->
                        <div class="form-group col-sm-6">
                            {!! Form::label('title', 'Title:') !!}
                            {!! Form::text('title', $item->title, ['class' => ($errors->has('title')) ? 'form-control border-danger' : 'form-control', 'required', 'maxlength' => 255]) !!}
                            @error('title')
                                <span class="text text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Name Field -->
                       {{--  <div class="form-group col-sm-6">
                            {!! Form::label('element_name', 'Name:(To be Shown In Table Header)') !!}
                            {!! Form::text('element_name', null, ['class' => ($errors->has('element_name')) ? 'form-control border-danger' : 'form-control', 'required', 'maxlength' => 255]) !!}
                            @error('element_name')
                                <span class="text text-danger">{{ $message }}</span>
                            @enderror
                        </div> --}}

                        <!-- Element Field -->
                        <div class="form-group col-sm-6">
                            {!! Form::label('element_type', 'Element Type:') !!}
                            {!! Form::select('element_type', $elementType, $item->element_type, ['class' => ($errors->has('element_type')) ? 'form-control border-danger' : 'form-control']) !!}
                            @error('element_type')
                                <span class="text text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Order Field -->
                        <div class="form-group col-sm-6">
                            {!! Form::label('order', 'Order:') !!}
                            {!! Form::number('show_order', $item->show_order, ['class' => ($errors->has('show_order')) ? 'form-control border-danger' : 'form-control', 'required']) !!}
                            @error('show_order')
                                <span class="text text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Is Active-->
                        <div class="form-group col-sm-6">
                            {!! Form::label('is_active', 'Element Status:') !!}
                            {!! Form::select('is_active', [''=>'Select Status', '1'=> 'Active', '0'=> 'Disabled'], $item->is_active, ['class' => ($errors->has('element_type')) ? 'form-control border-danger' : 'form-control']) !!}
                            @error('is_active')
                                <span class="text text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Is Active-->
                        <div class="form-group col-sm-6">
                            {!! Form::label('is_required', 'Element Required: (Applicable Only for Text Input.)') !!}
                            {!! Form::select('is_required', [''=>'Select Required', '1'=> 'Yes', '0'=> 'No'], $item->is_required, ['class' => ($errors->has('element_type')) ? 'form-control border-danger' : 'form-control']) !!}
                            @error('is_required')
                                <span class="text text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                </div>
            </div>

            <div class="card-footer">
                {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
                <a href="{{ route('masons.index') }}" class="btn btn-default">Cancel</a>
            </div>

        </form>

        </div>
    </div>
@endsection
@push('user-edit-blade')
    <script>
        $(document).ready(function(){
            $('#role').on('change', function(){
                if($(this).val() == 2){
                    // Role Mason
                    $('#aadharField').css({"display":"block"}) ;
                    $('#aadhaar_no').attr("required", true);
                } else{
                    $('#aadharField').css({"display":"none"}) ;
                    $('#aadhaar_no').attr("required", false);

                }
               
            });
        });
    </script>
@endpush
