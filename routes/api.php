<?php

use App\Http\Controllers\Api\AppointmentSlotController;
use Illuminate\Support\Facades\Route;

// ──────────────────────────────────────────────────────────────
// Public API Endpoints
// ──────────────────────────────────────────────────────────────

// Cek slot appointment tersedia
// GET /api/appointments/available-slots?date=YYYY-MM-DD
Route::get('appointments/available-slots', [AppointmentSlotController::class, 'availableSlots'])
    ->name('api.appointments.available-slots');
