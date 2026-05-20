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

Route::get('/test', function () {
    return response()->json([
        'message' => 'API working'
    ]);
});


Route::post('/admin/login', [AdminAuthController::class, 'login']);

Route::post('/tours', [TourController::class, 'store']);
Route::get('/tours', [TourController::class, 'index']);
Route::get('/tours/{id}', [TourController::class, 'show']);
Route::post('/tours/{id}', [TourController::class, 'update']);
Route::delete('/tours/{id}', [TourController::class, 'destroy']);

Route::get('/vehicles', [VehicleController::class, 'index']);
Route::get('/vehicles/{id}', [VehicleController::class, 'show']);
Route::post('/vehicles', [VehicleController::class, 'store']);
Route::post('/vehicles/{id}', [VehicleController::class, 'update']);
Route::delete('/vehicles/{id}', [VehicleController::class, 'destroy']);

Route::get('/hotels', [HotelController::class, 'index']);
Route::get('/hotels/{id}', [HotelController::class, 'show']);
Route::post('/hotels', [HotelController::class, 'store']);
Route::post('/hotels/{id}', [HotelController::class, 'update']);
Route::delete('/hotels/{id}', [HotelController::class, 'destroy']);

Route::get('/blogs', [BlogController::class, 'index']);
Route::get('/blogs/{id}', [BlogController::class, 'show']);
Route::post('/blogs', [BlogController::class, 'store']);
Route::post('/blogs/{id}', [BlogController::class, 'update']);
Route::delete('/blogs/{id}', [BlogController::class, 'destroy']);

// Privacy Policy
Route::post('/privacy-policy', [PolicyController::class, 'savePrivacyPolicy']);
Route::get('/privacy-policy', [PolicyController::class, 'getPrivacyPolicy']);

// Payment Policy
Route::post('/payment-policy', [PolicyController::class, 'savePaymentPolicy']);
Route::get('/payment-policy', [PolicyController::class, 'getPaymentPolicy']);


// Route::get('/sliders', [SliderController::class, 'index']);
Route::post('/sliders', [SliderController::class, 'store']);
Route::get('/sliders/{id}', [SliderController::class, 'show']);
Route::post('/sliders/{id}', [SliderController::class, 'update']);
Route::delete('/sliders/{id}', [SliderController::class, 'destroy']);

Route::get('/tour-enquiries', [TourEnquiryController::class, 'index']);
Route::post('/tour-enquiries', [TourEnquiryController::class, 'store']);
Route::get('/tour-enquiries/{id}', [TourEnquiryController::class, 'show']);
Route::delete('/tour-enquiries/{id}', [TourEnquiryController::class, 'destroy']);


Route::post('/contact-us', [ContactUsController::class, 'store']);
Route::get('/contact-us', [ContactUsController::class, 'index']);
// Route::get('/contact-us/{id}', [ContactUsController::class, 'show']);
// Route::delete('/contact-us/{id}', [ContactUsController::class, 'destroy']);


Route::middleware('api.language')->group(function () {

    Route::get('/sliders', [SliderController::class, 'index']);

});



Route::middleware('auth:sanctum')->group(function () {
    Route::get('/profile', function (\Illuminate\Http\Request $request) {
        return $request->user();
    });
    Route::post('/admin/logout', [AdminAuthController::class, 'logout']);
});

// Route::middleware(['auth:sanctum', 'admin'])->group(function () {
//     Route::get('/admin/dashboard', function () {
//         return response()->json(['message' => 'Welcome Admin']);
//     });
// });

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';


