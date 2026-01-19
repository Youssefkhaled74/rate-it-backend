<?php

namespace App\Modules\User\Notifications\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Modules\User\Notifications\Services\NotificationService;
use App\Modules\User\Notifications\Resources\UserNotificationResource;
use App\Modules\User\Notifications\Requests\IndexNotificationsRequest;
use App\Support\Exceptions\ApiException;

class NotificationsController extends Controller
{
    protected NotificationService $service;

    public function __construct(NotificationService $service)
    {
        $this->service = $service;
    }

    public function index(IndexNotificationsRequest $request)
    {
        $user = $request->user();
        $perPage = (int) $request->input('per_page', 10);
        $unreadOnly = (bool) $request->input('unread_only', false);

        $paginator = $this->service->list($user, $perPage, $unreadOnly);
        $unreadCount = $this->service->unreadCount($user);

        return response()->json([
            'success' => true,
            'message' => __('notifications.listed'),
            'data' => UserNotificationResource::collection($paginator->items()),
            'meta' => array_merge([
                'total' => $paginator->total(),
                'per_page' => $paginator->perPage(),
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'unread_count' => $unreadCount,
            ], []),
        ], 200);
    }

    public function unreadCount(Request $request)
    {
        $count = $this->service->unreadCount($request->user());
        return response()->json(['success' => true, 'message' => __('notifications.unread_count'), 'data' => ['unread_count' => $count], 'meta' => null], 200);
    }

    public function show(Request $request, $id)
    {
        $notif = $this->service->getById($request->user(), $id);
        return response()->json(['success' => true, 'message' => __('notifications.detail'), 'data' => new UserNotificationResource($notif), 'meta' => null], 200);
    }

    public function markRead(Request $request, $id)
    {
        $notif = $this->service->markRead($request->user(), $id);
        return response()->json(['success' => true, 'message' => __('notifications.marked_read'), 'data' => new UserNotificationResource($notif), 'meta' => null], 200);
    }

    public function markAllRead(Request $request)
    {
        $this->service->markAllRead($request->user());
        return response()->json(['success' => true, 'message' => __('notifications.marked_all_read'), 'data' => null, 'meta' => null], 200);
    }

    public function destroy(Request $request, $id)
    {
        $this->service->delete($request->user(), $id);
        return response()->json(['success' => true, 'message' => __('notifications.deleted'), 'data' => null, 'meta' => null], 200);
    }

    public function clearAll(Request $request)
    {
        $this->service->clearAll($request->user());
        return response()->json(['success' => true, 'message' => __('notifications.cleared'), 'data' => null, 'meta' => null], 200);
    }
}
