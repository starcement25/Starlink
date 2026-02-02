<!-- Name Field -->
<div class="form-group col-sm-6">
    {!! Form::label('name', 'Name:') !!}
    {!! Form::text('name', null, ['class' => ($errors->has('name')) ? 'form-control border-danger' : 'form-control','maxlength' => 255]) !!}
    @error('name')
        <span class="text text-danger">{{ $message }}</span>
    @enderror
</div>

<!-- From point Field -->
<div class="form-group col-sm-6">
    {!! Form::label('from_point', 'From Point:') !!}
    {!! Form::number('from_point', null, ['class' => ($errors->has('from_point')) ? 'form-control border-danger' :'form-control']) !!}
    @error('from_point')
        <span class="text text-danger">{{ $message }}</span>
    @enderror
</div>

<!--To Point Field -->
<div class="form-group col-sm-6">
    {!! Form::label('to_point', 'To Point:') !!}
    {!! Form::number('to_point', null, ['class' => ($errors->has('to_point')) ? 'form-control border-danger' : 'form-control','min' => 1]) !!}
    @error('to_point')
        <span class="text text-danger">{{ $message }}</span>
    @enderror
</div>