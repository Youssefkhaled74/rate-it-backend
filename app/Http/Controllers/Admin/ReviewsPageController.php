<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;

class ReviewsPageController extends Controller
{
    public function show(Review $review)
    {
        $review->load(['user', 'place', 'branch', 'photos', 'answers']);

        return view('admin.reviews.show', compact('review'));
    }
}
