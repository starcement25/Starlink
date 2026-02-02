<!-- Name Field -->
<div class="form-group col-sm-6">
    {!! Form::label('name', 'Name:') !!}
    {!! Form::text('name', null, ['class' => ($errors->has('name')) ? 'form-control border-danger' : 'form-control', 'required', 'maxlength' => 255]) !!}
    @error('name')
        <span class="text text-danger">{{ $message }}</span>
    @enderror
</div>

<!-- Title Field -->
<div class="form-group col-sm-6">
    {!! Form::label('Title', 'Title:') !!}
    {!! Form::text('title', null, ['class' => ($errors->has('title')) ? 'form-control border-danger' : 'form-control', 'required', 'maxlength' => 255]) !!}
    @error('title')
        <span class="text text-danger">{{ $message }}</span>
    @enderror
</div>

<!-- Title Field -->
<div class="form-group col-sm-6">
    {!! Form::label('sub_title', 'Sub Title:') !!}
    {!! Form::text('sub_title', null, ['class' => ($errors->has('sub_title')) ? 'form-control border-danger' : 'form-control', 'required', 'maxlength' => 255]) !!}
    @error('sub_title')
        <span class="text text-danger">{{ $message }}</span>
    @enderror
</div>
