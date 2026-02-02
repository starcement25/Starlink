<!-- Name Field -->
<div class="form-group col-sm-6">
    {!! Form::label('name', 'Name:') !!}
    {!! Form::text('name', null, ['class' => ($errors->has('name')) ? 'form-control border-danger' : 'form-control','maxlength' => 255]) !!}
    @error('name')
    <span class="text text-danger">{{ $message }}</span>
    @enderror
</div>

<!-- Description Field -->
<div class="form-group col-sm-6">
    {!! Form::label('description', 'Description:') !!}
    {!! Form::text('description', null, ['class' => ($errors->has('description')) ? 'form-control border-danger' :'form-control']) !!}
    @error('description')
        <span class="text text-danger">{{ $message }}</span>
    @enderror
</div>
<!-- Mason Category Field -->
<div class="form-group col-sm-6">
    {!! Form::label('mason_category_id', 'Mason Category:') !!}
    {!! Form::select('mason_category_id', $categoryOption, $categorySelected, ['class' => ($errors->has('mason_category_id')) ? 'form-control border-danger' : 'form-control']) !!}
    @error('mason_category_id')
        <span class="text text-danger">{{ $message }}</span>
    @enderror
</div>

<!-- Point Field -->
<div class="form-group col-sm-6">
    {!! Form::label('point', 'Point:') !!}
    {!! Form::number('point', null, ['class' => ($errors->has('point')) ? 'form-control border-danger' : 'form-control','min' => 1]) !!}
    @error('point')
        <span class="text text-danger">{{ $message }}</span>
    @enderror
</div>

<!-- Image Field -->
<div class="form-group col-sm-6">
    {!! Form::label('image', 'Image:') !!}
    {!! Form::file('image', ['class'=> 'form-control']) !!}
    @error('image')
        <span class="text text-danger">{{ $message }}</span>
    @enderror
</div>