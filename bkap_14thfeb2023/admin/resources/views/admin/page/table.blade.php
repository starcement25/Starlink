<div class="table-responsive">
    <table class="table" id="catalogues-table">
        <thead>
        <tr>
            <th>Name</th>
        <th>Description</th>
            <th colspan="3">Action</th>
        </tr>
        </thead>
        <tbody>
        @foreach($catalogues as $catalogue)
            <tr>
                <td>{{ $catalogue->name }}</td>
            <td>{{ $catalogue->description }}</td>
                <td width="120">
                    {!! Form::open(['route' => ['catalogues.destroy', $catalogue->id], 'method' => 'delete']) !!}
                    <div class='btn-group'>
                        <a href="{{ route('catalogues.edit', [$catalogue->id]) }}"
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
