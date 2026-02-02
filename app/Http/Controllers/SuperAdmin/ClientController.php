<?php

namespace App\Http\Controllers\SuperAdmin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Client;
class ClientController extends Controller
{
    function clients()
    {
        $clients = Client::get();
        return view('superadmin.clients')->with(['clients' => $clients]);
    }
    function addClient()
    {
        $client_id ='';
        return view('superadmin.add-client')->with(['client_id' => $client_id]);
    }
    function updateClient($id)
    {
        $client = Client::where('id',$id)->first();
        $client_id = $id;
        return view('superadmin.add-client')->with(['client_id' => $client_id,'client'=>$client]);
    }
    
    function saveClient(Request $req)
    {
        $req->validate([
            'name' => 'required',
            'about' => 'required',
            'say' => 'required',
        ]);

        if($req->client_id)
        {
            $client = Client::where('id', $req->client_id)->first();
            $msg = "Updated";

        }else{
            $client = new Client;
            $msg = "Created";
        }  
            
            $client->name = $req->name;
            $client->about = $req->about;
            $client->say = $req->say;
            $client->save();
            
            if($req->hasFile('image'))
            {
                $name = "c".$client->id.".jpg";
                $path = public_path('/img/client');
                $req->file('image')->move($path, $name);
                $client->img = $name;
                $client->save();
            }
            return back()->with('success','<strong>Success!</strong> '.$msg.' Successfully'); 
    }
    function deleteClient(Request $req){
    	$qry = Client::where('id', $req->id)->first(); 
    	$qry->delete();
    	return back()->with('success','<strong>Success!</strong> Client Deleted Successfully.');
    }
}
