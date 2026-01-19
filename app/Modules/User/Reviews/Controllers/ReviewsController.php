<?php

namespace App\Modules\User\Reviews\Controllers;

use App\Support\Api\BaseApiController;
use App\Modules\User\Reviews\Requests\ScanQrRequest;
use App\Modules\User\Reviews\Requests\BranchQuestionsRequest;
use App\Modules\User\Reviews\Requests\StoreReviewRequest;
use App\Modules\User\Reviews\Services\QrScanService;
use App\Modules\User\Reviews\Services\RatingCriteriaService;
use App\Modules\User\Reviews\Services\ReviewService;
use App\Models\Branch;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewsController extends BaseApiController
{
    public function scanQr(ScanQrRequest $req, QrScanService $svc)
    {
        $payload = $svc->scan($req->user(), $req->input('qr_code_value'));
        if (! $payload) {
            return $this->error(trans('reviews.invalid_qr'), 422);
        }

        [$session, $branch] = [$payload['session'], $payload['branch']];

        // load place and criteria
        $branch->load('place');
        $criteriaSvc = app(RatingCriteriaService::class);
        $questions = $criteriaSvc->getQuestionsForBranch($branch);

        return $this->success(new \App\Modules\User\Reviews\Resources\QrScanResource($session), 'reviews.qr_scanned', [
            'branch' => $branch,
            'place' => $branch->place,
            'questions' => \App\Modules\User\Reviews\Resources\RatingCriteriaResource::collection($questions),
        ]);
    }

    public function branchQuestions(BranchQuestionsRequest $req, Branch $branch, RatingCriteriaService $svc)
    {
        $questions = $svc->getQuestionsForBranch($branch);
        return $this->success(\App\Modules\User\Reviews\Resources\RatingCriteriaResource::collection($questions), 'reviews.questions');
    }

    public function store(StoreReviewRequest $req, ReviewService $svc)
    {
        $user = $req->user();
        $answers = $req->input('answers', []);
        if (is_string($answers)) {
            $answers = json_decode($answers, true) ?? [];
        }
        $photos = $req->file('photos', []);

        $result = $svc->createReview($user, $req->input('session_token'), $req->input('overall_rating'), $req->input('comment'), $answers, $photos);

        // $result may be an array with review and points info
        if (is_array($result) && isset($result['review'])) {
            $review = $result['review'];
            $meta = [
                'points_awarded' => $result['points_awarded'] ?? 0,
                'points_balance' => $result['points_balance'] ?? 0,
            ];
            return $this->success(new \App\Modules\User\Reviews\Resources\ReviewResource($review), 'reviews.created', $meta);
        }

        return $this->success(new \App\Modules\User\Reviews\Resources\ReviewResource($result), 'reviews.created');
    }

    public function myReviews(Request $req)
    {
        $user = $req->user();
        $perPage = (int) $req->query('per_page', 15);
        $p = Review::where('user_id', $user->id)->with(['photos','answers'])->paginate($perPage);
        return $this->success(\App\Modules\User\Reviews\Resources\ReviewResource::collection($p), 'reviews.my_list');
    }
    public function show(Request $req, Review $review)
    {
        $user = $req->user();
        if ($review->user_id !== $user->id) {
            return $this->error(trans('reviews.unauthorized'), 403);
        }
        $review->load(['photos','answers.choice','answers.criteria']);
        return $this->success(new \App\Modules\User\Reviews\Resources\ReviewResource($review), 'reviews.details');
    }
}
