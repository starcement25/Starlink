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
    {!! Form::number('bag', $product->reward_point->bag ?? "", ['class' => ($errors->has('bag')) ? 'form-control border-danger' :'form-control']) !!}
    @error('bag')
        <span class="text text-danger">{{ $message }}</span>
    @enderror
</div>

<!-- Point Field -->
<div class="form-group col-sm-6">
    {!! Form::label('point', 'Point:') !!}
    {!! Form::number('point', $product->reward_point->point ?? "", ['class' => ($errors->has('point')) ? 'form-control border-danger' :'form-control']) !!}
    @error('point')
        <span class="text text-danger">{{ $message }}</span>
    @enderror
</div>

<!-- Bonus Point Field -->
<div class="form-group col-sm-6">
    {!! Form::label('bonus', 'Bonus Point:') !!}
    {!! Form::number('bonus_points', null, ['class' => ($errors->has('bonus_points')) ? 'form-control border-danger' :'form-control']) !!}
    @error('bonus_points')
        <span class="text text-danger">{{ $message }}</span>
    @enderror
</div>

<!-- Bonus Point Field -->
<div class="form-group col-sm-6">
    {!! Form::label('more_than_bags', 'More Than Bags:') !!}
    {!! Form::number('more_than_bags', null, ['class' => ($errors->has('more_than_bags')) ? 'form-control border-danger' :'form-control']) !!}
    @error('more_than_bags')
        <span class="text text-danger">{{ $message }}</span>
    @enderror
</div>

<!-- Status Field -->
<div class="form-group col-sm-6">
    {!! Form::label('status', 'Status:') !!}
    {!! Form::select('status', [''=> 'Select Status', '1'=> 'Active', '0'=>'Deactive'], $product->status ?? "1", ['class' => 'form-control custom-select']) !!}
</div>