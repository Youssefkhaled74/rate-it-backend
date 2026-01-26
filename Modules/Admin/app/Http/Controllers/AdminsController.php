<?php

namespace Modules\Admin\app\Http\Controllers;

use Modules\Admin\app\Http\Requests\AdminStoreRequest;
use Modules\Admin\app\Http\Requests\AdminUpdateRequest;
use Modules\Admin\app\Models\Admin;
use Modules\Admin\app\Services\AdminService;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class AdminsController extends Controller
{
    public function __construct(protected AdminService $adminService)
    {
    }

    /**
     * Display a listing of admins.
     */
    public function index(): View
    {
        $this->authorize('viewAny', Admin::class);

        $search = request('search');
        $status = request('status');
        $admins = $this->adminService->getPaginatedAdmins($search, $status);
        $stats = $this->adminService->getStatistics();

        return view('admin::admins.index', [
            'admins' => $admins,
            'stats' => $stats,
            'search' => $search,
            'status' => $status,
        ]);
    }

    /**
     * Show the form for creating a new admin.
     */
    public function create(): View
    {
        $this->authorize('create', Admin::class);

        return view('admin::admins.create');
    }

    /**
     * Store a newly created admin.
     */
    public function store(AdminStoreRequest $request): RedirectResponse
    {
        $this->authorize('create', Admin::class);

        $this->adminService->createAdmin($request->validated());

        return redirect()->route('admin.admins.index')
            ->with('success', __('admin.admin_created'));
    }

    /**
     * Show the form for editing an admin.
     */
    public function edit(Admin $admin): View
    {
        $this->authorize('update', $admin);

        return view('admin::admins.edit', ['admin' => $admin]);
    }

    /**
     * Update the specified admin.
     */
    public function update(AdminUpdateRequest $request, Admin $admin): RedirectResponse
    {
        $this->authorize('update', $admin);

        $this->adminService->updateAdmin($admin, $request->validated());

        return redirect()->route('admin.admins.index')
            ->with('success', __('admin.admin_updated'));
    }

    /**
     * Deactivate admin (toggle status).
     */
    public function deactivate(Admin $admin): RedirectResponse
    {
        $this->authorize('deactivate', $admin);

        try {
            $this->adminService->deactivateAdmin($admin);

            return redirect()->back()
                ->with('success', __('admin.admin_deactivated'));
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Activate admin.
     */
    public function activate(Admin $admin): RedirectResponse
    {
        $this->authorize('update', $admin);

        $this->adminService->activateAdmin($admin);

        return redirect()->back()
            ->with('success', __('admin.admin_activated'));
    }

    /**
     * Delete admin.
     */
    public function destroy(Admin $admin): RedirectResponse
    {
        $this->authorize('delete', $admin);

        try {
            $this->adminService->deleteAdmin($admin);

            return redirect()->route('admin.admins.index')
                ->with('success', __('admin.admin_deleted'));
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }
}
