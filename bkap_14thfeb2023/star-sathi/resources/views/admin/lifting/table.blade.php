<div class="table-responsive">
    <table class="table table-sm" id="lifting-table">
        <thead>
        <tr>
            <th>Date</th>
            <th>Name</th>
            <th>Product</th>
            <th>Quantity</th>
            <th>Remarks</th>
           {{--  <th></th> --}}
            <th colspan="2"></th>
        </tr>
        </thead>
        <tbody>
        @foreach($liftings as $lifting)
            <tr>
                <td>{{ date('d/m/Y', strtotime($lifting->lifting_date)) }}</td>
                <td>{{ $lifting->user->name ?? "" }}</td>
                <td>{{ $lifting->product->name ?? "" }}</td>
                <td>{{ $lifting->qty }}</td>
                <td>{{ $lifting->remark }}</td>
               {{--  <td><img src="#" alt="" height="50" width="50"></td> --}}
                <td width="120">
                    {!! Form::open(['route' => ['liftings.destroy', $lifting->id], 'method' => 'delete']) !!}
                    <div class='btn-group'>
                        <a href="{{ route('liftings.edit', [$lifting->id]) }}"
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
