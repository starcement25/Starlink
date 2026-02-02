<!-- Name Field -->
<div class="form-group col-sm-6">
    {!! Form::label('title', 'Title:') !!}
    {!! Form::text('title', null, ['class' => ($errors->has('title')) ? 'form-control border-danger' : 'form-control','maxlength' => 255]) !!}
    @error('title')
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

<!-- Image Field -->
<div class="form-group col-sm-6">
    {!! Form::label('image', 'Image:(1024 X 500)') !!}
    {!! Form::file('image', ['class'=> 'form-control']) !!}
    @error('image')
        <span class="text text-danger">{{ $message }}</span>
    @enderror
</div>
<!--Status Field -->
<div class="form-group col-sm-6">
    {!! Form::label('status', 'Status:') !!}
    {!! Form::select('status', [''=> 'Select Status', '1'=> 'Active', '0'=> 'Disabled'], null, ['class' => ($errors->has('status')) ? 'form-control border-danger' : 'form-control','required' => true]) !!}
    @error('status')
        <span class="text text-danger">{{ $message }}</span>
    @enderror
</div>