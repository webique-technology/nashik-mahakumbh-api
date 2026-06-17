<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AdminAuthController;
use App\Http\Controllers\Api\TourController;
use App\Http\Controllers\Api\VehicleController;
use App\Http\Controllers\Api\HotelController;
use App\Http\Controllers\Api\BlogController;
use App\Http\Controllers\Api\PolicyController;
use App\Http\Controllers\Api\SliderController;
use App\Http\Controllers\Api\TourEnquiryController;
use App\Http\Controllers\Api\ContactUsController;
use App\Http\Controllers\Api\VehicleCategoryController;
use App\Http\Controllers\Api\CarouselController;
use App\Http\Controllers\Api\VideoController;
use App\Http\Controllers\Api\HotelEnquiryController;
use App\Http\Controllers\Api\VehicleEnquiryController;
use App\Http\Controllers\DashboardController;

Route::get('/test', function () {
    return response()->json([
        'message' => 'API working'
    ]);
});


// Website
Route::post('/admin/login', [AdminAuthController::class, 'login']);

//tours
Route::post('/tours/translate/{id}', [TourController::class, 'translateTour']);

Route::get('/tours', [TourController::class, 'index']);
Route::get('/tours/{id}', [TourController::class, 'show']);
Route::get('/tours/slug/{slug}', [TourController::class, 'getBySlug']);

Route::get('/tours/{id}/vehicle-categories', [TourController::class, 'getVehicleCategories']);

//vehicles
Route::get('/vehicles', [VehicleController::class, 'index']);
Route::get('/vehicles/{id}', [VehicleController::class, 'show']);


//hotels
Route::get('/hotels', [HotelController::class, 'index']);
Route::get('/hotels/{id}', [HotelController::class, 'show']);

//blogs
Route::post('/blogs/translate/{id}', [BlogController::class, 'translateBlog']);

Route::get('/blogs', [BlogController::class, 'index']);
Route::get('/blogs/{id}', [BlogController::class, 'show']);
Route::get('/blogs/slug/{slug}', [BlogController::class, 'getBySlug']);


// Privacy Policy
Route::get('/privacy-policy', [PolicyController::class, 'getPrivacyPolicy']);

// Payment Policy
Route::get('/payment-policy', [PolicyController::class, 'getPaymentPolicy']);

//sliders
Route::get('/sliders', [SliderController::class, 'index']);
Route::get('/sliders/{id}', [SliderController::class, 'show']);

//tour-enquiries
Route::post('/tour-enquiries', [TourEnquiryController::class, 'store']);

//contact-us
Route::post('/contact-us', [ContactUsController::class, 'store']);

Route::get('/vehicle-categories', [VehicleCategoryController::class, 'index']);

//carousel
Route::prefix('carousel')->group(function () {
    Route::get('/', [CarouselController::class, 'index']);
    Route::get('/{id}', [CarouselController::class, 'show']);
});

//videos
Route::prefix('videos')->group(function () {
    Route::get('/', [VideoController::class, 'index']);
    Route::get('/{id}', [VideoController::class, 'show']);
});

//hotel-enquiries
Route::prefix('hotel-enquiries')->group(function () {
    Route::post('/store', [HotelEnquiryController::class, 'store']);
});

//vehicle-enquiries
Route::prefix('vehicle-enquiries')->group(function () {
    Route::post('/store', [VehicleEnquiryController::class, 'store']);
});


Route::middleware('api.language')->group(function () {

    Route::get('/sliders', [SliderController::class, 'index']);

});


// Admin
Route::middleware('auth:sanctum')->group(function () {
        Route::get('/profile', function (\Illuminate\Http\Request $request) {
            return $request->user();
        });

        Route::get('/dashboard', [DashboardController::class, 'index']);

        //tours
        Route::post('/tours', [TourController::class, 'store']);
        Route::post('/tours/{id}', [TourController::class, 'update']);
        Route::delete('/tours/{id}', [TourController::class, 'destroy']);


        //vehicles
        Route::post('/vehicles', [VehicleController::class, 'store']);
        Route::post('/vehicles/{id}', [VehicleController::class, 'update']);
        Route::delete('/vehicles/{id}', [VehicleController::class, 'destroy']);

        //hotels
        Route::post('/hotels', [HotelController::class, 'store']);
        Route::post('/hotels/{id}', [HotelController::class, 'update']);
        Route::delete('/hotels/{id}', [HotelController::class, 'destroy']);

        //blogs
        Route::post('/blogs', [BlogController::class, 'store']);
        Route::post('/blogs/{id}', [BlogController::class, 'update']);
        Route::delete('/blogs/{id}', [BlogController::class, 'destroy']);

        // Privacy Policy
        Route::post('/privacy-policy', [PolicyController::class, 'savePrivacyPolicy']);
        // Payment Policy
        Route::post('/payment-policy', [PolicyController::class, 'savePaymentPolicy']);

        //tour-enquiries
        Route::get('/tour-enquiries', [TourEnquiryController::class, 'index']);
        Route::get('/tour-enquiries/{id}', [TourEnquiryController::class, 'show']);
        Route::delete('/tour-enquiries/{id}', [TourEnquiryController::class, 'destroy']);

        //carousel
        Route::prefix('carousel')->group(function () {
            Route::post('/store', [CarouselController::class, 'store']);
            Route::post('/update/{id}', [CarouselController::class, 'update']);
            Route::delete('/delete/{id}', [CarouselController::class, 'destroy']);
        });

        //videos
        Route::prefix('videos')->group(function () {
            Route::post('/store', [VideoController::class, 'store']);
            Route::post('/update/{id}', [VideoController::class, 'update']);
            Route::delete('/delete/{id}', [VideoController::class, 'destroy']);
        });

        //hotel-enquiries
        Route::prefix('hotel-enquiries')->group(function () {
            Route::get('/', [HotelEnquiryController::class, 'index']);
            Route::get('/{id}', [HotelEnquiryController::class, 'show']);
            Route::post('/update/{id}', [HotelEnquiryController::class, 'update']);
            Route::delete('/delete/{id}', [HotelEnquiryController::class, 'destroy']);
        });

        //vehicle-enquiries
        Route::prefix('vehicle-enquiries')->group(function () {
            Route::get('/', [VehicleEnquiryController::class, 'index']);
            Route::get('/{id}', [VehicleEnquiryController::class, 'show']);
            Route::post('/update/{id}', [VehicleEnquiryController::class, 'update']);
            Route::delete('/delete/{id}', [VehicleEnquiryController::class, 'destroy']);
        });

        //contact-us
        Route::get('/contact-us', [ContactUsController::class, 'index']);
        // Route::get('/contact-us/{id}', [ContactUsController::class, 'show']);
        // Route::delete('/contact-us/{id}', [ContactUsController::class, 'destroy']);


        //sliders
        Route::post('/sliders', [SliderController::class, 'store']);
        Route::post('/sliders/{id}', [SliderController::class, 'update']);
        Route::delete('/sliders/{id}', [SliderController::class, 'destroy']);
        
        Route::post('/admin/logout', [AdminAuthController::class, 'logout']);
});

// Route::middleware(['auth:sanctum', 'admin'])->group(function () {
//     Route::get('/admin/dashboard', function () {
//         return response()->json(['message' => 'Welcome Admin']);
//     });
// });

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';


