@extends('superadmin.layer')
@section('content')
<div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="dashboard-content-wrap">
            <div class="dashboard-header">
                <div class="dashboard-title">
                        <h3>Roles</h3>
                        
                </div>      
            </div>
        </div>
    </div>
    
       <!---->
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                
              <a href="" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#exampleModal">Add New Role</a>
             
                <div class="table-responsive">
                <br>
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <p>Please Fill Role Name</p>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                @if(session()->has('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {!! session()->get('success') !!}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>
                                Role
                            </th>
                            <th>
                                Action
                            </th>
                        </tr>
                        </thead>
                        <tbody id="tbody">
                        @if(!$roles->isEmpty())
                            @foreach( $roles as $role)
                                <tr>
                                    <td id="name{{$role->id}}">
                                       {{$role->role_name}}
                                    </td>
                                    <td>
                                        
                                        <button onclick="deleteRole(this)" data-id="{{$role->id}}" style="" type="submit" class="btn btn-warning btn-xs delete" title="delete" >  <i class="fa fa-trash" aria-hidden="true"></i> </button>
                                        <button onclick="editModal(this)" data-name="{{$role->role_name}}"  data-id="{{$role->id}}"  class="btn btn-warning btn-xs"><i class="fa fa-edit" aria-hidden="true"></i></button>
                                        <button  class="btn btn-xs btn-warning" data-bs-toggle="modal" data-bs-target="#pm{{$role->id}}"><i style="color:white" class="fa fa-user" aria-hidden="true"></i></button>
                                        <!-- Modal -->
                                        <div class="modal   fade"  id="pm{{$role->id}}" tabindex="-1" aria-labelledby="ModalLabel{{$role->id}}" aria-hidden="true">
                                            <div class="modal-dialog" >
                                                <div class="modal-content" >
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="ModalLabel{{$role->id}}">Add Permission</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body" >
                                                        <div>
                                                            <form action="{{route('superadmin.save-permission')}}" method="post">
                                                                @csrf
                                                                
                                                                @if(!$menus->isEmpty())
                                                                    @foreach($menus as $menu)
                                        
                                                                        <h4>{{$menu->name}}</h4>
                                                                        
                                                                        @if(!is_null($menu->links))
                                                                            @php 
                                                                                $checked = '';
                                                                            @endphp
                                                                            @foreach($menu->links as $link)
                                                                                @if(!is_null($role->permissions))
                                                                                    @foreach($role->permissions as $pm)
                                                                                        @if($pm->link_id == $link->id)
                                                                                            @php 
                                                                                                $checked = 'checked';
                                                                                                break;
                                                                                            @endphp
                                                                                        @else
                                                                                            @php
                                                                                                $checked = '';
                                                                                            @endphp
                                                                                        @endif
                                                                                    @endforeach
                                                                                @endif
                                                                                <label><input type="checkbox" name="links[]" value="{{$link->id}}" {{$checked}}> {{$link->link_name}} </label>
                                                                                <input type="hidden" name="role_id" value="{{$role->id}}">
                                                                            
                                                                            @endforeach
                                                                            <hr>
                                                                        @endif
                                                                    @endforeach
                                                                @endif
                                                                <button type="submit"  class="btn btn-warning">Save</button>
                                                            </form>
                                                        </div>
                                                        
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
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
<!-- Modal -->
<div class="modal   fade"  id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" >
      <div class="modal-content" >
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Add Role</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body" >
        <div class="mb-3">
            <form action="{{ route('superadmin.add-role') }}" method="post" >
                  @csrf
                  <label for="" class="form-label">Role Name</label>
                  <input type="text" class="form-control" id="role" name="role_name">
                  <p class="error" id="error"></p>
                  <button type="submit"  class="btn btn-warning">Add </button>
            </form>
        </div>

           
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editModalLabel">Update Role</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
        <div class="mb-3">
                  <label for="" class="form-label">Role Name</label>
                  <input type="text" class="form-control" id="edit-role-name" name="role">
                  <input type="hidden" id="edit-role-id">
                  <p class="error" id="error2"></p>
        </div>

            <button type="button" onClick="editRole()" class="btn btn-warning">Update</button>
        </div>
      </div>
    </div>
  </div>
@endsection
@section('js')
    <script>
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
            var id = self.dataset.id;
            $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                    });
                    $.ajax({
                    url: "{{ route('superadmin.delete-role') }}",
                    method: 'post',
                    data: {
                        id:id
                    },
                    success: function(result){
                        if(result['success'] == '1')
                        {
                           self.parentElement.parentElement.remove();
                        }
                    }});
           }
          

        }
    </script>
@endSection