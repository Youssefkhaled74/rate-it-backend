<?php

namespace App\Modules\User\Home\Services;

use App\Modules\User\Home\Resources\CategoryResource;
use App\Modules\User\Home\Resources\HomeBannerResource;
use App\Modules\User\Home\Resources\HomeTopBrandResource;

class HomePageService
{
    public function __construct(
        protected HomeCategoriesService $categoriesService,
        protected HomeBannerService $bannerService,
        protected HomeTopBrandService $topBrandService
    ) {
    }

    public function getPageData(int $perPage = 10): array
    {
        $topFiveBrands = $this->topBrandService->getTopFiveBrands();
        $allBrandsPaginator = $this->topBrandService->paginateAllBrandsRandom($perPage);

        return [
            'categories' => CategoryResource::collection($this->categoriesService->list()),
            'banners' => HomeBannerResource::collection($this->bannerService->listForHome()),
            'top_five_brands' => HomeTopBrandResource::collection($topFiveBrands),
            'all_brands' => [
                'items' => HomeTopBrandResource::collection($allBrandsPaginator->items()),
                'pagination' => [
                    'page' => $allBrandsPaginator->currentPage(),
                    'per_page' => $allBrandsPaginator->perPage(),
                    'total' => $allBrandsPaginator->total(),
                    'last_page' => $allBrandsPaginator->lastPage(),
                    'has_next' => $allBrandsPaginator->hasMorePages(),
                ],
            ],
        ];
    }
}
