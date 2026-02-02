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
    {!! Form::select('branch_id', $branchOption, $branchSelected, ['class' => ($errors->has('branch_id')) ? 'form-control border-danger' : 'form-control']) !!}
    
    @error('branch_id')
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


    



