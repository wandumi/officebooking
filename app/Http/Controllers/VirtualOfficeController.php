<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Location;
use Illuminate\Http\Request;
use App\Models\OfficePricing;
use App\Models\VirtualOffice;

class VirtualOfficeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $virtualOffices = VirtualOffice::when($search, function ($query, $search) {
                $query->where('virtualoffice_name', 'like', "%{$search}%");
            })
            ->with('location')
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString(); 

        return Inertia::render('VirtualOffices/AdminVirtualOffices', [
            'virtualoffices' => $virtualOffices,
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
        $pricing   = OfficePricing::select('category_name', 'pricing_type','rate')
                     ->where('category_name', 'Virtual Office')->get();

        return Inertia::render('VirtualOffices/CreateVirtualOffice',[
            'locations'=> $locations,
            'pricings' => $pricing,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'virtualoffice_name'        => 'required|string|max:255',
            'location_id'               => 'required|numeric',
            'address'                   => 'required|string|max:255',
            'discount'                  => 'required|numeric',
            'handling'                  => 'string|max:255',
            'price'                     => 'required|numeric',
            'phone_number'              => ['required', 'string', 'regex:/^\+?[1-9]\d{1,14}$/'], 
            'duration'                  => 'required|numeric',
            'pricing_type'        => [
                'required',
                'array',
                'min:2',
                function ($attribute, $value, $fail) {
                    if (!is_array($value) || !isset($value[0]) || !isset($value[1]) || is_null($value[0]) || is_null($value[1])) {
                        $fail('Both pricing fields are required for Dedicated Desk.');
                    }
                },
            ],
            'pricing_type.*'      => ['required', 'numeric'],
        ]);


        $price_premium = isset($validated['pricing_type'][0]) ? $validated['pricing_type'][0] : null;
        $price_standard = isset($validated['pricing_type'][1]) ? $validated['pricing_type'][1] : null;

        $virtualoffice = VirtualOffice::create([
            'virtualoffice_name'        => $validated['virtualoffice_name'],
            'location_id'               => $validated['location_id'],
            'address'                   => $validated['address'],
            'discount'                  => $validated['discount'],
            'handling'                  => $validated['handling'],
            'price'                     => $validated['price'],
            'phone_number'              => $validated['phone_number'], 
            'duration'                  => $validated['duration'],
            'price_premium'             => $price_premium,
            'price_standard'            => $price_standard
        ]);

        return redirect()->route('admin.virtual-offices')->with('success', 'A Virtual Office has been created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(VirtualOffice $virtualOffice)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(VirtualOffice $virtualoffice)
    {
        $locations = Location::select('id', 'name')->get();
        $pricings = OfficePricing::select('id','category_name', 'pricing_type','rate')
                 ->where('category_name', 'Virtual Office')->get();

                

        return Inertia::render('VirtualOffices/EditVirtualOffice', [
            'virtualoffices'    => $virtualoffice->load(['location']),
            'locations'         => $locations,
            'pricings'          => $pricings
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, VirtualOffice $virtualoffice)
    {
        $validated = $request->validate([
            'virtualoffice_name'  => 'required|string|max:255',
            'location_id'         => 'required|numeric',
            'address'             => 'required|string|max:255',
            'discount'            => 'required|numeric',
            'handling'            => 'string|max:255',
            'price'               => 'required|numeric',
            'phone_number'        => ['required', 'string', 'regex:/^\+?[1-9]\d{1,14}$/'],
            'duration'            => 'required|numeric',
            'pricing_type'        => [
                'required',
                'array',
                'min:2',
                function ($attribute, $value, $fail) {
                    if (!is_array($value) || !isset($value[0]) || !isset($value[1]) || is_null($value[0]) || is_null($value[1])) {
                        $fail('Both pricing fields are required for Dedicated Desk.');
                    }
                },
            ],
            'pricing_type.*'      => ['required', 'numeric'],
        ]);


        $price_premium = isset($validated['pricing_type'][0]) ? $validated['pricing_type'][0] : null;
        $price_standard = isset($validated['pricing_type'][1]) ? $validated['pricing_type'][1] : null;

        $virtualoffice->update([
            'virtualoffice_name'        => $validated['virtualoffice_name'],
            'location_id'               => $validated['location_id'],
            'address'                   => $validated['address'],
            'discount'                  => $validated['discount'],
            'handling'                  => $validated['handling'],
            'price'                     => $validated['price'],
            'phone_number'              => $validated['phone_number'], 
            'duration'                  => $validated['duration'],
            'price_premium'             => $price_premium,
            'price_standard'            => $price_standard
        ]);



        return redirect()->route('admin.virtual-offices')->with('success', 'Virtual Office updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(VirtualOffice $virtualOffice)
    {
        $virtualOffice->delete();

        return redirect()->route('admin.virtual-offices')->with('success', 'A Virtual Office has been deleted successfully.');
    }
}
