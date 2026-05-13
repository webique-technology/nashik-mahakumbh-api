<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AdminAuthController;
use App\Http\Controllers\Api\TourController;

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


