<!-- Mobile Field -->
<div class="form-group col-sm-12">
    {!! Form::label('mobile', 'Mobile:') !!}
    {!! Form::text('mobile', null, ['class' => ($errors->has('page_name')) ? 'form-control border-danger' : 'form-control','maxlength' => 255]) !!}
    @error('mobile')
    <span class="text text-danger">{{ $message }}</span>
    @enderror
</div>

<!-- Address Field -->
<div class="form-group col-sm-12">
    {!! Form::label('address', 'Address:') !!}
    {!! Form::text('address', null, ['class' => ($errors->has('value')) ? 'form-control border-danger' :'form-control', "id"=>"address"]) !!}
    @error('address')
        <span class="text text-danger">{{ $message }}</span>
    @enderror
</div>

