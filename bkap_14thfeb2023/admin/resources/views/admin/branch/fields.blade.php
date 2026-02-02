<!-- Name Field -->
<div class="form-group col-sm-6">
    {!! Form::label('name', 'Name:') !!}
    {!! Form::text('name', null, ['class' => ($errors->has('name')) ? 'form-control border-danger' : 'form-control','maxlength' => 255]) !!}
    @error('name')
    <span class="text text-danger">{{ $message }}</span>
    @enderror
</div>

<!-- Code Field -->
<div class="form-group col-sm-6">
    {!! Form::label('branch_code', 'Branch Code:') !!}
    {!! Form::text('branch_code', null, ['class' => ($errors->has('branch_code')) ? 'form-control border-danger' : 'form-control','maxlength' => 255]) !!}
    @error('branch_code')
    <span class="text text-danger">{{ $message }}</span>
    @enderror
</div>

<!--Zone Field -->
<div class="form-group col-sm-6">
    {!! Form::label('zone_id', 'Zone:') !!}
    {!! Form::select('zone_id', $zoneOption, $zoneOptionSelected, ['class' => ($errors->has('zone_id')) ? 'form-control border-danger' : 'form-control']) !!}
    @error('zone_id')
        <span class="text text-danger">{{ $message }}</span>
    @enderror
</div>

<!--State Field -->
<div class="form-group col-sm-6">
    {!! Form::label('state_id', 'State:') !!}
    {!! Form::select('state_id', $stateOption, $stateOptionSelected, ['class' => ($errors->has('state_id')) ? 'form-control border-danger' : 'form-control']) !!}
    @error('state_id')
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

<!--Status Field -->
<div class="form-group col-sm-6">
    {!! Form::label('status', 'Status:') !!}
    {!! Form::select('status', [''=> 'Select Status', '1'=> 'Active', '0'=> 'Disabled'], $statusSelected, ['class' => ($errors->has('status')) ? 'form-control border-danger' : 'form-control']) !!}
    @error('status')
        <span class="text text-danger">{{ $message }}</span>
    @enderror
</div>