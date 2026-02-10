<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PointsSetting;
use App\Models\UserLevel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RewardsController extends Controller
{
    public function index()
    {
        $activeSetting = PointsSetting::where('is_active', true)->orderByDesc('version')->first();
        $settings = PointsSetting::orderByDesc('version')->paginate(10);
        $levels = UserLevel::orderBy('min_reviews')->orderBy('id')->get();

        return view('admin.rewards.index', compact('activeSetting', 'settings', 'levels'));
    }

    public function storeSettings(Request $request)
    {
        $data = $request->validate([
            'points_per_review' => ['required', 'integer', 'min:0'],
            'invite_points_per_friend' => ['required', 'integer', 'min:0'],
            'invitee_bonus_points' => ['required', 'integer', 'min:0'],
            'point_value_money' => ['required', 'numeric', 'min:0'],
            'currency' => ['required', 'string', 'size:3'],
            'points_expiry_days' => ['nullable', 'integer', 'min:0'],
            'activate_now' => ['nullable', 'boolean'],
        ]);

        $maxVersion = PointsSetting::max('version') ?? 0;
        $data['version'] = $maxVersion + 1;
        $data['is_active'] = false;

        $admin = auth()->guard('admin_web')->user();
        if ($admin) $data['created_by_admin_id'] = $admin->id;

        $setting = PointsSetting::create($data);

        if ($request->boolean('activate_now')) {
            $this->activateSetting($setting->id);
        }

        return redirect()
            ->route('admin.rewards.index')
            ->with('success', __('admin.rewards_setting_created'));
    }

    public function activateSettings(PointsSetting $setting)
    {
        $this->activateSetting($setting->id);

        return redirect()
            ->route('admin.rewards.index')
            ->with('success', __('admin.rewards_setting_activated'));
    }

    public function storeLevel(Request $request)
    {
        $data = $this->validateLevel($request);
        UserLevel::create($data);

        return redirect()
            ->route('admin.rewards.index')
            ->with('success', __('admin.level_created'));
    }

    public function editLevel(UserLevel $level)
    {
        $benefitsText = $this->benefitsToText($level->benefits);
        return view('admin.rewards.level-edit', compact('level', 'benefitsText'));
    }

    public function updateLevel(Request $request, UserLevel $level)
    {
        $data = $this->validateLevel($request);
        $level->update($data);

        return redirect()
            ->route('admin.rewards.index')
            ->with('success', __('admin.level_updated'));
    }

    public function destroyLevel(UserLevel $level)
    {
        $level->delete();

        return redirect()
            ->route('admin.rewards.index')
            ->with('success', __('admin.level_deleted'));
    }

    private function validateLevel(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'min_reviews' => ['required', 'integer', 'min:0'],
            'benefits_text' => ['nullable', 'string'],
        ]);

        $benefits = [];
        $lines = preg_split('/\r\n|\r|\n/', (string) ($data['benefits_text'] ?? ''));
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line !== '') $benefits[] = $line;
        }

        unset($data['benefits_text']);
        $data['benefits'] = $benefits;

        return $data;
    }

    private function benefitsToText($benefits): string
    {
        if (!is_array($benefits)) return '';
        return implode("\n", $benefits);
    }

    private function activateSetting(int $id): void
    {
        $admin = auth()->guard('admin_web')->user();
        $adminId = $admin ? $admin->id : null;

        DB::transaction(function () use ($id, $adminId) {
            DB::table('points_settings')->update(['is_active' => false]);
            DB::table('points_settings')->where('id', $id)->update([
                'is_active' => true,
                'activated_by_admin_id' => $adminId,
                'activated_at' => now(),
            ]);
        });
    }
}
