@extends('superadmin.layer')
@section('content')
<div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="dashboard-content-wrap">
            <div class="dashboard-header">
                <div class="dashboard-title">
                        <h3>Edit Content</h3>
                </div>      
            </div>
        </div>
    </div>
    
   
    <div class="col-lg-12 grid-margin stretch-card">
        <center>
            <h5>Please visit site to edti content</h5>
            <a href="{{ url('/')}}" class="btn btn-success">Edit Content</a>
        </center>
    
    </div>
</div>
@endsection
@section('js')

@endsection