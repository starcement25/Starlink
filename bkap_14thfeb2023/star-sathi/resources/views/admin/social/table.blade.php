<div class="table-responsive">
    <table class="table" id="contact-table">
        <thead>
        <tr>
            <th>Facebook Link</th>
            <th>Youtube Link</th>
            <th>Web Link</th>
            <th colspan="3">Action</th>
        </tr>
        </thead>
        <tbody>
        @foreach($links as $link)
            <tr>
                <td>{{ $link->fb_link ?? ""}}</td>
                <td>{{ $link->twitter_link ?? ""}}</td>
                <td>{{ $link->web_link ?? ""}}</td>
                <td width="120">
               
                    <div class='btn-group'>
                        <a href="{{ route('links.edit', ['link'=> $link->id ?? '#']) }}"
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
