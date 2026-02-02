<div class="table-responsive">
    <table class="table" id="contact-table">
        <thead>
        <tr>
            <th>Mobile</th>
            <th>Email</th>
            <th colspan="3">Action</th>
        </tr>
        </thead>
        <tbody>
        @foreach($contacts as $contact)
            <tr>
                <td>{{ $contact->mobile }}</td>
                <td>{{ $contact->email }}</td>
                <td width="120">
               
                    <div class='btn-group'>
                        <a href="{{ route('contacts.edit', ['contact'=> $contact->id]) }}"
                           class='btn btn-default btn-xs'>
                            <i class="far fa-edit"></i>
                        </a>
                       
                    </div>
                 
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
