<!-- Name Field -->
<div class="form-group col-sm-6">
    {!! Form::label('name', 'Name:') !!}
    {!! Form::text('name', null, ['class' => ($errors->has('name')) ? 'form-control border-danger' : 'form-control','maxlength' => 255]) !!}
    @error('name')
    <span class="text text-danger">{{ $message }}</span>
    @enderror
</div>

<!-- Name Field -->
<div class="form-group col-sm-6">
    {!! Form::label('code', 'Employee Code:') !!}
    {!! Form::text('emp_code', null, ['class' => ($errors->has('emp_code')) ? 'form-control border-danger' : 'form-control','maxlength' => 255]) !!}
    @error('emp_code')
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
<div class="form-group col-sm-6">
    {!! Form::label('password', 'Password:') !!}
   
    {!! Form::password('password', ['class' => ($errors->has('password')) ? 'form-control border-danger' : 'form-control']) !!}
    @error('password')
        <span class="text text-danger">{{ $message }}</span>
    @enderror
</div>


<!-- Address Field -->
<div class="form-group col-sm-6">
    {!! Form::label('address', 'Address:') !!}
    {!! Form::text('address', null, ['class' => ($errors->has('address')) ? 'form-control border-danger' : 'form-control']) !!}
    @error('address')
        <span class="text text-danger">{{ $message }}</span>
    @enderror
</div>

<!-- Dob Field -->
<div class="form-group col-sm-6">
    {!! Form::label('dob', 'Dob:') !!}
    {!! Form::date('dob', null, ['class' => ($errors->has('dob')) ? 'form-control border-danger' : 'form-control']) !!}
    @error('dob')
        <span class="text text-danger">{{ $message }}</span>
    @enderror
</div>

<!-- Branch Field -->
<div class="form-group col-sm-6">
    {!! Form::label('branch', 'Branch:') !!}
    {!! Form::select('branch_id', $branchOption, $branchSelected, ['class' => ($errors->has('branch_id')) ? 'form-control border-danger' : 'form-control']) !!}
    
    @error('branch_id')
     <span class="text text-danger">{{ $message }}</span>
    @enderror
</div>

<!-- Maritial Field -->
<div class="form-group col-sm-6">
    {!! Form::label('marital_status', 'Marital Status:') !!}
    {!! Form::select('marital_status', $statusOption, $status, ['class' => ($errors->has('marital_status')) ? 'form-control border-danger' : 'form-control']) !!}
    
    @error('marital_status')
     <span class="text text-danger">{{ $message }}</span>
    @enderror
</div>

<!-- Role Field -->
<div class="form-group col-sm-6">
    {!! Form::label('role', 'Role:') !!}
    {!! Form::select('role', $roleOption, $roleSelected, ['class' => ($errors->has('role')) ? 'form-control border-danger' : 'form-control']) !!}
    
    @error('role')
     <span class="text text-danger">{{ $message }}</span>
    @enderror
</div>

@if(Request::is('*edit*'))
    @if($user->role == 2)
        <!-- Aadhaar Field -->
        <div class="form-group col-sm-6"  id="aadharField">
            {!! Form::label('aadhaar_no', 'Aadhar No:') !!}
            {!! Form::text('aadhaar_no', null, [ 'class' => ($errors->has('aadhaar_no')) ? 'form-control border-danger' : 'form-control']) !!}
            @error('aadhaar_no')
                <span class="text text-danger">{{ $message }}</span>
            @enderror
        </div>
    @else
         <!-- Aadhaar Field -->
        <div class="form-group col-sm-6"  style="display: none" id="aadharField">
            {!! Form::label('aadhaar_no', 'Aadhar No:') !!}
            {!! Form::text('aadhaar_no', null, [ 'class' => ($errors->has('aadhaar_no')) ? 'form-control border-danger' : 'form-control']) !!}
            @error('aadhaar_no')
                <span class="text text-danger">{{ $message }}</span>
            @enderror
        </div>
    @endif
        
@else
    <!-- Aadhaar Field -->
    <div class="form-group col-sm-6"  {{ Request::get("role") == '2' ? '' : 'style="display: none;"' }} id="aadharField">
        {!! Form::label('aadhaar_no', 'Aadhar No:') !!}
        {!! Form::text('aadhaar_no', null, [ 'class' => ($errors->has('aadhaar_no')) ? 'form-control border-danger' : 'form-control']) !!}
        @error('aadhaar_no')
            <span class="text text-danger">{{ $message }}</span>
        @enderror
    </div>
@endif
    



