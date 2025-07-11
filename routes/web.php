<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\AboutController;
use App\Http\Controllers\OfficeController;
use App\Http\Controllers\AmenityController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ManagerController;
use App\Http\Controllers\PricingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HelpDeskController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\BoardroomController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\UserAccessController;
use App\Http\Controllers\ClosedOfficeController;
use App\Http\Controllers\DedicatedDeskController;
use App\Http\Controllers\OfficePricingController;
use App\Http\Controllers\VirtualOfficeController;
use App\Http\Controllers\HotDeskBookingController;
use App\Http\Controllers\VirtualBookingController;
use App\Http\Controllers\BoardroomBookingController;

Route::get('/', function () {
//     return Inertia::render('Welcome');
     return redirect('/login');
});

Route::get('/clear-cache', function () {
    Artisan::call('cache:clear');
    Artisan::call('route:clear');
    Artisan::call('config:clear');
    Artisan::call('view:clear');
    
    return 'All caches cleared!';
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/booking-offices', [BookingController::class, 'index'])->name('booking.offices');
    Route::get('/bookings-show', [BookingController::class, 'showOffices'])->name('bookingoffices.show');
    Route::get('/booking-offices/{bookingoffice}', [BookingController::class, 'viewOffice'])->name('booking.officeview');
    Route::post('/booking-store', [BookingController::class, 'storeOffice'])->name('bookingoffice.store');
    Route::get('/booked-dedicated', [BookingController::class, 'showDedicated'])->name('bookingdedicated.show');

    Route::put('/booking-offices/{booking}/approve', [BookingController::class, 'approve'])->name('bookingoffice.approve');
    Route::put('/booking-offices/{booking}/reject', [BookingController::class, 'reject'])->name('bookingoffice.reject');
    Route::put('/booking-offices/{booking}/cancel', [BookingController::class, 'cancel'])->name('bookingoffice.cancel');
    
    Route::get('/virtual-index', [VirtualBookingController::class, 'index'])->name('virtual.home');
    Route::get('/virtual-booking/{virtual}', [VirtualBookingController::class, 'view'])->name('booking.virtual');
    Route::post('/virtual-booking', [VirtualBookingController::class, 'store'])->name('bookingvirtual.store');
    Route::get('/virtual-booking', [VirtualBookingController::class, 'show'])->name('bookingvirtual.show');

    Route::put('/booking-virtual/{virtual}/approve', [VirtualBookingController::class, 'approve'])->name('bookingvirtual.approve');
    Route::put('/booking-virtual/{virtual}/reject', [VirtualBookingController::class, 'reject'])->name('bookingvirtual.reject');
    Route::put('/booking-virtual/{virtual}/cancel', [VirtualBookingController::class, 'cancel'])->name('bookingvirtual.cancel');

    Route::get('/booking-hotdesk/{hotDesk}', [HotDeskBookingController::class, 'viewHotDesk'])->name('booking.hotdesk');
    Route::post('/booking-hotdesk', [HotDeskBookingController::class, 'store'])->name('bookinghotdesk.store');
    Route::get('/hotdesk-booking', [HotDeskBookingController::class, 'show'])->name('bookinghotdesk.show');
    
    Route::put('/booking-hotdesk/{hotdesk}/approve', [HotDeskBookingController::class, 'approve'])->name('hotdeskbooking.approve');
    Route::put('/booking-hotdesk/{hotdesk}/reject', [HotDeskBookingController::class, 'reject'])->name('hotdeskbooking.reject');
    Route::put('/booking-hotdesk/{hotdesk}/cancel', [HotDeskBookingController::class, 'cancel'])->name('hotdeskbooking.cancel');

    Route::get('/booking-boardrooms', [BoardroomBookingController::class, 'index'])->name('booking.boardrooms');
    Route::get('/booking-boardrooms/{bookedboardroom}', [BoardroomBookingController::class, 'view'])->name('booking.boardroom_view');
    Route::post('/booking-boardrooms', [BoardroomBookingController::class, 'store'])->name('bookingboardroom.store');
    Route::get('/boardroom-booking', [BoardroomBookingController::class, 'show'])->name('bookingboardroom.show');

    Route::put('/booking-boardrooms/{booking}/approve', [BoardroomBookingController::class, 'approve'])->name('boardroombooking.approve');
    Route::put('/booking-boardrooms/{booking}/reject', [BoardroomBookingController::class, 'reject'])->name('boardroombooking.reject');
    Route::put('/booking-boardrooms/{booking}/cancel', [BoardroomBookingController::class, 'cancel'])->name('boardroombooking.cancel');


    Route::get('/booking-extras', [BookingController::class, 'extrasIndex'])->name('booking.extras');
  
    


});

require __DIR__.'/auth.php';
// Frontend of the application

Route::get('/offices', [OfficeController::class, 'index'])->name('offices.index');

Route::get('/contact', [ContactController::class, 'index'])->name('contact.index');

Route::get('/about', [AboutController::class, 'index'])->name('about.index');

Route::get('/pricing', [PricingController::class, 'index'])->name('pricing.index');


Route::middleware(['web', 'auth'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/manage', [ManagerController::class, 'index'])->name('manage.user');
        Route::get('/manage/create', [ManagerController::class, 'create'])->name('manage.create');
        Route::post('/manage', [ManagerController::class, 'store'])->name('manage.store');
        Route::get('/manage/{user}/edit', [ManagerController::class, 'edit'])->name('manage.edit');
        Route::put('/manage/{user}', [ManagerController::class, 'update'])->name('manage.update');
        Route::delete('/manage/{user}', [ManagerController::class, 'destroy'])
                ->name('manage.destroy');

        Route::get('/offices', [OfficeController::class, 'adminIndex'])->name('offices');
        Route::get('/offices/create', [OfficeController::class, 'create'])->name('offices.create');
        Route::post('/offices', [OfficeController::class, 'store'])->name('offices.store');
        Route::get('/offices/{Office}/edit', [OfficeController::class, 'edit'])->name('offices.edit');
        Route::put('/offices/{Office}', [OfficeController::class, 'update'])->name('offices.update');
        Route::delete('/offices/{Office}', [OfficeController::class, 'destroy'])
            ->name('offices.destroy');

        Route::get('/closed-offices', [ClosedOfficeController::class, 'adminIndex'])->name('closedoffices');
        Route::get('/closed-offices/create', [ClosedOfficeController::class, 'create'])->name('closedoffices.create');
        Route::post('/closed-offices', [ClosedOfficeController::class, 'store'])->name('closedoffices.store');
        Route::get('/closed-offices/{Office}/edit', [ClosedOfficeController::class, 'edit'])->name('closedoffices.edit');
        Route::put('/closed-offices/{Office}', [ClosedOfficeController::class, 'update'])->name('closedoffices.update');
        Route::delete('/closed-offices/{Office}', [ClosedOfficeController::class, 'destroy'])
            ->name('closedoffices.destroy');

        Route::get('/dedicated-offices', [DedicatedDeskController::class, 'adminIndex'])->name('dedicateddesk');
        Route::get('/dedicated-offices/create', [DedicatedDeskController::class, 'create'])->name('dedicateddesk.create');
        Route::post('/dedicated-offices', [DedicatedDeskController::class, 'store'])->name('dedicateddesk.store');
        Route::get('/dedicated-offices/{Office}/edit', [DedicatedDeskController::class, 'edit'])->name('dedicateddesk.edit');
        Route::put('/dedicated-offices/{Office}', [DedicatedDeskController::class, 'update'])->name('dedicateddesk.update');
        Route::delete('/dedicated-offices/{Office}', [DedicatedDeskController::class, 'destroy'])
            ->name('offices.destroy');
        
        Route::get('/offices_rates', [OfficePricingController::class, 'index'])->name('offices_rates');
        Route::get('/offices_rates/create', [OfficePricingController::class, 'create'])->name('offices_rates.create');
        Route::post('/offices_rates', [OfficePricingController::class, 'store'])->name('offices_rates.store');
        Route::get('/offices_rates/{officePricing}/edit', [OfficePricingController::class, 'edit'])->name('offices_rates.edit');
        Route::put('/offices_rates/{officePricing}', [OfficePricingController::class, 'update'])->name('offices_rates.update');
        Route::delete('/offices_rates/{officePricing}', [OfficePricingController::class, 'destroy'])
                ->name('offices_rates.destroy');

        Route::get('/categories', [CategoriesController::class, 'index'])->name('categories');
        Route::get('/categories/create', [CategoriesController::class, 'create'])->name('categories.create');
        Route::post('/categories', [CategoriesController::class, 'store'])->name('categories.store');
        Route::get('/categories/{category}/edit', [CategoriesController::class, 'edit'])->name('categories.edit');
        Route::put('/categories/{category}', [CategoriesController::class, 'update'])->name('categories.update');
        Route::delete('/categories/{category}', [CategoriesController::class, 'destroy'])
                ->name('categories.destroy');

        Route::get('/amenities', [AmenityController::class, 'index'])->name('amenities');
        Route::get('/amenities/create', [AmenityController::class, 'create'])->name('amenities.create');
        Route::post('/amenities', [AmenityController::class, 'store'])->name('amenities.store');
        Route::get('/amenities/{amenity}/edit', [AmenityController::class, 'edit'])->name('amenities.edit');
        Route::put('/amenities/{amenity}', [AmenityController::class, 'update'])->name('amenities.update');
        Route::delete('/amenities/{amenity}', [AmenityController::class, 'destroy'])
                ->name('amenities.destroy');

        Route::get('/boardrooms', [BoardroomController::class, 'index'])->name('boardrooms');
        Route::get('/boardrooms/create', [BoardroomController::class, 'create'])->name('boardrooms.create');
        Route::post('/boardrooms', [BoardroomController::class, 'store'])->name('boardrooms.store');
        Route::get('/boardrooms/{boardroom}/edit', [BoardroomController::class, 'edit'])->name('boardrooms.edit');
        Route::put('/boardrooms/{boardroom}', [BoardroomController::class, 'update'])->name('boardrooms.update');
        Route::delete('/boardrooms/{boardroom}', [BoardroomController::class, 'destroy'])
                ->name('boardrooms.destroy');
    
        Route::get('/virtual-offices', [VirtualOfficeController::class, 'index'])->name('virtual-offices');
        Route::get('/virtual-office/create', [VirtualOfficeController::class, 'create'])->name('virtual-office.create');
        Route::post('/virtual-office', [VirtualOfficeController::class, 'store'])->name('virtual-office.store');
        Route::get('/virtual-office/{virtualoffice}/edit', [VirtualOfficeController::class, 'edit'])->name('virtual-office.edit');
        Route::put('/virtual-office/{virtualoffice}', [VirtualOfficeController::class, 'update'])->name('virtual-office.update');
        Route::delete('/virtual-office/{virtualoffice}', [VirtualOfficeController::class, 'destroy'])
                ->name('virtual-office.destroy');
    
        Route::get('/help-desks', [HelpDeskController::class, 'index'])->name('help-desks');
        Route::get('/help-desk/create', [HelpDeskController::class, 'create'])->name('help-desk.create');
        Route::post('/help-desk', [HelpDeskController::class, 'store'])->name('help-desk.store');
        Route::get('/help-desk/{helpDesk}/edit', [HelpDeskController::class, 'edit'])->name('help-desk.edit');
        Route::put('/help-desk/{helpDesk}', [HelpDeskController::class, 'update'])->name('help-desk.update');
        Route::delete('/help-desk/{helpDesk}', [HelpDeskController::class, 'destroy'])
                ->name('help-desk.destroy');

        Route::get('/locations', [LocationController::class, 'index'])->name('locations');
        Route::get('/location/create', [LocationController::class, 'create'])->name('location.create');
        Route::post('/location', [LocationController::class, 'store'])->name('location.store');
        Route::get('/location/{location}/edit', [LocationController::class, 'edit'])->name('location.edit');
        Route::put('/location/{location}', [LocationController::class, 'update'])->name('location.update');
        Route::delete('/location/{location}', [LocationController::class, 'destroy'])
                ->name('location.destroy');
    
        Route::get('/roles', [RoleController::class, 'index'])->name('roles');
        Route::get('/role/create', [RoleController::class, 'create'])->name('role.create');
        Route::post('/role', [RoleController::class, 'store'])->name('role.store');
        Route::get('/role/{role}/edit', [RoleController::class, 'edit'])->name('role.edit');
        Route::put('/role/{role}', [RoleController::class, 'update'])->name('role.update');
        Route::delete('/role/{role}', [RoleController::class, 'destroy'])
                ->name('role.destroy');
    
        Route::get('/permissions', [PermissionController::class, 'index'])->name('permissions');
        Route::get('/permission/create', [PermissionController::class, 'create'])->name('permission.create');
        Route::post('/permission', [PermissionController::class, 'store'])->name('permission.store');
        Route::get('/permission/{permission}/edit', [PermissionController::class, 'edit'])->name('permission.edit');
        Route::put('/permission/{permission}', [PermissionController::class, 'update'])->name('permission.update');
        Route::delete('/permission/{permission}', [PermissionController::class, 'destroy'])     
                ->name('permission.destroy');
    });
