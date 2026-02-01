<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Services\Admin\UsersService;

class UsersController extends Controller
{
    protected UsersService $service;

    public function __construct(UsersService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        // dd([
        //     'default_guard_user' => auth()->check() ? get_class(auth()->user()) : null,
        //     'default_guard_id' => auth()->id(),
        //     'admin_web_user' => auth('admin_web')->check() ? get_class(auth('admin_web')->user()) : null,
        //     'admin_web_id' => auth('admin_web')->id(),
        //     'admin_role' => auth('admin_web')->user()->role ?? null,
        // ]);

        $this->authorize('viewAny', User::class);
        $filters = $request->only(['q']);
        $users = $this->service->listUsers($filters, 15);
        return view('admin.users.index', compact('users','filters'));
    }

    public function show(User $user)
    {
        $this->authorize('view', $user);
        $data = $this->service->getUserProfile($user);
        return view('admin.users.show', $data);
    }
}
