<!-- Title Field -->
<div class="form-group col-sm-6">
    {!! Form::label('title', 'Title:') !!}
    {!! Form::text('title', null, ['class' => ($errors->has('title')) ? 'form-control border-danger' : 'form-control', 'required', 'maxlength' => 255]) !!}
    @error('title')
        <span class="text text-danger">{{ $message }}</span>
    @enderror
</div>

<!-- Name Field -->
<div class="form-group col-sm-6">
    {!! Form::label('element_name', 'Name:(To be Shown In Table Header)') !!}
    {!! Form::text('element_name', null, ['class' => ($errors->has('element_name')) ? 'form-control border-danger' : 'form-control', 'required', 'maxlength' => 255]) !!}
    @error('element_name')
        <span class="text text-danger">{{ $message }}</span>
    @enderror
</div>

<!-- Element Field -->
<div class="form-group col-sm-6">
    {!! Form::label('element_type', 'Element Type:') !!}
    {!! Form::select('element_type', $elementType, $elementSelected, ['class' => ($errors->has('element_type')) ? 'form-control border-danger' : 'form-control']) !!}
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
    {!! Form::select('is_active', [''=>'Select Status', '1'=> 'Active', '0'=> 'Disabled'], $elementSelected, ['class' => ($errors->has('element_type')) ? 'form-control border-danger' : 'form-control']) !!}
    @error('is_active')
        <span class="text text-danger">{{ $message }}</span>
    @enderror
</div>

<!-- Is Active-->
<div class="form-group col-sm-6">
    {!! Form::label('is_required', 'Element Required: (Applicable Only for Text Input.)') !!}
    {!! Form::select('is_required', [''=>'Select Required', '1'=> 'Yes', '0'=> 'No'], $elementSelected, ['class' => ($errors->has('element_type')) ? 'form-control border-danger' : 'form-control']) !!}
    @error('is_required')
        <span class="text text-danger">{{ $message }}</span>
    @enderror
</div>