<!-- Name Field -->
<div class="form-group col-sm-6">
    {!! Form::label('name', 'Name:') !!}
    {!! Form::text('name', null, ['class' => ($errors->has('name')) ? 'form-control border-danger' : 'form-control','maxlength' => 255]) !!}
    @error('name')
        <span class="text text-danger">{{ $message }}</span>
    @enderror
</div>

<!-- Status Field -->
<div class="form-group col-sm-6">
    {!! Form::label('status', 'Status:') !!}
    {!! Form::select('status', [''=> 'Select Status', '1'=>'Active', '0'=> 'Disabled'], $selected, ['class' => ($errors->has('status')) ? 'form-control border-danger' : 'form-control']) !!}

    @error('status')
        <span class="text text-danger">{{ $message }}</span>
    @enderror
</div>

