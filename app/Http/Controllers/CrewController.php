<?php

namespace App\Http\Controllers;

use App\Models\Crew;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;



class CrewController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Crews/Index', [
            'search'    => request()->input('search'),
            'crews'     => Crew::when(request()->input('search.name'), function ($query, $name) {
                                $query->where('name', 'like', '%'.$name.'%');
                            })
                            ->when(request()->input('search.role'), function ($query, $role) {
                                $query->where('role', 'like', '%'.$role.'%');
                            })
                            ->when(request()->input('search.vessel'), function ($query, $vessel) {
                                $query->where('vessel', 'like', '%'.$vessel.'%');
                            })
                            ->with(['user:id,name', 'contracts'])->latest()->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {   
        return Inertia::render('Crews/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'ktp' => 'required|unique:crews|max:255',
            'name' => 'required|max:255',
            'birthplace' => 'required|max:255',
            'role' => 'required|max:255',
            'vessel' => 'required|max:255',
            'birthdate' => 'required|date',
            'religion' => 'required|max:255',
            'marital_status' => 'required|max:255',
            'blood_type' => 'required|max:255',
            'address' => 'required|max:255',
            'phone' => 'required|max:255',
            'emergency_contact' => 'required|max:255',
            'email' => 'required|max:255',
            'passport' => 'required|max:255',
            'seaman_book' => 'required|max:255',
            'kk' => 'required|max:255',
            'npwp' => 'required|max:255',
            'bpjs_tk' => 'required|max:255',
            'bpjs_kes' => 'required|max:255',
            'bank' => 'required|max:255',
            'bank_account_number' => 'required|max:255',
            'bank_account_owner' => 'required|max:255',
        ]);
 
        $request->user()->crews()->create($validated);
 
        Session::flash('alert-class', 'success'); 
        Session::flash('message', 'Crew created successfully.'); 

        return redirect(route('crews.index'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Crew $crew)
    {
        return Inertia::render('Crews/Show', [
            'crew' => Crew::with(['user:id,name', 'documents', 'contracts'])->find($crew->id),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Crew $crew)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Crew $crew)
    {
        $validated = $request->validate([
            'ktp' => [
                'required',
                Rule::unique('crews')->ignore($crew->id, 'id')
            ],
            'name' => 'required|max:255',
            'role' => 'required|max:255',
            'vessel' => 'required|max:255',
            'birthplace' => 'required|max:255',
            'birthdate' => 'required|date',
            'religion' => 'required|max:255',
            'marital_status' => 'required|max:255',
            'blood_type' => 'required|max:255',
            'address' => 'required|max:255',
            'phone' => 'required|max:255',
            'emergency_contact' => 'required|max:255',
            'email' => 'required|max:255',
            'passport' => 'required|max:255',
            'seaman_book' => 'required|max:255',
            'kk' => 'required|max:255',
            'npwp' => 'required|max:255',
            'bpjs_tk' => 'required|max:255',
            'bpjs_kes' => 'required|max:255',
            'bank' => 'required|max:255',
            'bank_account_number' => 'required|max:255',
            'bank_account_owner' => 'required|max:255',
        ]);
 
        $crew->update($validated);

        Session::flash('alert-class', 'success'); 
        Session::flash('message', 'Crew updated successfully.'); 
 
        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Crew $crew)
    {
        
        foreach($crew->documents() as $document) {
            dd($document->url);
            if(File::exists($document->url)) {
                File::delete($document->url);
            }
            $document->delete();
        }
        $crew->delete();

        Session::flash('alert-class', 'success'); 
        Session::flash('message', 'Crew deleted successfully.'); 

        return redirect(route('crews.index'));
    }

    /**
     * Download input-multiple-crew-template.xlsx
     */
    public function download_template()
    {
        //
    }
}
