<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use App\Models\Office;
use App\Models\HelpDesk;
use App\Models\Location;
use Illuminate\Http\Request;
use App\Models\HotDeskBooking;
use App\Notifications\HotDeskBookingNotification;

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
    
        // dd($request);
        $validated = $request->validate([
            'hotdesk_id'        => 'required|exists:help_desks,id',
            'plan'              => 'required',
            'is_half_day'       => 'required|boolean',
            'selected_dates'    => 'required|array|min:1',
            'selected_dates.*'  => 'date|after_or_equal:today',
            'time_slots'        => 'nullable|array',
            'days_count'        => 'required|integer|min:1',
            'selected_price'    => 'required|numeric|min:1',
        ]);

        // Manually validate the nested time_slots structure
        foreach ($validated['time_slots'] ?? [] as $date => $slot) {
            if (!is_array($slot) || !in_array($slot['block'] ?? null, ['morning', 'afternoon'])) {
                return back()->withErrors([
                    'time_slots' => "Invalid block for date $date. Must be 'morning' or 'afternoon'."
                ])->withInput();
            }
        }

        // Check for existing overlapping booking
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

        if(!empty($validated['time_slots'])){
            $half_day = $validated['is_half_day'] = 1;
            $validated['selected_dates'] = [];
        }

       
        //Store booking
        $hotDesk = HotDeskBooking::create([
            'user_id'         => auth()->id(),
            'helpdesk_id'     => $validated['hotdesk_id'],
            'plan'            => $validated['plan'],
            'is_half_day'     => $half_day ?? 0,
            'selected_dates'  => $validated['selected_dates'],
            'time_slots'      => $validated['time_slots'] ?? null,
            'days_count'      => $validated['days_count'],
            'selected_price'  => $validated['selected_price'],
            'status'          => 'pending',
        ]);
        
        $office = HelpDesk::findOrFail($validated['hotdesk_id']);

        // nortifications
        $bookingData = [
            'id' => $office->id,
            'room_type' => $office->virtualoffice_name,
            'status' => 'created',
            'user_name' => auth()->user()->name,
        ];

        auth()->user()->notify(new HotDeskBookingNotification($bookingData, 'created', 'user'));

        $admins = User::withRole('Admin')
            ->get()
            ->merge(User::withRole('Super Admin')->get())
            ->unique('id');


        $admins->each(fn ($user) => $user->notify(new HotDeskBookingNotification($bookingData, 'created', 'admin')));
    
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
            $bookings = HotDeskBooking::with(['user', 'helpdesk'])
                ->where('user_id', $user->id)
                ->latest()
                ->paginate(10);
        }


        return Inertia::render('Bookings/HotDesks/ShowHotDesks', [
            'bookings' => $bookings,
        ]);
    }

     /**
     * Remove the resource from storage.
     */
    public function destroy(HotDeskBooking $hotdesk)
    {

        $hotdesk->delete();

        return back()->with('success', 'A Hot Desks booking has been deleted successfully.');
    }

    /**
     * Deleted office of office.
    */
    public function deleted(Request $request)
    {
        $search = $request->input('search');

        $user = auth()->user();

        if ($user->hasRole('admin') || $user->hasRole('super admin')) {
           
             $bookings = HotDeskBooking::with(['user', 'helpdesk'])
                        ->onlyTrashed() 
                        ->latest()
                        ->paginate(10);

                        //  dd($bookings);
     
        } 

        return Inertia::render('Bookings/HotDesks/DeletedHotDesks',[
            'bookings' => $bookings,
            'filters' => [
                'search' => $search,
            ]
        ]);

    }

    /**
     * Restore a deleted office.
    */
    public function restore($id)
    {
        $booking = HotDeskBooking::onlyTrashed()->findOrFail($id);
      
        $booking->restore();

        return redirect()->to('/hotdesk-booking')->with('success', 'Booking has been restored successfully.');
    }


    /**
     * Approve function
     *
     * @param VirtualBooking $virtual
     * @return void
     */
    public function approve(HotDeskBooking $hotdesk)
    {
        $hotdesk->update([
            'status' => 'approved',
        ]);

        $office = HelpDesk::findOrFail($hotdesk->helpdesk_id);
    
        // nortifications
        $bookingData = [
            'id' => $office->id,
            'room_type' => $office->virtualoffice_name,
            'status' => 'approved',
            'user_name' => auth()->user()->name,
        ];

        $hotdesk->user->notify(new HotDeskBookingNotification($bookingData, 'approved', 'user'));

        $admins = User::withRole('Admin')
            ->get()
            ->merge(User::withRole('Super Admin')->get())
            ->unique('id');


        $admins->each(fn ($user) => $user->notify(new HotDeskBookingNotification($bookingData, 'approved', 'admin')));

              
        return back()->with('success', 'Booking approved successfully.');
    }

    public function reject(Request $request, HotDeskBooking $hotdesk)
    {
        

        $hotdesk->update([
            'status' => 'rejected',
        ]);

        $office = HelpDesk::findOrFail($hotdesk->helpdesk_id);

        // nortifications
        $bookingData = [
            'id' => $office->id,
            'room_type' => $office->virtualoffice_name,
            'status' => 'rejected',
            'user_name' => auth()->user()->name,
        ];

        $hotdesk->user->notify(new HotDeskBookingNotification($bookingData, 'rejected', 'user'));

        $admins = User::withRole('Admin')
            ->get()
            ->merge(User::withRole('Super Admin')->get())
            ->unique('id');


        $admins->each(fn ($user) => $user->notify(new HotDeskBookingNotification($bookingData, 'rejected', 'admin')));

              
        return back()->with('success', 'Booking rejected.');
    }

    public function cancel(Request $request, HotDeskBooking $hotdesk)
    {

        $hotdesk->update([
            'status' => 'cancelled',
        ]);

        $office = HelpDesk::findOrFail($hotdesk->helpdesk_id);

        // nortifications
        $bookingData = [
            'id' => $office->id,
            'room_type' => $office->virtualoffice_name,
            'status' => 'cancelled',
            'user_name' => auth()->user()->name,
        ];

        $hotdesk->user->notify(new HotDeskBookingNotification($bookingData, 'cancelled', 'user'));

        $admins = User::withRole('Admin')
            ->get()
            ->merge(User::withRole('Super Admin')->get())
            ->unique('id');


        $admins->each(fn ($user) => $user->notify(new HotDeskBookingNotification($bookingData, 'cancelled', 'admin')));

        return back()->with('success', 'Booking cancelled.');
    }


}
