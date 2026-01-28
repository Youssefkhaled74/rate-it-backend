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
