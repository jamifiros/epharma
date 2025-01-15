<?php

use App\Models\Prescription;
use Database\Seeders\AdminSeeder;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\AdminController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('login');
});
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');

Route::post('/login', [LoginController::class, 'login'])->name('login');

Route::middleware(['auth', 'role:admin']) // Ensure only authenticated users with admin role can access
    ->name('admin.') // This will prefix all route names with "admin."
    ->group(function () {
        
        // Admin dashboard route
        Route::get('/dashboard', function () {return view('dashboard');})->name('dashboard');
        // Users route, managed by AdminController
        Route::get('/users', [AdminController::class, 'index'])->name('users');
        Route::get('/user/{id}/details', [AdminController::class, 'show'])->name('userdetails.show');
        Route::delete('/user/{id}/details', [AdminController::class, 'destroy'])->name('userdetails.destroy');
        Route::get('admin/prescriptions', [AdminController::class, 'showPrescriptions'])->name('prescriptions');
        Route::get('admin/prescriptions/{id}', [AdminController::class, 'showMedicineAddForm'])->name('prescription.show');
        Route::post('/medicines/store/{prescription}', [AdminController::class, 'store'])->name('medicines.store');
        Route::get('prescriptions/history', [AdminController::class, 'showPrescriptionsHistory'])->name('prescriptions.history');
        Route::get('/medicines/show/{prescription}', [AdminController::class, 'showMedicines'])->name('medicines.show');
        Route::get('/user/prescriptions/{id}',[AdminController::class,'prescriptions'])->name('user.prescriptions');
    });