<input type="text" name="lifting_id" value="{{$lifting->id}}" hidden>
{{-- <!-- User Field -->
<div class="form-group col-sm-6">
    {!! Form::label('user', 'TE/Dealer:') !!}
    {!! Form::select('user_id', $teOption, $teSelected, [ 'class' => ($errors->has('user_id')) ? 'form-control border-danger' : 'form-control']) !!}
    @error('user_id')
     <span class="text text-danger">{{ $message }}</span>
    @enderror
</div>

<!-- User Field -->
<div class="form-group col-sm-6">
    {!! Form::label('user', 'Mason:') !!}
    {!! Form::select('mason_id', $userOption, $userSelected, ['class' => ($errors->has('mason_id')) ? 'form-control border-danger' : 'form-control']) !!}
    @error('mason_id')
     <span class="text text-danger">{{ $message }}</span>
    @enderror
</div> --}}


<!-- Is Verified Field -->
<div class="form-group col-sm-6">
    {!! Form::label('is_verified', 'Is Verified:') !!}
    {!! Form::select('is_verified', $verificationOptions,$is_verified, ['class' => ($errors->has('is_verified')) ? 'form-control border-danger' :'form-control']) !!}
    @error('is_verified')
     <span class="text text-danger">{{ $message }}</span>
    @enderror
</div>

{{-- <!-- Date Field -->
<div class="form-group col-sm-6">
    {!! Form::label('date', 'Lifting Date:') !!}
    {!! Form::date('lifting_date', null, ['class' => ($errors->has('lifting_date')) ? 'form-control border-danger' : 'form-control','max' => $lifting->lifting_date]) !!}
    @error('lifting_date')
        <span class="text text-danger">{{ $message }}</span>
    @enderror
</div> --}}
{{-- <!-- Quantity Field -->
<div class="form-group col-sm-6">
    {!! Form::label('name', 'Quantity:') !!}
    {!! Form::number('qty', null, ['readonly'=>true,'class' => ($errors->has('qty')) ? 'form-control border-danger' : 'form-control','min' => 1]) !!}
    @error('qty')
        <span class="text text-danger">{{ $message }}</span>
    @enderror
</div> --}}


{{-- <!-- Remark Field -->
<div class="form-group col-sm-6">
    {!! Form::label('remark', 'remark:') !!}
    {!! Form::text('remark', null, ['class' => ($errors->has('remark')) ? 'form-control border-danger' :'form-control']) !!}
    @error('remark')
        <span class="text text-danger">{{ $message }}</span>
    @enderror
</div> --}}

<!-- File Upload Field -->
<div class="form-group col-sm-6">
    {!! Form::label('file', 'Upload File:') !!}
    {!! Form::file('file', ['class'=> 'form-control']) !!}
    @error('file')
        <span class="text text-danger">{{ $message }}</span>
    @enderror
</div>