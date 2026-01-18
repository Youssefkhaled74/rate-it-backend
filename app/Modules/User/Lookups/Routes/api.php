<?php

use Illuminate\Support\Facades\Route;

Route::get('lookups/genders', [\App\Modules\User\Lookups\Controllers\GendersController::class, 'index']);
Route::get('lookups/nationalities', [\App\Modules\User\Lookups\Controllers\NationalitiesController::class, 'index']);
