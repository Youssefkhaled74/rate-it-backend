<?php

namespace App\Modules\User\Points\Controllers;

use App\Support\Api\BaseApiController;
use Illuminate\Http\Request;
use App\Modules\User\Points\Services\PointsService;
use App\Modules\User\Points\Resources\PointsTransactionResource;
use App\Support\Exceptions\ApiException;

class PointsController extends BaseApiController
{
    protected PointsService $service;

    public function __construct(PointsService $service)
    {
        $this->service = $service;
    }

    public function summary(Request $request)
    {
        $user = $request->user();
        $balance = $this->service->getBalance($user);
        $setting = $this->service->getCurrentSetting();

        $data = [
            'balance' => $balance,
            'points_per_review' => $setting ? (int) $setting->points_per_review : null,
            'point_value_money' => $setting ? (float) $setting->point_value_money : null,
            'currency' => $setting ? $setting->currency : null,
            // placeholder for levels
            'current_level' => null,
            'next_level' => null,
            'progress_percent' => null,
            'expiring_points' => [ 'total' => 0, 'nearest_expiry' => null ],
        ];

        return $this->success($data, 'points.summary');
    }

    public function history(Request $request)
    {
        $user = $request->user();
        $perPage = (int) $request->query('per_page', 20);
        $p = $this->service->getHistory($user, $perPage);

        return $this->paginated($p, 'points.history');
    }

    public function redeem(Request $request)
    {
        $request->validate(['points' => ['required','integer','min:1']]);
        $user = $request->user();
        $points = (int) $request->input('points');

        $newBalance = $this->service->redeemPoints($user, $points);
        return $this->success(['balance' => $newBalance], 'points.redeem_success');
    }
}
