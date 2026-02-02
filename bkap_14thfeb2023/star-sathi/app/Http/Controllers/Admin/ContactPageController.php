<?php

namespace App\Http\Controllers\Admin;

use Laracasts\Flash\Flash;
use App\Models\ContactPage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Contact\UpdateContactRequest;

class ContactPageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
         $contacts = ContactPage::all();
      
         return view('admin.contact.index')->with('contacts', $contacts);
   
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
       // return view('admin.contact.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
       
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $contactPage = ContactPage::find($id);

        if (empty($contactPage)) {
            Flash::error('Contact not found');

            return redirect(route('contacts.index'));
        }

        return view('admin.contact.edit')->with('contactPage', $contactPage);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateContactRequest $request, $id)
    {
        $contactPage = ContactPage::find($id);

        if (empty($contactPage)) {
            Flash::error('Page not found');

            return redirect(route('contacts.index'));
        }

        $contactPage =  $contactPage->update($request->all());

        Flash::success('Page updated successfully.');

        return redirect(route('contacts.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

    }
}
