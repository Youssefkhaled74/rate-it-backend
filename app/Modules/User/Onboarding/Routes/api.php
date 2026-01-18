<?php

use Illuminate\Support\Facades\Route;
use App\Modules\User\Onboarding\Controllers\OnboardingController;

Route::get('onboarding-screens', [OnboardingController::class, 'index']);
