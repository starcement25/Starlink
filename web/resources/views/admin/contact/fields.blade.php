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
    {!! Form::label('email', 'Email Address:') !!}
    {!! Form::text('email', null, ['class' => ($errors->has('value')) ? 'form-control border-danger' :'form-control', "id"=>"email"]) !!}
    @error('email')
        <span class="text text-danger">{{ $message }}</span>
    @enderror
</div>

