<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Inertia\Inertia;
use App\Models\Booking;
use Illuminate\Http\Request;
use App\Models\VirtualOffice;
use App\Models\HotdeskBooking;
use App\Models\VirtualBooking;
use App\Models\BoardroomBooking;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class CalendarController extends Controller
{
    public function hotdesk(Request $request)
    {
    
        $user = auth()->user();

        $bookings = HotDeskBooking::with('user:id,name', 'helpdesk:id,help_desk_name')
            ->where('status', 'approved')
            ->get()
            ->flatMap(function ($hd) use ($user) {
                $dates = is_array($hd->selected_dates)
                    ? $hd->selected_dates
                    : json_decode($hd->selected_dates, true) ?? [];

                $isOwner = $user->id === $hd->user_id;
                $isAdmin = $user->is_admin;

                return collect($dates)->map(function ($date) use ($hd, $isOwner, $isAdmin) {
                    return [
                        'start'   => Carbon::parse($date)->format('Y-m-d'),
                        'end'     => Carbon::parse($date)->format('Y-m-d'),
                        'title'   => $isOwner || $isAdmin ? $hd->user->name : 'Booked',
                        'content' => $isOwner || $isAdmin ? ($hd->helpdesk->help_desk_name ?? 'Hotdesk Booking') : 'None',
                        'class'   => $hd->helpdesk_id ? 'hotdesk-' . $hd->helpdesk_id : 'default-hotdesk',
                    ];
                });
            });

        $halfDayBookings = HotDeskBooking::with('user:id,name', 'helpdesk:id,help_desk_name')
            ->where('status', 'approved')
            ->where('is_half_day', true)
            ->get()
            ->flatMap(function ($hd) use ($user) {
                $isOwner = $user->id === $hd->user_id;
                $isAdmin = $user->is_admin;

                return collect($hd->time_slots)
                    ->map(function ($slot, $date) use ($hd, $isOwner, $isAdmin) {
                        $block = $slot['block'] ?? null;

                        $start = match ($block) {
                            'morning'   => $date . ' 08:00:00',
                            'afternoon' => $date . ' 13:00:00',
                            default     => $date . ' 09:00:00',
                        };
                        $end = match ($block) {
                            'morning'   => $date . ' 12:00:00',
                            'afternoon' => $date . ' 17:00:00',
                            default     => $date . ' 17:00:00',
                        };

                        return [
                            'start'   => Carbon::parse($start)->format('Y-m-d H:i:s'),
                            'end'     => Carbon::parse($end)->format('Y-m-d H:i:s'),
                            'title'   => $isOwner || $isAdmin ? $hd->user->name : 'Booked',
                            'content' => $isOwner || $isAdmin ? ($hd->helpdesk->help_desk_name ?? 'Hotdesk Booking') : 'None',
                            'class'   => 'hotdesk-' . ($hd->helpdesk_id ?? 'default'),
                        ];
                    })->values();
            });

        $events = collect()
            ->merge($bookings)
            ->merge($halfDayBookings)
            ->sortBy('start')
            ->values();

        return Inertia::render('Calendars/HotDeskCalendar', [
            'events' => $events,
        ]);

    }

    public function boardroom(Request $request)
    {
    
        $boardrooms = BoardroomBooking::with('user:id,name', 'boardroom:id,boardroom_name')
                    ->where('status', 'approved')
                    ->get()
                    ->flatMap(function ($booking) {
                        $times = is_array($booking->selected_times)
                            ? $booking->selected_times
                            : json_decode($booking->selected_times, true);

                        return collect($times)->flatMap(function ($slots, $date) use ($booking) {
                            return collect($slots)->map(function ($time) use ($date, $booking) {
                                $start = Carbon::parse("{$date} {$time}:00");
                                $end = $start->copy()->addHour(); // Assuming each slot is 1 hour long

                                return [
                                    'start'   => $start->format('Y-m-d H:i:s'),
                                    'end'     => $end->format('Y-m-d H:i:s'),
                                    'title'   => "{$booking->user->name} ",
                                    'content' => $booking->boardroom->boardroom_name ?? 'Boardroom Booking',
                                    'class'   => 'boardroom-' . ($booking->boardroom_id ?? 'default'),
                                ];
                            });
                        });
                    });
          

        $events = collect()
            ->merge($boardrooms)
            ->sortBy('start')
            ->values();

        return Inertia::render('Calendars/BoardroomsCalendar', [
            'events' => $events,
        ]);

    }

    public function dedicated(Request $request)
    {

        $offices = Booking::with('user:id,name', 'office:id,office_name')
                    ->where('status', 'approved')
                    ->get()
                    ->map(function ($booking) {
                        return [
                            'start'   => $booking->start_date,
                            'end'     => $booking->end_date,
                            'title'   => $booking->user->name,
                            'content' => $booking->office->office_name,
                            'class'   => $booking->office_id ? 'office-' . $booking->office_id : 'default-office',
                        ];
                    });
        
        
        $events = collect()
        ->merge($offices)
        ->sortBy('start')
        ->values();

        return Inertia::render('Calendars/DedicatedCalendar', [
            'events' => $events,
        ]);

    }

    public function closed(Request $request)
    {
    
       $offices = Booking::with('user:id,name', 'office:id,office_name')
        ->where('status', 'approved')
        ->get()
        ->map(function ($booking) {
            return [
                'start'   => $booking->start_date,
                'end'     => $booking->end_date,
                'title'   => $booking->user->name,
                'content' => $booking->office->office_name,
                'class'   => $booking->office_id ? 'office-' . $booking->office_id : 'default-office',
            ];
        });

        $events = collect()
            ->merge($offices)
            ->sortBy('start')
            ->values();

        return Inertia::render('Calendars/ClosedCalendar', [
            'events' => $events,
        ]);

    }

    public function virtual(Request $request)
    {

        $virtuals = VirtualBooking::with('user:id,name', 'virtualOffice:id,virtualoffice_name')
                ->where('status', 'approved')
                ->get()
                ->flatMap(function ($vb) {
                    $dates = is_array($vb->selected_dates)
                        ? $vb->selected_dates
                        : json_decode($vb->selected_dates, true);

                    return collect($dates)->map(function ($date) use ($vb) {
                        return [
                            'start'   => Carbon::parse($date)->format('Y-m-d'),
                            'end'     => Carbon::parse($date)->format('Y-m-d'),
                            'title'   => "{$vb->user->name} ",
                            'content' => $vb->virtualOffice->office_name ?? 'Virtual Booking',
                            'class'   => 'virtual-' . ($vb->virtual_office_id ?? 'default'),
                        ];
                    });
                });

        $events = collect()
            ->merge($virtuals)
            ->sortBy('start')
            ->values();

        return Inertia::render('Calendars/VirtualCalendar', [
            'events' => $events,
        ]);

    }


}