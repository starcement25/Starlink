<!-- FB link Field -->
<div class="form-group col-sm-12">
    {!! Form::label('fb_link', 'Facebook Link:') !!}
    {!! Form::text('fb_link', null, ['class' => ($errors->has('fb_link')) ? 'form-control border-danger' : 'form-control','maxlength' => 255]) !!}
    @error('fb_link')
        <span class="text text-danger">{{ $message }}</span>
    @enderror
</div>
<!-- Twitter link Field -->
<div class="form-group col-sm-12">
    {!! Form::label('twitter_link', 'Twitter Link:') !!}
    {!! Form::text('twitter_link', null, ['class' => ($errors->has('twitter_link')) ? 'form-control border-danger' : 'form-control','maxlength' => 255]) !!}
    @error('twitter_link')
        <span class="text text-danger">{{ $message }}</span>
    @enderror
</div>

<!-- Web link Field -->
<div class="form-group col-sm-12">
    {!! Form::label('web_link', 'URL Link:') !!}
    {!! Form::text('web_link', null, ['class' => ($errors->has('web_link')) ? 'form-control border-danger' : 'form-control','maxlength' => 255]) !!}
    @error('web_link')
        <span class="text text-danger">{{ $message }}</span>
    @enderror
</div>

<!-- Whats App No. Field -->
<div class="form-group col-sm-12">
    {!! Form::label('whatsapp_no', 'WhatsApp No. :') !!}
    {!! Form::text('whatsapp_no', null, ['class' => ($errors->has('whatsapp_no')) ? 'form-control border-danger' : 'form-control','maxlength' => 255]) !!}
    @error('whatsapp_no')
        <span class="text text-danger">{{ $message }}</span>
    @enderror
</div>

