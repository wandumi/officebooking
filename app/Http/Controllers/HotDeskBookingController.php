<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Office;
use App\Models\HelpDesk;
use App\Models\Location;
use Illuminate\Http\Request;
use App\Models\HotDeskBooking;

class HotDeskBookingController extends Controller
{
    /**
     * Display a office of the resource.
    */
    public function viewHotDesk(HelpDesk $hotDesk)
    {
        $locations = Location::select('id', 'name')->get();

        return Inertia::render('Bookings/HotDesks/EditHotDesk',[
             'helpDesks' => $hotDesk->load(['location']),
             'locations' => $locations,
        ]);

    }

    /**
     * Store hotdesks offices the resource.
     */
    public function store(Request $request)
    {

   
        $validated = $request->validate([
            'hotdesk_id'     => 'required|exists:help_desks,id',
            'plan'            => 'required',
            'is_half_day'     => 'required|boolean',
            'selected_dates'  => 'required|array|min:1',
            'selected_dates.*'=> 'date|after_or_equal:today',
            'time_slots'      => 'nullable|array',
            'time_slots.*.block' => 'nullable|string|in:morning,afternoon',
            'days_count'      => 'required|integer|min:1',
            'selected_price'  => 'required|numeric|min:1',
        ]);

        $office = Office::findOrFail($validated['hotdesk_id']);

        $conflict = HotDeskBooking::where('user_id', auth()->id())
                    ->where('helpdesk_id', $validated['hotdesk_id'])
                    ->where('plan', $validated['plan'])
                    ->whereIn('status', ['pending', 'approved']) 
                    ->where(function ($query) use ($validated) {
                        foreach ($validated['selected_dates'] as $date) {
                            $query->orWhereJsonContains('selected_dates', $date);
                        }
                    })
                    ->exists();


        if ($conflict) {
            return back()->withErrors([
                'booking_conflict' => 'You already have a booking with this plan type that overlaps the selected dates.',
            ])->withInput();
        }

        $hotDesk = HotDeskBooking::create([
            'user_id'         => auth()->id(),
            'helpdesk_id'    => $validated['hotdesk_id'], 
            'plan'            => $validated['plan'],
            'is_half_day'     => $validated['is_half_day'],
            'selected_dates'  => $validated['selected_dates'], 
            'time_slots'      => $validated['time_slots'] ?? null, 
            'days_count'      => $validated['days_count'],
            'selected_price'  => $validated['selected_price'],
            'status'          => 'pending',
        ]);


        return back()->with('success', 'Booking created successfully!');
    }

    /**
     * View the booked HotDesks.
     */
    public function show(Request $request)
    {


        $user = auth()->user();

        if ($user->hasRole('admin') || $user->hasRole('super admin')) {
           
             $bookings = HotDeskBooking::with(['user', 'helpdesk']) 
                        ->latest()
                        ->paginate(10);
     
        } else {
            $bookings = HotDeskBooking::with('helpDesk.location')
                ->where('user_id', $user->id)
                ->latest()
                ->paginate(10);
        }


        return Inertia::render('Bookings/HotDesks/ShowHotDesks', [
            'bookings' => $bookings,
        ]);
    }
}
