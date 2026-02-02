<!-- Name Field -->
<div class="form-group col-sm-6">
    {!! Form::label('name', 'Name:') !!}
    {!! Form::text('name', null, ['class' => ($errors->has('name')) ? 'form-control border-danger' : 'form-control','maxlength' => 255]) !!}
    @error('name')
    <span class="text text-danger">{{ $message }}</span>
    @enderror
</div>

<!-- code Field -->
<div class="form-group col-sm-6">
    {!! Form::label('code', 'Employee Code:') !!}
    {!! Form::text('emp_code', null, ['class' => ($errors->has('emp_code')) ? 'form-control border-danger' : 'form-control','maxlength' => 255]) !!}
    @error('emp_code')
    <span class="text text-danger">{{ $message }}</span>
    @enderror
</div>

<!-- Desiagnation Field -->
<div class="form-group col-sm-6">
    {!! Form::label('designation', 'Designation:') !!}
    {!! Form::text('designation', null, ['class' => ($errors->has('designation')) ? 'form-control border-danger' : 'form-control','maxlength' => 255]) !!}
    @error('designation')
        <span class="text text-danger">{{ $message }}</span>
    @enderror
</div>

<!-- Phone Field -->
<div class="form-group col-sm-6">
    {!! Form::label('phone', 'Phone:') !!}
    {!! Form::text('phone', null, ['class' => ($errors->has('phone')) ? 'form-control border-danger' : 'form-control','maxlength' => 10]) !!}
    @error('phone')
    <span class="text text-danger">{{ $message }}</span>
    @enderror
</div>

<!-- Email Field -->
<div class="form-group col-sm-6">
    {!! Form::label('email', 'Email:') !!}
    {!! Form::text('email', null, ['class' => ($errors->has('email')) ? 'form-control border-danger' : 'form-control','maxlength' => 255]) !!}
    @error('email')
        <span class="text text-danger">{{ $message }}</span>
    @enderror
</div>


<!-- Password Field -->
{{-- <div class="form-group col-sm-6">
    {!! Form::label('password', 'Password:') !!}
   
    {!! Form::password('password', ['class' => ($errors->has('password')) ? 'form-control border-danger' : 'form-control']) !!}
    @error('password')
        <span class="text text-danger">{{ $message }}</span>
    @enderror
</div> --}}


<!-- Branch Field -->
<div class="form-group col-sm-6">
    {!! Form::label('branch', 'Branch:') !!}
    {!! Form::select('branch_id', $branchOption, $branchSelected, ['name'=> 'branch_id[]', 'class' => ($errors->has('branch_id')) ? 'form-control border-danger' : 'form-control', 'multiple'=> true]) !!}
    
    @error('branch_id')
     <span class="text text-danger">{{ $message }}</span>
    @enderror
</div>
<!-- Address1 Field -->
<div class="form-group col-sm-6">
    {!! Form::label('address1', 'Address1:') !!}
    {!! Form::text('address1', null, ['class' => ($errors->has('address1')) ? 'form-control border-danger' : 'form-control']) !!}
    @error('address1')
        <span class="text text-danger">{{ $message }}</span>
    @enderror
</div>

<!-- Address2 Field -->
<div class="form-group col-sm-6">
    {!! Form::label('address2', 'Address2:') !!}
    {!! Form::text('address2', null, ['class' => ($errors->has('address2')) ? 'form-control border-danger' : 'form-control']) !!}
    @error('address2')
        <span class="text text-danger">{{ $message }}</span>
    @enderror
</div>

<!-- City Field -->
<div class="form-group col-sm-6">
    {!! Form::label('city', 'City:') !!}
    {!! Form::text('city', null, ['class' => ($errors->has('city')) ? 'form-control border-danger' : 'form-control']) !!}
    @error('city')
        <span class="text text-danger">{{ $message }}</span>
    @enderror
</div>
<!-- Pin Code Field -->
<div class="form-group col-sm-6">
    {!! Form::label('pincode', 'Pin Code:') !!}
    {!! Form::text('pincode', null, ['class' => ($errors->has('pincode')) ? 'form-control border-danger' : 'form-control']) !!}
    @error('pincode')
        <span class="text text-danger">{{ $message }}</span>
    @enderror
</div>

<!-- District Field -->
<div class="form-group col-sm-6">
    {!! Form::label('district', 'District:') !!}
    {!! Form::text('district', null, ['class' => ($errors->has('district')) ? 'form-control border-danger' : 'form-control']) !!}
    @error('district')
        <span class="text text-danger">{{ $message }}</span>
    @enderror
</div>

<!-- state Field -->
<div class="form-group col-sm-6">
    {!! Form::label('state', 'state:') !!}
    {!! Form::select('state', $stateOption, null, ['class' => ($errors->has('state')) ? 'form-control border-danger' : 'form-control']) !!}
    
    @error('state')
     <span class="text text-danger">{{ $message }}</span>
    @enderror
</div>

<!-- Status Field -->
<div class="form-group col-sm-6">
    {!! Form::label('status', 'Status:') !!}
    {!! Form::select('status', [''=> 'Select Staus', '1'=> 'Active', '0'=> 'Disabled'], $statusSelected, ['class' => ($errors->has('status')) ? 'form-control border-danger' : 'form-control']) !!}
    
    @error('status')
     <span class="text text-danger">{{ $message }}</span>
    @enderror
</div>


    



