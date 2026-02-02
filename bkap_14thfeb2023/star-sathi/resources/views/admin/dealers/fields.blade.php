<!-- Name Field -->
<div class="form-group col-sm-6">
    {!! Form::label('name', 'Name:') !!}
    {!! Form::text('name', null, ['class' => ($errors->has('name')) ? 'form-control border-danger' : 'form-control', 'required', 'maxlength' => 255]) !!}
    @error('name')
        <span class="text text-danger">{{ $message }}</span>
    @enderror
</div>

<!-- Name Field -->
<div class="form-group col-sm-6">
    {!! Form::label('emp_code', 'Code:') !!}
    {!! Form::text('emp_code', null, ['class' => ($errors->has('emp_code')) ? 'form-control border-danger' : 'form-control', 'required', 'maxlength' => 255]) !!}
    @error('emp_code')
        <span class="text text-danger">{{ $message }}</span>
    @enderror
</div>


<!-- Linked Dealer Field -->
<div class="form-group col-sm-6">
    {!! Form::label('role', 'Dealer Type:') !!}
    {!! Form::select('role', $roleOption, $roleSelected, ['class' => ($errors->has('role')) ? 'form-control border-danger' : 'form-control']) !!}
    @error('role')
        <span class="text text-danger">{{ $message }}</span>
    @enderror
</div>

<!-- Linked Dealer Field -->
<div class="form-group col-sm-6">
    {!! Form::label('linked_dealer', 'Linked Dealer:') !!}
    {!! Form::select('linked_dealer', $dealerOption, $dealerSelected, ['class' => ($errors->has('linked_dealer')) ? 'form-control border-danger' : 'form-control']) !!}
    @error('linked_dealer')
        <span class="text text-danger">{{ $message }}</span>
    @enderror
</div>

<!-- Branch Id Field -->
<div class="form-group col-sm-6">
    {!! Form::label('branch_id', 'Branch Id:') !!}
    {!! Form::select('branch_id', $branchOption, $branchSelected, ['class' => ($errors->has('branch_id')) ? 'form-control border-danger' : 'form-control']) !!}
    @error('branch_id')
        <span class="text text-danger">{{ $message }}</span>
    @enderror
</div>

<!-- Status Field -->
<div class="form-group col-sm-6">
    {!! Form::label('status', 'Status:') !!}
    {!! Form::select('status', $statusOption, $statusSelected, ['class' => ($errors->has('status')) ? 'form-control border-danger' : 'form-control']) !!}
    @error('status')
        <span class="text text-danger">{{ $message }}</span>
    @enderror
</div>

<!-- Phone Field -->
<div class="form-group col-sm-6">
    {!! Form::label('phone', 'Phone:') !!}
    {!! Form::text('phone', null, ['class' => ($errors->has('phone')) ? 'form-control border-danger' : 'form-control']) !!}
    @error('phone')
        <span class="text text-danger">{{ $message }}</span>
    @enderror
</div>

<!-- Whatsapp No Field -->
<div class="form-group col-sm-6">
    {!! Form::label('whatsapp_no', 'Whatsapp No:') !!}
    {!! Form::text('whatsapp_no', null, ['class' => ($errors->has('whatsapp_no')) ? 'form-control border-danger' : 'form-control']) !!}
    @error('whatsapp_no')
     <span class="text text-danger">{{ $message }}</span>
    @enderror
</div>