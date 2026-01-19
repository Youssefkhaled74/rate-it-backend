<?php

namespace App\Modules\User\Invites\Services;

use App\Models\User;
use App\Models\Invite;
use App\Models\PointsSetting;
use App\Models\PointsTransaction;
use App\Support\PhoneNormalizer;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InviteService
{
    public function checkPhones(array $phones): array
    {
        $normalized = collect($phones)
            ->map(fn($p) => PhoneNormalizer::normalize($p))
            ->filter()
            ->unique()
            ->values()
            ->all();

        $users = User::whereIn('phone', $normalized)->get(['id','name','phone','email']);
        $installed = $users->map(function($u){ return ['id'=>$u->id,'name'=>$u->name,'phone'=>$u->phone,'email'=>$u->email]; })->all();

        $installedPhones = $users->pluck('phone')->all();
        $notInstalled = array_values(array_diff($normalized, $installedPhones));

        return ['installed' => $installed, 'not_installed' => $notInstalled];
    }

    public function createInvites(User $inviter, array $phones): array
    {
        $res = ['created'=>[], 'skipped'=>[]];
        $setting = PointsSetting::where('is_active', true)->orderBy('created_at','desc')->first();
        $reward = $setting->invite_points_per_friend ?? 50;

        $phonesNormalized = collect($phones)
            ->map(fn($p) => PhoneNormalizer::normalize($p))
            ->filter()
            ->unique()
            ->values();

        DB::beginTransaction();
        try {
            foreach ($phonesNormalized as $phone) {
                // skip if already registered
                $existsUser = User::where('phone', $phone)->exists();
                if ($existsUser) {
                    $res['skipped'][] = ['phone'=>$phone,'reason'=>'installed'];
                    continue;
                }

                // create or ignore duplicate invites
                try {
                    $invite = Invite::create([
                        'inviter_user_id' => $inviter->id,
                        'invited_phone' => $phone,
                        'status' => 'pending',
                        'reward_points' => $reward,
                    ]);

                    Log::debug('invite.created', ['inviter_id'=>$inviter->id,'invited_phone'=>$phone,'invite_id'=>$invite->id,'points'=>$reward]);
                    $res['created'][] = $invite->toArray();
                } catch (\Exception $e) {
                    // likely unique constraint - skip
                    Log::debug('invite.create_failed', ['phone'=>$phone,'error'=>$e->getMessage()]);
                    $res['skipped'][] = ['phone'=>$phone,'reason'=>'already_invited'];
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        return $res;
    }

    public function listInvites(User $user)
    {
        return Invite::where('inviter_user_id', $user->id)->orderBy('created_at','desc')->get();
    }

    public function completeInviteForNewUser(User $newUser, ?string $invitedByPhone = null): void
    {
        if (! $invitedByPhone) {
            return;
        }

        $inviterPhone = PhoneNormalizer::normalize($invitedByPhone);
        if (! $inviterPhone) return;

        $inviter = User::where('phone', $inviterPhone)->first();
        if (! $inviter) return;

        $invite = Invite::where('inviter_user_id', $inviter->id)
            ->where('invited_phone', $newUser->phone)
            ->whereNull('rewarded_at')
            ->first();

        if (! $invite) return;

        $setting = PointsSetting::where('is_active', true)->orderBy('created_at','desc')->first();
        $points = $invite->reward_points ?? ($setting->invite_points_per_friend ?? 50);

        DB::transaction(function() use ($invite, $newUser, $inviter, $points) {
            $invite->status = 'joined';
            $invite->invited_user_id = $newUser->id;
            $invite->rewarded_at = Carbon::now();
            $invite->save();

            PointsTransaction::create([
                'user_id' => $inviter->id,
                'brand_id' => null,
                'type' => 'EARN_INVITE',
                'points' => (int) $points,
                'source_type' => 'invite',
                'source_id' => $invite->id,
                'meta' => ['invited_user_id'=>$newUser->id],
                'expires_at' => null,
            ]);

            Log::debug('invite.redeemed', ['inviter_id'=>$inviter->id,'invited_phone'=>$invite->invited_phone,'invite_id'=>$invite->id,'points'=>$points]);
        });
    }
}
