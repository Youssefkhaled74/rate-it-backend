<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin;
use App\Services\Admin\AdminManagementService;
use App\Http\Requests\Admin\StoreAdminRequest;
use App\Http\Requests\Admin\UpdateAdminRequest;

class AdminsController extends Controller
{
    protected AdminManagementService $service;

    public function __construct(AdminManagementService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', Admin::class);
        $filters = $request->only(['q','status']);
        $admins = $this->service->paginateAdmins($filters, 15);
        return view('admin.admins.index', compact('admins','filters'));
    }

    public function create()
    {
        $this->authorize('create', Admin::class);
        return view('admin.admins.create');
    }

    public function store(StoreAdminRequest $request)
    {
        $this->authorize('create', Admin::class);
        $admin = $this->service->createAdmin($request->validated());
        return redirect()->route('admin.admins.index')->with('success', 'Admin created');
    }

    public function edit(Admin $admin)
    {
        $this->authorize('update', $admin);
        return view('admin.admins.edit', compact('admin'));
    }

    public function update(UpdateAdminRequest $request, Admin $admin)
    {
        $this->authorize('update', $admin);
        $this->service->updateAdmin($admin, $request->validated());
        return redirect()->route('admin.admins.index')->with('success', 'Admin updated');
    }

    public function toggle(Request $request, Admin $admin)
    {
        $this->authorize('toggle', $admin);
        $by = auth()->guard('admin_web')->user();
        try {
            $this->service->toggleAdmin($admin, $by);
            return back()->with('success','Admin toggled');
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function destroy(Request $request, Admin $admin)
    {
        $this->authorize('delete', $admin);
        $by = auth()->guard('admin_web')->user();
        $this->service->deleteAdmin($admin, $by);
        return redirect()->route('admin.admins.index')->with('success','Admin deleted');
    }
}
