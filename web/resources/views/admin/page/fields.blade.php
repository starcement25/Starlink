<!-- Name Field -->
<div class="form-group col-sm-12">
    {!! Form::label('name', 'Name:') !!}
    {!! Form::text('page_name', null, ['class' => ($errors->has('page_name')) ? 'form-control border-danger' : 'form-control','maxlength' => 255]) !!}
    @error('page_name')
    <span class="text text-danger">{{ $message }}</span>
    @enderror
</div>

<!-- Description Field -->
<div class="form-group col-sm-12">
    {!! Form::label('description', 'Description:') !!}
    {!! Form::textarea('value', null, ['class' => ($errors->has('value')) ? 'form-control border-danger' :'form-control', "id"=>"value"]) !!}
    @error('value')
    <span class="text text-danger">{{ $message }}</span>
    @enderror
</div>

