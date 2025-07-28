<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\HelpDesk;
use App\Models\Location;
use Illuminate\Http\Request;

class HelpDeskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $helpDesks = HelpDesk::when($search, function ($query, $search) {
                $query->where('help_desk_name', 'like', "%{$search}%");
            })
            ->with('location')
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString(); 
            
        
        return Inertia::render('HelpDesk/AdminHelpDesk', [
            'helpDesks' => $helpDesks,
            'filters' => [
                'search' => $search,
            ]
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $locations = Location::select('id', 'name')->get();

        return Inertia::render('HelpDesk/CreateHelpDesk', [
            'locations' => $locations
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
 
        $validated = $request->validate([
            'location_id'       => 'required',
            'help_desk_name'    => 'required|string|max:255',
            'price'             => 'required|numeric',
            'duration'          => 'required|numeric',
            'desks'             => 'nullable|numeric',
            'discount'          => 'required|numeric',
        ]);

        $HelpDesk = HelpDesk::create($validated);

        return redirect()->route('admin.help-desks')->with('success', 'Help Desks created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(HelpDesk $helpDesk)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(HelpDesk $helpDesk)
    {
        $locations = Location::select('id', 'name')->get();

        return Inertia::render('HelpDesk/EditHelpDesk',[
            'helpDesks' => $helpDesk,
            'locations' => $locations,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, HelpDesk $helpDesk)
    {

         $validated = $request->validate([
            'location_id'       => 'required',
            'help_desk_name'    => 'required|string|max:255',
            'price'             => 'required|numeric',
            'duration'          => 'required|numeric',
            'desks'             => 'nullable|numeric',
            'discount'          => 'required|numeric',
        ]);

        $helpDesk->update($validated);

        return redirect()->route('admin.help-desks')->with('success', 'Help Desks updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(HelpDesk $helpDesk)
    {
        $helpDesk->delete();

        return back()->with('success', 'Help Desk deleted successfully.');
    }
}
