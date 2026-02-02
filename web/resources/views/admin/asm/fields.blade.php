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

<!-- Email Field -->
<div class="form-group col-sm-6">
    {!! Form::label('email', 'Email:') !!}
    {!! Form::text('email', null, ['class' => ($errors->has('email')) ? 'form-control border-danger' : 'form-control']) !!}
    @error('email')
    <span class="text text-danger">{{ $message }}</span>
    @enderror
</div>

<!--Branch Field -->
<div class="form-group col-sm-6">
    {!! Form::label('branch_ids', 'Branch:') !!}
    {!! Form::select('branch_ids[]', $branchOptions, $branchSelected, ['class' => ($errors->has('state_id')) ? 'form-control border-danger' : 'form-control', 'multiple'=> true, 'id' => 'branches']) !!}
    @error('branch_ids')
        <span class="text text-danger">{{ $message }}</span>
    @enderror
</div>






