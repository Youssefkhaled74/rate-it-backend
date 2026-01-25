<?php

namespace App\Modules\Vendor\Auth\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Support\Resources\TimestampResource;
use App\Models\VendorUser;

class VendorUserResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'brand_id' => $this->brand_id,
            'branch_id' => $this->branch_id,
            'name' => $this->name,
            'phone' => $this->phone,
            'email' => $this->email,
            'photo' => $this->photo ? asset('storage/' . $this->photo) : null,
            'role' => $this->role,
            'is_active' => (bool) $this->is_active,
            
            'brand' => $this->brand ? [
                'id' => $this->brand->id,
                'name' => $this->brand->name,
                'name_en' => $this->brand->name_en ?? null,
                'name_ar' => $this->brand->name_ar ?? null,
                'logo_url' => $this->brand->logo_url,
            ] : null,
            
            'branch' => $this->branch ? [
                'id' => $this->branch->id,
                'name' => $this->branch->name,
                'address' => $this->branch->address,
                'phone' => $this->branch->phone,
                'email' => $this->branch->email,
            ] : null,
            
            // Staff statistics
            'staff_count' => $this->getStaffCount(),
            'branches_count' => $this->getBranchesCount(),
            
            // Reviews statistics
            'reviews_stats' => $this->getReviewsStats(),
            
            // Subscription information
            'subscription' => $this->getSubscriptionInfo(),
            
            // Vouchers count
            'vouchers_count' => $this->getVouchersCount(),
            
            'timestamps' => [
                'created_at' => new TimestampResource($this->created_at),
                'updated_at' => new TimestampResource($this->updated_at),
            ],
        ];
    }

    /**
     * Get count of staff members under this vendor
     */
    private function getStaffCount()
    {
        try {
            return VendorUser::where('brand_id', $this->brand_id)
                ->where('role', 'BRANCH_STAFF')
                ->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Get count of branches owned by this vendor
     */
    private function getBranchesCount()
    {
        try {
            if (!$this->brand_id) {
                return 0;
            }

            return \App\Models\Place::where('brand_id', $this->brand_id)
                ->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Get reviews statistics for vendor's brand
     */
    private function getReviewsStats()
    {
        try {
            if (!$this->brand_id) {
                return null;
            }

            $placeIds = \App\Models\Place::where('brand_id', $this->brand_id)
                ->pluck('id')
                ->toArray();

            if (empty($placeIds)) {
                return [
                    'total_reviews' => 0,
                    'average_rating' => 0,
                    'total_photos' => 0,
                ];
            }

            $reviews = \App\Models\Review::whereIn('place_id', $placeIds);
            $count = $reviews->count();
            $avgRating = $count > 0 ? round($reviews->avg('rating'), 2) : 0;

            return [
                'total_reviews' => $count,
                'average_rating' => $avgRating,
                'total_photos' => \App\Models\ReviewPhoto::whereIn('review_id', function ($q) use ($placeIds) {
                    $q->select('id')->from('reviews')->whereIn('place_id', $placeIds);
                })->count(),
            ];
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Get active subscription information
     */
    private function getSubscriptionInfo()
    {
        try {
            if (!$this->brand_id) {
                return null;
            }

            $subscription = \App\Models\Subscription::where('brand_id', $this->brand_id)
                ->where('status', 'active')
                ->first();

            if (!$subscription) {
                return null;
            }

            return [
                'id' => $subscription->id,
                'plan_name' => $subscription->plan?->name ?? 'Unknown',
                'status' => $subscription->status,
                'started_at' => $subscription->started_at,
                'expires_at' => $subscription->expires_at,
            ];
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Get count of active vouchers
     */
    private function getVouchersCount()
    {
        try {
            if (!$this->brand_id) {
                return 0;
            }

            return \App\Models\Voucher::where('brand_id', $this->brand_id)
                ->where('is_active', true)
                ->count();
        } catch (\Exception $e) {
            return 0;
        }
    }
}
