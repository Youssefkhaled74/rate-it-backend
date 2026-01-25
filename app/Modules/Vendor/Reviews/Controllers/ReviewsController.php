<?php

namespace App\Modules\Vendor\Reviews\Controllers;

use App\Support\Api\BaseApiController;
use App\Modules\Vendor\Reviews\Services\VendorReviewService;
use App\Modules\Vendor\Reviews\Requests\VendorReviewsIndexRequest;
use App\Modules\Vendor\Reviews\Resources\VendorReviewListResource;
use App\Modules\Vendor\Reviews\Resources\VendorReviewDetailResource;
use Illuminate\Support\Facades\Auth;

class ReviewsController extends BaseApiController
{
    protected VendorReviewService $service;

    public function __construct(VendorReviewService $service)
    {
        $this->service = $service;
    }

    /**
     * List vendor's reviews with filters and pagination
     * VENDOR_ADMIN only
     */
    public function index(VendorReviewsIndexRequest $request)
    {
        $vendor = Auth::guard('vendor')->user();
        $filters = $request->validated();
        
        $paginator = $this->service->list($vendor, $filters);
        return $this->paginated($paginator, 'vendor.reviews.list', VendorReviewListResource::class);
    }

    /**
     * Get review details with photos and answers
     * VENDOR_ADMIN only
     */
    public function show(string $id)
    {
        $vendor = Auth::guard('vendor')->user();
        $review = $this->service->find($vendor, (int) $id);
        
        if (! $review) {
            return $this->error(__('vendor.reviews.not_found'), null, 404);
        }

        return $this->success(new VendorReviewDetailResource($review), 'vendor.reviews.details');
    }
}
