<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Inertia\Inertia;
use App\Models\Office;
use App\Models\Amenity;
use App\Models\Booking;
use App\Models\Category;
use App\Models\HelpDesk;
use App\Models\Location;
use App\Models\Boardroom;
use Illuminate\Http\Request;
use App\Models\OfficePricing;
use App\Models\VirtualOffice;

class BookingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $offices = Office::with([
                        'location:id,name',    
                        'category:id,name',  
                        'pricing:id,office_id,premium_price,standard_price' 
                    ])->get();
        
        $helpDesks = HelpDesk::with('location')->get();


        $locations = Location::select('name', 'address', 'city')->get();
        
        return Inertia::render('Bookings/offices/IndexBookings', [
            'offices'           => $offices,
            'hotDesks'          => $helpDesks,
            'locations'         => $locations,
        ]);
        
    }

     /**
     * Display a listing of the resource.
     */
    public function extrasIndex()
    {

        $amenities = Amenity::select('id', 'amenity_name', 'description', 'price')->get();
        
        return Inertia::render('Bookings/offices/IndexExtras', [
            'amenities' => $amenities
        ]);
        
    }

    /**
     * Display a office of the resource.
     */
    public function viewOffice(Office $bookingoffice)
    {
        $locations = Location::select('id', 'name')->get();

        $pricings = OfficePricing::select('id', 'category_name', 'pricing_type', 'rate')
            ->where('category_name', 'Dedicated Desk')
            ->get();

        $amenities = Amenity::select('id', 'amenity_name')->get();

        $categories = Category::select('id', 'name')
            ->where('name', '!=', 'Virtual Office')
            ->get();

        $rawDates = Booking::where('user_id', auth()->id())
            ->where('office_id', optional($bookingoffice)->id)
            ->pluck('selected_dates');

        $selectedDates = $rawDates
            ->map(function ($item) {
                if (is_string($item)) {
                    $decoded = json_decode($item, true);
                    return is_array($decoded) ? $decoded : [];
                }
                return is_array($item) ? $item : [];
            })
            ->flatten()
            ->filter()
            ->unique()
            ->values()
            ->toArray();

        $rangeDates = Booking::where('user_id', auth()->id())
            ->where('office_id', optional($bookingoffice)->id)
            ->whereNotNull('start_date')
            ->whereNotNull('end_date')
            ->get()
            ->flatMap(function ($booking) {
                $start = Carbon::parse($booking->start_date);
                $end = Carbon::parse($booking->end_date);
                $dates = [];
                while ($start->lte($end)) {
                    $dates[] = $start->toDateString();
                    $start->addDay();
                }
                return $dates;
            })
            ->unique()
            ->toArray();

        $allBookedDates = collect([...$selectedDates, ...$rangeDates])
            ->unique()
            ->values();

        return Inertia::render('Bookings/offices/EditBooking', [
            'office' => $bookingoffice->load(['location', 'pricing', 'amenities']),
            'locations' => $locations,
            'pricings' => $pricings,
            'amenities' => $amenities,
            'categories' => $categories,
            'bookedDates' => $allBookedDates,
        ]);
    }

    
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
     * Store offices the resource.
     */
    public function storeOffice(Request $request)
    {
        $validated = $request->validate([
            'office_id'        => 'required|integer|exists:offices,id',
            'plan'             => 'required|string|in:daily,monthly,premium,standard',
            'selected_dates'   => 'nullable|array',
            'start_date'       => 'required|date',
            'end_date'         => 'required|date|after_or_equal:start_date',
            'months'           => 'nullable|integer|min:1',
            'weekdays_count'   => 'nullable|integer|min:0',
            'total_price'      => 'required|numeric',
            'category_id'      => 'nullable|numeric|exists:categories,id',
        ]);

        $office = Office::findOrFail($validated['office_id']);

        $start = Carbon::parse($validated['start_date'])->startOfDay();
        $end   = Carbon::parse($validated['end_date'])->endOfDay();

        // Check if this user already booked this plan on this office and there's overlap
        $conflict = Booking::where('user_id', auth()->id())
            ->where('office_id', $office->id)
            ->where('plan', $validated['plan']) // same plan only
            ->where(function ($query) use ($start, $end) {
                $query->whereBetween('start_date', [$start, $end])
                    ->orWhereBetween('end_date', [$start, $end])
                    ->orWhere(function ($q) use ($start, $end) {
                        $q->where('start_date', '<=', $start)
                        ->where('end_date', '>=', $end);
                    });
            })
            ->exists();

        if ($conflict) {
            return back()->withErrors([
                'booking_conflict' => 'You already have a booking with this plan type that overlaps the selected dates.',
            ])->withInput();
        }

        $booking = Booking::create([
            'user_id'         => auth()->id(),
            'office_id'       => $office->id,
            'category_id'     => $validated['category_id'] ?? null,
            'plan'            => $validated['plan'],
            'selected_dates'  => $validated['selected_dates'] ?? null,
            'weekdays_count'  => $validated['weekdays_count'] ?? null,
            'months'          => $validated['months'] ?? null,
            'start_date'      => $start->toDateString(),
            'end_date'        => $end->toDateString(),
            'total_price'     => $validated['total_price'],
            'status'          => 'pending',
        ]);

        return back()->with('success', 'Booking created successfully!');
    }


    public function showOffices(Request $request)
    {
        $user = auth()->user();

        if ($user->hasRole('admin') || $user->hasRole('super admin')) {
            $bookings = Booking::with(['user', 'office.location', 'office.category'])
                ->whereHas('office.category', function ($query) {
                    $query->where('name', 'Closed Office');
                })
                ->latest()
                ->paginate(10);
        } else {
            $bookings = Booking::with(['office.location', 'office.category'])
                ->whereHas('office.category', function ($query) {
                    $query->where('name', 'Closed Office');
                })
                ->where('user_id', $user->id)
                ->latest()
                ->paginate(10);
        }

        return Inertia::render('Bookings/offices/ShowOffices', [
            'bookings' => $bookings,
        ]);
    }

    public function showDedicated(Request $request)
    {
        $user = auth()->user();

        if ($user->hasRole('admin') || $user->hasRole('super admin')) {
            $bookings = Booking::with(['user', 'office.location', 'office.category'])
                ->whereHas('office.category', function ($query) {
                    $query->where('name', 'Dedicated Desk');
                })
                ->latest()
                ->paginate(10);
        } else {
            $bookings = Booking::with(['office.location', 'office.category'])
                ->whereHas('office.category', function ($query) {
                    $query->where('name', 'Dedicated Desk');
                })
                ->where('user_id', $user->id)
                ->latest()
                ->paginate(10);
        }


        return Inertia::render('Bookings/offices/ShowOffices', [
            'bookings' => $bookings,
        ]);
    }


    public function approve(Booking $booking)
    {
        // dd($booking);
        $booking->update([
            'status' => 'approved',
            
        ]);

        // Optional: Log action or notify user
        return back()->with('success', 'Booking approved successfully.');
    }

    public function reject(Request $request, Booking $booking)
    {
        $booking->update([
            'status' => 'rejected',
        ]);

        // Optional: store $request->note, trigger notification
        return back()->with('success', 'Booking rejected.');
    }

    public function cancel(Request $request, Booking $booking)
    {
        $booking->update([
            'status' => 'cancelled',
        ]);

        // Optional: store $request->note if super admin
        return back()->with('success', 'Booking cancelled.');
    }




      
}
