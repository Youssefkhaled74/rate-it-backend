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
        $topBrandsPaginator = $this->topBrandService->paginateTopBrands($perPage);

        return [
            'categories' => CategoryResource::collection($this->categoriesService->list()),
            'banners' => HomeBannerResource::collection($this->bannerService->listForHome()),
            'top_brands' => [
                'items' => HomeTopBrandResource::collection($topBrandsPaginator->items()),
                'pagination' => [
                    'page' => $topBrandsPaginator->currentPage(),
                    'per_page' => $topBrandsPaginator->perPage(),
                    'total' => $topBrandsPaginator->total(),
                    'last_page' => $topBrandsPaginator->lastPage(),
                    'has_next' => $topBrandsPaginator->hasMorePages(),
                ],
            ],
        ];
    }
}
