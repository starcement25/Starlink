<!-- Dealer Field -->
<div class="form-group col-sm-6">
    {!! Form::label('dealer_id', 'Dealer Name:') !!}
    {!! Form::select('dealer_id', $dealerOption, null, ['class' => ($errors->has('dealer_id')) ? 'form-control border-danger' : 'form-control']) !!}
    @error('dealer_id')
        <span class="text text-danger">{{ $message }}</span>
    @enderror
</div>

<!-- Year Field -->
<div class="form-group col-sm-6">
    {!! Form::label('year', 'Year:') !!}
    {!! Form::number('year', null, ['class' => ($errors->has('year')) ? 'form-control border-danger' : 'form-control']) !!}
    @error('year')
        <span class="text text-danger">{{ $message }}</span>
    @enderror
</div>

<!-- Month Field -->
<div class="form-group col-sm-6">
    {!! Form::label('month', 'Month:') !!}
    {!! Form::select('month', $monthOption, null, ['class' => ($errors->has('month')) ? 'form-control border-danger' : 'form-control']) !!}
    @error('month')
        <span class="text text-danger">{{ $message }}</span>
    @enderror
</div>

<!-- Dealer Field -->
<div class="form-group col-sm-6">
    {!! Form::label('product_id', 'Product:') !!}
    {!! Form::select('product_id', $productOption, null, ['class' => ($errors->has('product_id')) ? 'form-control border-danger' : 'form-control']) !!}
    @error('product_id')
        <span class="text text-danger">{{ $message }}</span>
    @enderror
</div>

<!-- Phone Field -->
<div class="form-group col-sm-6">
    {!! Form::label('quantity', 'Quantity:') !!}
    {!! Form::number('quantity', null, ['class' => ($errors->has('quantity')) ? 'form-control border-danger' : 'form-control']) !!}
    @error('quantity')
        <span class="text text-danger">{{ $message }}</span>
    @enderror
</div>

<!-- Status Field -->
<div class="form-group col-sm-6">
    {!! Form::label('status', 'Status:') !!}
    {!! Form::select('status', [''=>'Select Status', '1'=> 'Active', '0'=> 'Disable'], null, ['class' => ($errors->has('status')) ? 'form-control border-danger' : 'form-control']) !!}
    @error('status')
        <span class="text text-danger">{{ $message }}</span>
    @enderror
</div>