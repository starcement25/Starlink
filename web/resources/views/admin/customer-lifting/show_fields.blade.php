<!-- Name Field -->
<div class="col-sm-12">
    {!! Form::label('name', 'Name:') !!}
    <p>{{ $dealer->name }}</p>
</div>

<!-- Role Field -->
<div class="col-sm-12">
    {!! Form::label('role', 'Role:') !!}
    <p>{{ $dealer->role }}</p>
</div>

<!-- Linked Dealer Field -->
<div class="col-sm-12">
    {!! Form::label('linked_dealer', 'Linked Dealer:') !!}
    <p>{{ $dealer->linked_dealer }}</p>
</div>

<!-- Branch Id Field -->
<div class="col-sm-12">
    {!! Form::label('branch_id', 'Branch Id:') !!}
    <p>{{ $dealer->branch_id }}</p>
</div>

<!-- Status Field -->
<div class="col-sm-12">
    {!! Form::label('status', 'Status:') !!}
    <p>{{ $dealer->status }}</p>
</div>

<!-- Phone Field -->
<div class="col-sm-12">
    {!! Form::label('phone', 'Phone:') !!}
    <p>{{ $dealer->phone }}</p>
</div>

<!-- Whatsapp No Field -->
<div class="col-sm-12">
    {!! Form::label('whatsapp_no', 'Whatsapp No:') !!}
    <p>{{ $dealer->whatsapp_no }}</p>
</div>

<!-- Created At Field -->
<div class="col-sm-12">
    {!! Form::label('created_at', 'Created At:') !!}
    <p>{{ $dealer->created_at }}</p>
</div>

<!-- Updated At Field -->
<div class="col-sm-12">
    {!! Form::label('updated_at', 'Updated At:') !!}
    <p>{{ $dealer->updated_at }}</p>
</div>

