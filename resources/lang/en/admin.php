<?php

return [
    // Dashboard endpoints
    'dashboard' => [
        'summary' => 'Dashboard summary retrieved successfully',
        'top_places' => 'Top places retrieved successfully',
        'reviews_chart' => 'Reviews chart data retrieved successfully',
    ],

    // Vendors management
    'vendors' => [
        'list' => 'Vendor accounts retrieved successfully',
        'details' => 'Vendor details retrieved successfully',
        'created' => 'Vendor account created successfully',
        'updated' => 'Vendor account updated successfully',
        'deleted' => 'Vendor account deleted successfully',
        'restored' => 'Vendor account restored successfully',
        'not_found' => 'Vendor account not found',
        'brand_id_required' => 'Brand ID is required',
        'brand_id_invalid' => 'Selected brand does not exist',
        'name_required' => 'Name is required',
        'phone_required' => 'Phone number is required',
        'phone_invalid' => 'Phone number format is invalid',
        'phone_already_exists' => 'Phone number already registered',
        'email_invalid' => 'Email format is invalid',
        'email_already_exists' => 'Email already registered',
        'password_required' => 'Password is required',
        'password_min' => 'Password must be at least 6 characters',
        'password_confirmation_failed' => 'Password confirmation does not match',
        'photo_invalid' => 'Photo must be a valid image',
        'photo_invalid_format' => 'Photo must be in JPEG, PNG, JPG, or GIF format',
        'photo_too_large' => 'Photo size must not exceed 5MB',
    ],
];
