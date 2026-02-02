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

<!-- Bag Field -->
<div class="form-group col-sm-6">
    {!! Form::label('bag', 'Bag:') !!}
    {!! Form::number('bag', $product->reward_point->bag ?? null, ['class' => ($errors->has('bag')) ? 'form-control border-danger' :'form-control']) !!}
    @error('bag')
        <span class="text text-danger">{{ $message }}</span>
    @enderror
</div>

<!-- Point Field -->
<div class="form-group col-sm-6">
    {!! Form::label('point', 'Point:') !!}
    {!! Form::number('point', $product->reward_point->point ?? null, ['class' => ($errors->has('point')) ? 'form-control border-danger' :'form-control']) !!}
    @error('point')
        <span class="text text-danger">{{ $message }}</span>
    @enderror
</div>