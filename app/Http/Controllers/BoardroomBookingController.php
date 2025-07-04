<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Amenity;
use App\Models\Location;
use App\Models\Boardroom;
use Illuminate\Http\Request;
use App\Models\BoardroomBooking;

class BoardroomBookingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $boardrooms = Boardroom::with('location','amenities')
                    ->select('id','boardroom_name', 'location_id','seats', 'hourly_price', 'daily_price')
                    ->get();
        

        $locations = Location::select('name', 'address', 'city')->get();
        
        return Inertia::render('Bookings/Boardrooms/IndexBoardrooms', [
            'boardrooms'        => $boardrooms,
            'locations'         => $locations,
        ]);
        
    }

    /**
     * Display a boardroom of the resource.
     */
    public function view(Boardroom $bookedboardroom)
    {
      
        // $this->authorize('update', $bookedboardroom); 
        $locations = Location::select('id', 'name')->get();
        $amenities = Amenity::select('id', 'amenity_name')->get();

        return Inertia::render('Bookings/Boardrooms/EditBoardroom', [
            'boardroom' => $bookedboardroom->load(['location', 'amenities']),
            'locations' => $locations,
            'amenities' => $amenities
        ]);
    }

         /**
     * Store offices the resource.
     */
    public function store(Request $request)
    {
      
         $validated = $request->validate([
            'boardroom_id'          => 'required|exists:boardrooms,id',
            'plan'                  => 'required|string|in:hourly,daily,monthly',

            'selected_dates' => 'required|array|min:1',
            'selected_dates.*' => 'date',

            'selected_times' => [
                'required_if:plan,hourly',
                'array',
            ],


            'months'                => 'required|integer|min:1',
            'selected_price'        => 'required|numeric|min:0',

        ]);

  
        $office = Boardroom::findOrFail($validated['boardroom_id']);

        $conflict = BoardroomBooking::where('user_id', auth()->id())
                ->where('boardroom_id', $validated['boardroom_id'])
                ->where('plan', $validated['plan'])
                ->where(function ($query) use ($validated) {
                    if ($validated['plan'] === 'daily') {
                        
                        foreach ($validated['selected_dates'] as $date) {
                            $query->orWhereJsonContains('selected_dates', $date);
                        }
                    }

                    if ($validated['plan'] === 'hourly') {
                        foreach ($validated['selected_dates'] as $date) {
                            $day = $date;
                            $timeSlots = $validated['selected_times'][$day] ?? [];

                            $query->orWhere(function ($q) use ($day, $timeSlots) {
                                $q->whereJsonContains('selected_dates', $day)
                                ->where(function ($timeCheck) use ($day, $timeSlots) {
                                    foreach ($timeSlots as $slot) {
                                        $timeCheck->orWhereJsonContains("selected_times->$day", $slot);
                                    }
                                });
                            });
                        }
                    }
                })
                ->exists();

            if ($conflict) {
                return back()->withErrors([
                    'booking_conflict' => 'There is already a booking for the selected date(s) and time(s).',
                ])->withInput();
            }

        $booking = BoardroomBooking::create([
            'user_id'         => auth()->id(),
            'boardroom_id'    => $office->id,
            'plan'            => $validated['plan'],
            'selected_dates'  => $validated['selected_dates'] ?? [],
            'selected_times'  => $validated['selected_times'] ?? null,
            'months'          => $validated['months'] ?? null,
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
           
             $bookings = BoardroomBooking::with(['user', 'boardroom']) 
                        ->latest()
                        ->paginate(10);
            // dd($bookings);
        } else {
            $bookings = BoardroomBooking::with('boardroom.location')
                ->where('user_id', $user->id)
                ->latest()
                ->paginate(10);
        }


        return Inertia::render('Bookings/Boardrooms/ShowHotDesks', [
            'bookings' => $bookings,
        ]);
    }

    public function approve(BoardroomBooking $booking)
    {
        $booking->update([
            'status' => 'approved',
            
        ]);

        return back()->with('success', 'Booking approved successfully.');
    }

    public function reject(Request $request, BoardroomBooking $booking)
    {
        $booking->update([
            'status' => 'rejected',
        ]);

        return back()->with('success', 'Booking rejected.');
    }

    public function cancel(Request $request, BoardroomBooking $booking)
    {
        $booking->update([
            'status' => 'cancelled',
        ]);

        return back()->with('success', 'Booking cancelled.');
    }

}
