<?php

namespace App\Modules\Admin\Reviews\Controllers;

use App\Support\Api\BaseApiController;
use Illuminate\Http\Request;
use App\Modules\Admin\Reviews\Services\ReviewModerationService;
use App\Modules\Admin\Reviews\Requests\AdminReviewsIndexRequest;
use App\Modules\Admin\Reviews\Requests\HideReviewRequest;
use App\Modules\Admin\Reviews\Requests\ReplyReviewRequest;
use App\Modules\Admin\Reviews\Requests\MarkFeaturedRequest;
use App\Modules\Admin\Reviews\Resources\AdminReviewResource;
use App\Modules\Admin\Reviews\Resources\AdminReviewListResource;

class ReviewsController extends BaseApiController
{
    protected ReviewModerationService $service;

    public function __construct(ReviewModerationService $service)
    {
        $this->service = $service;
    }

    public function index(AdminReviewsIndexRequest $request)
    {
        $filters = $request->validated();
        $paginator = $this->service->list($filters);
        return $this->paginated($paginator, 'admin.reviews.list');
    }

    public function show($id)
    {
        $review = $this->service->find((int) $id);
        if (! $review) return $this->error('Not found', null, 404);
        return $this->success(new AdminReviewResource($review), 'admin.reviews.details');
    }

    public function hide(HideReviewRequest $request, $id)
    {
        $data = $request->validated();
        $review = $this->service->hide((int) $id, $data);
        if (! $review) return $this->error('Not found', null, 404);
        return $this->success(new AdminReviewResource($review), $data['is_hidden'] ? 'admin.reviews.hidden' : 'admin.reviews.unhidden');
    }

    public function reply(ReplyReviewRequest $request, $id)
    {
        $data = $request->validated();
        $review = $this->service->reply((int) $id, $data);
        if (! $review) return $this->error('Not found', null, 404);
        return $this->success(new AdminReviewResource($review), 'admin.reviews.replied');
    }

    public function markFeatured(MarkFeaturedRequest $request, $id)
    {
        $data = $request->validated();
        $review = $this->service->feature((int) $id, $data);
        if (! $review) return $this->error('Not found', null, 404);
        return $this->success(new AdminReviewResource($review), $data['is_featured'] ? 'admin.reviews.featured' : 'admin.reviews.unfeatured');
    }
}
