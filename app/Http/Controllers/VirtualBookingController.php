<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use Inertia\Inertia;
use App\Models\Location;
use Illuminate\Http\Request;
use App\Models\OfficePricing;
use App\Models\VirtualOffice;
use App\Models\VirtualBooking;
use App\Notifications\VirtualBookingNotification;


class VirtualBookingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $virtualOffices = VirtualOffice::with('location')
                         ->select('id','location_id', 'virtualoffice_name', 'address', 'discount', 
                            'price', 'price_premium',
                            'price_standard')
                        ->get();
        

        $locations = Location::select('name', 'address', 'city')->get();

        
        return Inertia::render('Bookings/Virtual/IndexVirtual', [
            'virtualOffices'    => $virtualOffices,
            'locations'         => $locations,
        ]);
        
    }

    /**
     * Display a office of the resource.
     */
    public function view(Request $request, VirtualOffice $virtual)
    {
        
        $locations = Location::select('id', 'name')->get();

        $pricings = OfficePricing::select('id', 'category_name', 'pricing_type', 'rate')
            ->where('category_name', 'Virtual Office')
            ->get();

        $bookedRanges = VirtualBooking::where('virtual_office_id', $virtual->id)
                        ->where('user_id', auth()->id())
                        ->get(['plan', 'selected_dates']); 
                        
            
        return Inertia::render('Bookings/Virtual/EditVirtual', [
            'virtual'       => $virtual->load(['location']),
            'locations'     => $locations,
            'pricings'      => $pricings,
            'bookedRange'   => $bookedRanges, 
        ]);
    

    }

    /**
     * Work on store.
     */
    public function store(Request $request)
    {
        
    
        $validated = $request->validate([
            'virtual_office_id' => 'required|exists:virtual_offices,id',
            'plan'              => 'required|string|max:255',
            'start_date'        => 'required|date|after_or_equal:today',
            'end_date'          => 'nullable|date|after:start_date',
            'months'            => 'required|integer|min:3|max:12',
            'selected_price'    => 'required|numeric|min:0',
            'selected_dates'    => 'nullable|array',
            'selected_dates.*'  => 'date',
        ]);

        // Convert dates to Carbon
        $startDate = Carbon::parse($validated['start_date'])->toDateString();
        $endDate = Carbon::parse($validated['end_date'])->toDateString();

        $hasOverlap = VirtualBooking::where('user_id', $request->user()->id)
            ->where('virtual_office_id', $validated['virtual_office_id'])
            ->where('plan', $validated['plan'])
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('start_date', [$startDate, $endDate])
                    ->orWhereBetween('end_date', [$startDate, $endDate])
                    ->orWhere(function ($q) use ($startDate, $endDate) {
                        $q->where('start_date', '<=', $startDate)
                        ->where('end_date', '>=', $endDate);
                    });
            })
            ->exists();

        if ($hasOverlap) {
            return redirect()->back()
                ->withErrors([
                    'booking_conflict' => 'You’ve already booked this office with that plan in the selected date range.',
                ])
                ->withInput();
        }

        $office = VirtualOffice::findOrFail($validated['virtual_office_id']);

        $booking = VirtualBooking::create([
            'user_id'           => $request->user()->id,
            'virtual_office_id' => $validated['virtual_office_id'],
            'plan'              => $validated['plan'],
            'start_date'        => $startDate,
            'end_date'          => $endDate,
            'months'            => $validated['months'],
            'selected_price'    => $validated['selected_price'],
            'selected_dates'    => $validated['selected_dates'],
        ]);

        // nortifications
        $bookingData = [
            'id' => $office->id,
            'room_type' => $office->virtualoffice_name, 
            'status' => 'pending',
        ];

        auth()->user()->notify(new VirtualBookingNotification($bookingData, 'created'));

        User::withRole('super_admin')->get()
            ->each(fn ($user) => $user->notify(new VirtualBookingNotification($bookingData, 'created')));

        User::withRole('admin')->get()
            ->each(fn ($user) => $user->notify(new VirtualBookingNotification($bookingData, 'created')));


        return redirect()->route('booking.virtual', ['virtual' => $validated['virtual_office_id']])
            ->with('success', 'Virtual office booked successfully!');

    }

    /**
     * View the booked virtuals.
     */
    public function show(Request $request)
    {

        $user = auth()->user();

        if ($user->hasRole('admin') || $user->hasRole('super admin')) {
             $bookings = VirtualBooking::with(['user', 'virtualOffice']) 
                        ->latest()
                        ->paginate(10);

        } else {
            $bookings = VirtualBooking::with('virtualOffice.location')
                ->where('user_id', $user->id)
                ->latest()
                ->paginate(10);
        }


        return Inertia::render('Bookings/Virtual/ShowVirtual', [
            'bookings' => $bookings,
        ]);
    }

    /**
     * Approve function
     *
     * @param VirtualBooking $virtual
     * @return void
     */
    public function approve(VirtualBooking $virtual)
    {
        $virtual->update([
            'status' => 'approved',
            
        ]);

        $office = VirtualOffice::findOrFail($virtual->virtual_office_id);
    
        // nortifications
        $bookingData = [
            'id' => $virtual->virtual_office_id,
            'room_type' => $office->virtualoffice_name, 
            'status' => 'approved',
        ];

        auth()->user()->notify(new VirtualBookingNotification($bookingData, 'approved'));

        User::withRole('super_admin')->get()
            ->each(fn ($user) => $user->notify(new VirtualBookingNotification($bookingData, 'approved')));

        User::withRole('admin')->get()
            ->each(fn ($user) => $user->notify(new VirtualBookingNotification($bookingData, 'approved')));


        return back()->with('success', 'Booking approved successfully.');
    }

    public function reject(Request $request, VirtualBooking $virtual)
    {
        $virtual->update([
            'status' => 'rejected',
        ]);

        $office = VirtualOffice::findOrFail($virtual->virtual_office_id);
    
        // nortifications
        $bookingData = [
            'id' => $virtual->virtual_office_id,
            'room_type' => $office->virtualoffice_name, 
            'status' => 'rejected',
        ];

        auth()->user()->notify(new VirtualBookingNotification($bookingData, 'rejected'));

        User::withRole('super_admin')->get()
            ->each(fn ($user) => $user->notify(new VirtualBookingNotification($bookingData, 'rejected')));

        User::withRole('admin')->get()
            ->each(fn ($user) => $user->notify(new VirtualBookingNotification($bookingData, 'rejected')));

        return back()->with('success', 'Booking rejected.');
    }

    public function cancel(Request $request, VirtualBooking $virtual)
    {
        $virtual->update([
            'status' => 'cancelled',
        ]);

        $office = VirtualOffice::findOrFail($virtual->virtual_office_id);
    
        // nortifications
        $bookingData = [
            'id' => $virtual->virtual_office_id,
            'room_type' => $office->virtualoffice_name, 
            'status' => 'cancel',
        ];

        auth()->user()->notify(new VirtualBookingNotification($bookingData, 'cancelled'));

        User::withRole('super_admin')->get()
            ->each(fn ($user) => $user->notify(new VirtualBookingNotification($bookingData, 'cancelled')));

        User::withRole('admin')->get()
            ->each(fn ($user) => $user->notify(new VirtualBookingNotification($bookingData, 'cancelled')));

        return back()->with('success', 'Booking cancelled.');
    }
}
