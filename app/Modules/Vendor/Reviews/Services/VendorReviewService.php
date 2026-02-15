<?php

namespace App\Modules\Vendor\Reviews\Services;

use App\Models\Review;
use App\Models\VendorUser;
use App\Support\Traits\Vendor\VendorScoping;
use Illuminate\Pagination\LengthAwarePaginator;

class VendorReviewService
{
    use VendorScoping;

    /**
     * List reviews for vendor's brand with filters and pagination
     * Only VENDOR_ADMIN can access this endpoint
     */
    public function list(VendorUser $vendor, array $filters): LengthAwarePaginator
    {
        $perPage = (int) ($filters['per_page'] ?? 15);
        
        // Get vendor's brand ID
        $brandId = $this->getVendorBrandId($vendor);
        
        // Query reviews under vendor's brand
        $query = Review::query()
            ->with(['user', 'place', 'branch:id,name,name_en,name_ar'])
            ->withCount(['photos'])
            ->whereHas('branch', fn($q) => $q->where('brand_id', $brandId));

        // Filter by branch if specified
        if (! empty($filters['branch_id'])) {
            $branchId = (int) $filters['branch_id'];
            
            // Verify branch belongs to vendor's brand
            $branch = \App\Models\Branch::find($branchId);
            if (! $branch || (int) $branch->brand_id !== (int) $brandId) {
                return Review::paginate(0); // Return empty paginator
            }
            
            $query->where('branch_id', $branchId);
        }

        // Filter by date range
        if (! empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }
        if (! empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        // Filter by rating range
        if (isset($filters['min_rating'])) {
            $query->where('overall_rating', '>=', (float) $filters['min_rating']);
        }
        if (isset($filters['max_rating'])) {
            $query->where('overall_rating', '<=', (float) $filters['max_rating']);
        }

        // Filter by has_photos
        if (isset($filters['has_photos']) && $filters['has_photos']) {
            $query->has('photos');
        }

        // Keyword search
        if (! empty($filters['keyword'])) {
            $keyword = $filters['keyword'];
            $query->where(function ($q) use ($keyword) {
                $q->where('comment', 'like', "%{$keyword}%")
                    ->orWhereHas('user', fn($s) => $s->where('name', 'like', "%{$keyword}%")->orWhere('phone', 'like', "%{$keyword}%"))
                    ->orWhereHas('branch', fn($s) => $s->where('name', 'like', "%{$keyword}%"));
            });
        }

        $query->orderBy('created_at', 'desc');

        $page = (int) ($filters['page'] ?? 1);
        return $query->paginate($perPage, ['*'], 'page', $page);
    }

    /**
     * Get single review with full details
     * Enforces brand-level scoping
     */
    public function find(VendorUser $vendor, int $reviewId)
    {
        $brandId = $this->getVendorBrandId($vendor);
        
        $review = Review::with([
            'user',
            'place',
            'branch:id,name,name_en,name_ar,address',
            'answers.criteria',
            'answers.choice',
            'photos'
        ])->whereHas('branch', fn($q) => $q->where('brand_id', $brandId))->find($reviewId);

        return $review;
    }
}
