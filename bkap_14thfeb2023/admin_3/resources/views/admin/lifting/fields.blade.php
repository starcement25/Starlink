<!-- User Field -->
<div class="form-group col-sm-6">
    {!! Form::label('user', 'User:') !!}
    {!! Form::select('user_id', $userOption, $userSelected, ['class' => ($errors->has('user_id')) ? 'form-control border-danger' : 'form-control']) !!}
    @error('user_id')
     <span class="text text-danger">{{ $message }}</span>
    @enderror
</div>

<!-- Product Field -->
<div class="form-group col-sm-6">
    {!! Form::label('product', 'Product:') !!}
    {!! Form::select('product_id', $productOption, $productSelected, ['class' => ($errors->has('product_id')) ? 'form-control border-danger' : 'form-control']) !!}
    @error('product_id')
     <span class="text text-danger">{{ $message }}</span>
    @enderror
</div>

<!-- Date Field -->
<div class="form-group col-sm-6">
    {!! Form::label('date', 'Lifting Date:') !!}
    {!! Form::date('lifting_date', null, ['class' => ($errors->has('lifting_date')) ? 'form-control border-danger' : 'form-control','min' => 1]) !!}
    @error('lifting_date')
        <span class="text text-danger">{{ $message }}</span>
    @enderror
</div>
<!-- Quantity Field -->
<div class="form-group col-sm-6">
    {!! Form::label('name', 'Quantity:') !!}
    {!! Form::number('qty', null, ['class' => ($errors->has('qty')) ? 'form-control border-danger' : 'form-control','min' => 1]) !!}
    @error('qty')
        <span class="text text-danger">{{ $message }}</span>
    @enderror
</div>

<!-- Image Field -->
<div class="form-group col-sm-6">
    {!! Form::label('date', 'Image:') !!}
    {!! Form::file('img', ['class'=> 'form-control']) !!}
    @error('img')
        <span class="text text-danger">{{ $message }}</span>
    @enderror
</div>



<!-- Price Field -->
<div class="form-group col-sm-6">
    {!! Form::label('remark', 'remark:') !!}
    {!! Form::text('remark', null, ['class' => ($errors->has('remark')) ? 'form-control border-danger' :'form-control']) !!}
    @error('remark')
        <span class="text text-danger">{{ $message }}</span>
    @enderror
</div>