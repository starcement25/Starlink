@extends('superadmin.layer')
@section('content')
<div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="dashboard-content-wrap">
            <div class="dashboard-header">
                <div class="dashboard-title">
                        <h3>Assign Roles</h3>
                </div>      
            </div>
        </div>
    </div>
    
       <!---->
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
                @if(session()->has('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {!! session()->get('success') !!}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                <form action="{{route('superadmin.assign-role-save')}}" id="assign-role-form" method="post">
                    <p class="error" id="error"></p>
                    @csrf
                    <label for="" class="form-label">Users</label>
                    
                    <select  class="form-control" id="user" name="user_id" required>
                            <option class="" value="">Choose User</option>
                        @foreach($admins as $admin)
                            <option class="" value="{{$admin->id}}" {!! (!is_null($admin->assignRole))? 'disabled':'' !!}>{{$admin->user_fn}}</option>
                        @endforeach
                        
                        
                    </select>
                    <br>
                    <label for="" class="form-label">User Role</label>
                    <select  class="form-control" id="role" name="role_id" required>
                        <option class="" value="">Choose Role</option>
                        @foreach($roles as $role)
                            <option class="" value="{{$role->id}}">{{$role->role_name}}</option>
                        @endforeach
                    </select>
                    <br>
                    <button onclick="validateForm()" data-id="" style="" type="submit"  class="btn btn-success btn-xs delete" title="Assign" >  <i class="fa fa-save" aria-hidden="true"></i> Assign</button>
                </form>
                
            </div>
        </div>
    </div>
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
               
                <div class="table-responsive">
                <br>
               
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>
                            User
                            </th>
                            <th>
                            Role
                            </th>
                            <th>
                            Action
                            </th>

                        </tr>
                        </thead>
                        <tbody id="tbody">
                        @if(!$user_roles->isEmpty())
                            @foreach( $user_roles as $role)
                                <tr>
                                    <td id="name{{$role->id}}">
                                       {{$role->user->user_fn}}
                                    </td>
                                    <td id="name{{$role->id}}">
                                       {{$role->role->role_name}}
                                    </td>
                                    <td>
                                        <button onclick="deleteRole(this)" data-user="{{$role->user->id}}" style="" type="submit" class="btn btn-warning btn-xs delete" title="delete" >  <i class="fa fa-trash" aria-hidden="true"></i> </button>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
</div>

@endsection
@section('js')
    <script>
        $('#user').change(function(){
            var user_id = $(this).val();
            if(user_id != '')
            {
                $.ajaxSetup({
                  headers: {
                      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                  }
                });
                $.ajax({
                  url: "{{ route('superadmin.check-assign-role') }}",
                  method: 'post',
                  data: {
                     user_id:user_id
                  },
                  success: function(result){
                    if(result['success'] == '1')
                    {
                      alert('this user already assigned role');
                      $("#user option:first").attr('selected','selected');   
                    }
                  }});
            }
        });
        function editRole()
        {
            
            var id = $('#edit-role-id').val();
            var role_name = $('#edit-role-name').val();
           
            if( role_name != '' )
            {
                $.ajaxSetup({
                  headers: {
                      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                  }
                });
                $.ajax({
                  url: "{{ route('superadmin.add-role') }}",
                  method: 'post',
                  data: {
                     role_name:role_name,
                     role_id:id
                  },
                  success: function(result){
                     if(result['success'] == '1')
                     {
                        $('#editModal').modal('hide');
                        var nameid = '#name'+id;
                        $(nameid).html(result['data'].role_name);
                      
                        
                    }
                  }});

              
                
            }else
            {
                $('#error2').html('<i>Please fill this field</i>')
            }
            
        }
        function addRole()
        {
            
            var role = $('#role').val();
            if( role != '' )
            {
                $.ajaxSetup({
                  headers: {
                      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                  }
                });
                $.ajax({
                  url: "{{ route('superadmin.add-role') }}",
                  method: 'post',
                  data: {
                     role_name:role
                  },
                  success: function(result){
                     if(result['success'] == '1')
                     {
                        $('#exampleModal').modal('hide');
                       
                        var html = '<tr> <td id="name'+result['data'].id+'"> '+result['data'].role_name+' </td> <td> <button onclick="deleteRole(this)" data-id="'+result['data'].id+'" style="" type="submit" class="btn btn-warning btn-xs delete" title="delete" >  <i class="fa fa-trash" aria-hidden="true"></i> </button> <button onclick="editModal(this)" data-name="'+result['data'].role_name+'"  data-id="'+result['data'].id+'"  class="btn btn-warning btn-xs"><i class="fa fa-edit" aria-hidden="true"></i></button> </td> </tr>';
                        $('#tbody').prepend(html);
                    }
                  }});

              
                
            }else
            {
                $('#error').html('<i>Please fill this field</i>')
            }
            
        }
        function editModal(self)
        {
            
           var id = self.dataset.id;
           var role_name = self.dataset.name;
           $('#edit-role-id').val(id);
           $('#edit-role-name').val(role_name);
           $('#editModal').modal('show');
           self.parentElement.parentElement.id = 'deleteMe';
           
            
        }
        function deleteRole(self)
        {
           if(confirm('Are you sure to want to delete'))
           {
            var id = self.dataset.user;
            $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                    });
                    $.ajax({
                    url: "{{ route('superadmin.delete-assign-role') }}",
                    method: 'post',
                    data: {
                        user_id:id
                    },
                    success: function(result){
                        if(result['success'] == '1')
                        {
                           self.parentElement.parentElement.remove();
                        }
                    }});
           }
          

        }

        $.validator.setDefaults({
		    submitHandler: function() {
			//alert("submitted!");
                var form = document.getElementById("#assign-role-form");
                form.submit();
		    }
	    });
        $("#assign-role-form").validate({
			rules: {
               
                user_id: "required",
                role_id: "required",

			},
			
		});
    </script>
@endSection