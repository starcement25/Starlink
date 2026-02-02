@extends('admin.layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-12">
                 
                </div>
            </div>
        </div>
    </section>

    <div class="content px-3">

        <div class="card">

           

            <div class="card-body">
                <div class="card-header p-1">
                    <h5>Redeemtion Edit</h5>
                </div>
                <div class="row">
                    <table class="table table-borderless">
                    <form action="{{ route('redeemtions.update', ['redeemtion' => $redeemtion->id ]) }}" method="POST">
                        @csrf
                        <input type="hidden" name="_method" id="_method" value="PATCH">
                        <tr>
                            <td>Mason Name</td>
                            <td>
                                {{ $redeemtion->user->name ?? ""}}
                            </td>
                        </tr>
                        <tr>
                            <td>Gift Name</td>
                            <td>
                                {{ $redeemtion->catalogue->name ?? "" }}<br>
                            </td>
                        </tr>
                        <tr>
                            <td>Status</td>
                            <td>
                                <select name="status" id="status" class="form-control">
                                    <option value="">Select Status</option>
                                    <option value="0" @if (isset($redeemtion->status) && $redeemtion->status == "0") {{ "selected" }} @endif >Pending</option>
                                    <option value="1" @if (isset($redeemtion->status) && $redeemtion->status == "1") {{ "selected" }} @endif>Delivered</option>
                                    <option value="2" @if (isset($redeemtion->status) && $redeemtion->status == "2") {{ "selected" }} @endif>Rejected</option>
                                </select>
                                @error('status')
                                    <span class="text text-danger">{{ $message }}</span>
                                @enderror
                             </td>
                        </tr>
                        <tr>
                            <td>Delivery Date</td>
                            <td>
                                <input type="date" name="delivery_date" id="delivery_date" class="form-control" value="{{ $redeemtion->delivery_date ?? "" }}">
                                @error('date')
                                    <span class="text text-danger">{{ $message }}</span>
                                @enderror
                             </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>
                                 <button type="submit" class="btn btn-success">Save</button>
                             </td>
                        </tr>
                    </form>
                    </table>
                </div>

            </div>

            <div class="card-footer">
               
                <a href="{{ route('redeemtions.index') }}" class="btn btn-default">Cancel</a>
            </div>

          

        </div>
    </div>
@endsection

