<?php

use Illuminate\Support\Facades\Route;

Route::get('lookups/genders', [\App\Modules\User\Lookups\Controllers\GendersController::class, 'index']);
Route::get('lookups/nationalities', [\App\Modules\User\Lookups\Controllers\NationalitiesController::class, 'index']);
Route::get('lookups/cities', [\App\Modules\User\Lookups\Controllers\CitiesController::class, 'index']);
Route::get('lookups/areas', [\App\Modules\User\Lookups\Controllers\AreasController::class, 'index']);
