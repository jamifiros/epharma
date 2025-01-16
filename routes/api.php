<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\EmailController;



/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });


// Public routes
Route::post('/login', [ApiController::class, 'login']);
Route::post('/register', [ApiController::class, 'register']);

Route::middleware(['auth:sanctum', 'role:user'])->group(function () {
    Route::post('/logout', [ApiController::class, 'logout']);
    Route::post('/prescriptions', [ApiController::class, 'addPrescription']);
    Route::get('/prescriptions/view', [ApiController::class, 'viewPrescriptions']);

    Route::get('/medicines', [ApiController::class, 'viewMedicines']);
    Route::post('/medicine-intake', [ApiController::class, 'addMedicineIntake']);
    Route::delete('/delete/{id}', [ApiController::class, 'deletePrescription']);

    Route::post('/email', [ApiController::class, 'notifyGuardian']);
});




