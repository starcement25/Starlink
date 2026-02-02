@extends('admin.layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Mason Points</h1>
                </div>
                <div class="col-sm-2">
                    <label for="">&nbsp; </label>
                            <!-- <button type="button" class="btn btn-info form-control" onclick="htmlTableToExcel('xlsx')">Export</button> -->
                            <a href="{{ route('mason.points.export') }}" class="btn btn-info form-control">Export</a>
                </div>
            </div>
        </div>
    </section>

    <div class="content px-3">
        @include('flash::message')
        <div class="clearfix"></div>

        <div class="card">
            <div class="card-body table-responsive">
                <table class="table table-sm" id="mason-point-table">
                    <thead>
                        <th>Name</th>
                        <th>Contact</th>
                        <th>Points</th>
                        <th>Mason Category</th>
                        <th>Branch</th>
                        <th>Zone</th>
                        <th>TE Code</th>
                        <th>TE Name</th>
                        <th>TE Mobile</th>
                        <th>Status</th>
                    </thead>
                    <tbody>
                        @if (!empty($users))
                              @foreach ($users as $user)
                                  <tr>
                                    <td>{{ $user->name ?? "" }}</td>
                                    <td>{{ $user->phone ?? "" }}</td>
                                    <td>{{ $user->points ?? ""  }}</td>
                                    <td>{{ $user->mason_category->name ?? ""  }}</td>
                                    <td>{{ $user->branch->name ?? ""  }}</td>
                                    <td>{{ $user->branch->zone->name ?? ""  }}</td>
                                    <td>{{ $user->te_linked->emp_code ?? ""  }}</td>
                                    <td>{{ $user->te_linked->name ?? ""  }}</td>
                                    <td>{{ $user->te_linked->phone ?? ""  }}</td>
                                    <td>{{ $user->getUserStatus() }}</td>
                                  </tr>
                              @endforeach  
                        @endif
                    </tbody>
                    
                </table>

                {{$users->links()}}

             {{-- {!! $dataTable->table() !!} --}}

            </div>

        </div>
       {{--  {!! $dataTable->scripts() !!} --}}
    </div>

@endsection

@push('mason-point-blade')
    <script>
        function htmlTableToExcel(type)
        {
            var data = document.getElementById('mason-point-table');
            var excelFile = XLSX.utils.table_to_book(data, {sheet: "sheet1"});
            XLSX.write(excelFile, { bookType: type, bookSST: true, type: 'base64' });
            XLSX.writeFile(excelFile, 'ExportedFile:Mason_Point.' + type);
        }
    
    </script>
@endpush

