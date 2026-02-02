<div class="card-body p-0">
    <div class="table-responsive">
        <table class="table" id="dealers-table">
            <thead>
            <tr>
                <th>Name</th>
                <th>Role</th>
                <th>Linked Dealer</th>
                <th>Branch Id</th>
                <th>Status</th>
                <th>Phone</th>
                <th>Whatsapp No</th>
                <th colspan="3">Action</th>
            </tr>
            </thead>
            <tbody>
            @foreach($dealers as $dealer)
                <tr>
                    <td>{{ $dealer->name }}</td>
                    <td>{{ $dealer->role }}</td>
                    <td>{{ $dealer->linked_dealer }}</td>
                    <td>{{ $dealer->branch_id }}</td>
                    <td>{{ $dealer->status }}</td>
                    <td>{{ $dealer->phone }}</td>
                    <td>{{ $dealer->whatsapp_no }}</td>
                    <td  style="width: 120px">
                        {!! Form::open(['route' => ['dealers.destroy', $dealer->id], 'method' => 'delete']) !!}
                        <div class='btn-group'>
                            <a href="{{ route('dealers.show', [$dealer->id]) }}"
                               class='btn btn-default btn-xs'>
                                <i class="far fa-eye"></i>
                            </a>
                            <a href="{{ route('dealers.edit', [$dealer->id]) }}"
                               class='btn btn-default btn-xs'>
                                <i class="far fa-edit"></i>
                            </a>
                            {!! Form::button('<i class="far fa-trash-alt"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                        </div>
                        {!! Form::close() !!}
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <div class="card-footer clearfix">
        <div class="float-right">
            @include('adminlte-templates::common.paginate', ['records' => $dealers])
        </div>
    </div>
</div>
