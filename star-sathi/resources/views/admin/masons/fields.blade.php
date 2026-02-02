<!-- Name Field -->
<div class="form-group col-sm-6">
    {!! Form::label('name', 'Name:') !!}
    {!! Form::text('name', null, ['class' => ($errors->has('name')) ? 'form-control border-danger' : 'form-control','maxlength' => 255]) !!}
    @error('name')
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

 <!-- Aadhaar Field -->
 <div class="form-group col-sm-6"  id="aadharField">
    {!! Form::label('aadhaar_no', 'Aadhar No:') !!}
    {!! Form::text('aadhaar_no', null, [ 'class' => ($errors->has('aadhaar_no')) ? 'form-control border-danger' : 'form-control']) !!}
    @error('aadhaar_no')
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

<!-- Maritial Field -->
<div class="form-group col-sm-6">
    {!! Form::label('marital_status', 'Maritial Status:') !!}
    {!! Form::select('marital_status', [''=> 'Select Status','1'=>'Married', '0'=> 'Un-married'], $maritalStatus, ['class' => ($errors->has('marital_status')) ? 'form-control border-danger' : 'form-control']) !!}
    
    @error('marital_status')
     <span class="text text-danger">{{ $message }}</span>
    @enderror
</div>

<!-- Spouse Field -->
<div class="form-group col-sm-6"  id="spouseField">
    {!! Form::label('spouse_name', 'Spouse Name:') !!}
    {!! Form::text('spouse_name', null, ['class' => ($errors->has('spouse_name')) ? 'form-control border-danger' : 'form-control']) !!}
    @error('spouse_name')
        <span class="text text-danger">{{ $message }}</span>
    @enderror
</div>

<!-- Spouse Field -->
<div class="form-group col-sm-6"  id="spouseDobField">
    {!! Form::label('spouse_dob', 'Spouse Dob:') !!}
    {!! Form::date('spouse_dob', null, ['class' => ($errors->has('spouse_dob')) ? 'form-control border-danger' : 'form-control']) !!}
    @error('spouse_dob')
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

<!-- Branch Field -->
<div class="form-group col-sm-6">
    {!! Form::label('branch', 'Branch:') !!}
    {!! Form::select('branch_id', $branchOption, $branchSelected, ['class' => ($errors->has('branch_id')) ? 'form-control border-danger' : 'form-control']) !!}
    
    @error('branch_id')
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

<!-- Status Field -->
<div class="form-group col-sm-6">
    {!! Form::label('status', 'Status:') !!}
    {!! Form::select('status', [''=>'Select Satatus', '1'=> 'Active', '0'=> 'Disabled'], $status, ['class' => ($errors->has('status')) ? 'form-control border-danger' : 'form-control']) !!}
    
    @error('status')
     <span class="text text-danger">{{ $message }}</span>
    @enderror
</div>

<!-- Created Field -->
<div class="form-group col-sm-6">
    {!! Form::label('parent', 'Created By:') !!}
    {!! Form::select('parent', $usersOption, $userSelected, ['class' => ($errors->has('parent')) ? 'form-control border-danger' : 'form-control']) !!}
    
    @error('parent')
     <span class="text text-danger">{{ $message }}</span>
    @enderror
</div>

<!-- Dealers Field -->
<div class="form-group col-sm-6">
    {!! Form::label('dealers', 'Dealers:') !!}
    {!! Form::select('dealers[]', $dealerOption, $dealerSelected, ['class' => ($errors->has('created_by')) ? 'form-control border-danger' : 'form-control', 'multiple'=> true, "id"=>"dealers"]) !!}
    
    @error('dealers')
     <span class="text text-danger">{{ $message }}</span>
    @enderror
</div>

<!-- Image Field -->
<div class="form-group col-sm-6">
    {!! Form::label('aadhar_img', 'Aadhar Image:') !!}
    {!! Form::file('aadhar_img', ['class'=> 'form-control']) !!}
    @error('aadhar_img')
        <span class="text text-danger">{{ $message }}</span>
    @enderror
</div>


    



