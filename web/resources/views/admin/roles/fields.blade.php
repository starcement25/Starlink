<!-- Role Name Field -->
<div class="form-group col-sm-12">
    {!! Form::label('role_name', 'Role Name:') !!}
    {!! Form::text('role_name', null, ['class' => 'form-control', 'required']) !!}
</div>

<div class="form-group col-sm-12">
    {!! Form::label('permissions', 'Permissions:') !!}
</div>

@foreach($menus as $menu)
    <div class="form-group col-sm-4">
        <ul>
            <li>
                <input type="checkbox" id={{$menu->name}} class="permission-group" onclick=getId(this) value="{{$menu->name}}">
                <label for="menus" ><strong>{{$menu->view_name}}</strong></label>
                @foreach($menu->permissions as $permission)
                    <ul>
                        <li>
                            <input type="checkbox"  name='permissions[]' class="{{$menu->name}}_permission" value={{$permission->id}} <?php if(in_array($permission->id,$roleHasPermission)){ echo 'checked';} ?>>
                            <label for="permission-6">{{$permission->display_name}}</label>
                        </li>
                    </ul>
                @endforeach
            </li>
        </ul>
    </div>
@endforeach

@push('js')
    <script>
        let ele;
        function getId(elem)
        {
            //console.log('hello');
            ele = $(elem).attr("id");
            if ($(elem).is(':checked'))
            {
            $(`.${ele}_permission`).each(function (){
                $(this).prop("checked", true);
            });
            //console.log($(elem).attr("id"));
            }
            else
            {
                $(`.${ele}_permission`).each(function (){
                    $(this).prop("checked", false);
                });
                //console.log($(elem).attr("id"));
            }
        };
    </script>
@endpush
