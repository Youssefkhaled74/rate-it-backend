<?php

namespace App\Support\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use App\Support\Traits\Api\ApiResponseTrait;

class BaseApiController extends Controller
{
    use ApiResponseTrait;
}
