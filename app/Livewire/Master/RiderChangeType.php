<?php

namespace App\Livewire\Master;

use Livewire\Component;
use App\Models\User;
use App\Models\Organization;
use App\Models\AsignedVehicle;
use App\Models\UserKycLog;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\PersonalAccessToken;

class RiderChangeType extends Component
{
    public $rider_mobile_or_email;
    public $rider;
    public $rider_type;
    public $organizations = [];
    public $organization_id;

    /**
     * Auto reset when input changes
     */
    public function updatedRiderMobileOrEmail()
    {
        $this->resetRiderData();
    }

    public function resetRiderData()
    {
        $this->rider = null;
        $this->rider_type = null;
        $this->organization_id = null;
        $this->organizations = [];
    }

    public function searchRider()
    {
        $this->validate([
            'rider_mobile_or_email' => 'required'
        ]);

        $this->rider = User::where('mobile', $this->rider_mobile_or_email)
            ->orWhere('email', $this->rider_mobile_or_email)
            ->first();

        if (!$this->rider) {
            session()->flash('error', 'Rider not found');
            return;
        }

        // Opposite type
        $this->rider_type = $this->rider->user_type === 'B2B' ? 'B2C' : 'B2B';

        // Load org if needed
        if ($this->rider_type === 'B2B') {
            $this->organizations = Organization::where('status', 1)->get();
        }
    }

   public function updateRiderType()
    {
        if (!$this->rider) {
            session()->flash('error', 'Rider not found');
            return;
        }

        //  BLOCK: assigned / overdue vehicle
        if ($this->rider->active_vehicle()->exists()) {
            session()->flash('error', 'Rider has assigned/overdue vehicle. Type change not allowed.');
            return;
        }

        $rules = [
            'rider_type' => 'required'
        ];

        if ($this->rider_type === 'B2B') {
            $rules['organization_id'] = 'required';
        }

        $this->validate($rules);

        DB::beginTransaction();

        try {

            $oldEmail = $this->rider->email;
            $oldMobile = $this->rider->mobile;
            $oldUserId = $this->rider->id;

            //  Get logged-in admin/user
            $adminId = auth()->id();
            $adminName = auth()->user()->name ?? 'System';

            // Modify old unique fields
            $this->rider->update([
                'email' => $oldEmail ? $oldEmail . '_old_' . time() : null,
                'mobile' => $oldMobile . '_old_' . time(),
            ]);

            // Clone user
            $newUser = $this->rider->replicate();

            $newUser->user_type = $this->rider_type;
            $newUser->organization_id = $this->rider_type === 'B2B'
                ? $this->organization_id
                : null;

            $newUser->email = $oldEmail;
            $newUser->mobile = $oldMobile;

            $newUser->save();

            // Clone KYC logs
            foreach ($this->rider->doc_logs as $log) {
                $newLog = $log->replicate();
                $newLog->user_id = $newUser->id;
                $newLog->save();
            }

            // Soft delete old user
            $this->rider->delete();
            PersonalAccessToken::where('tokenable_id', $this->rider->id)
            ->where('tokenable_type', User::class)
            ->delete();

            DB::commit();

            // SUCCESS LOG
            Log::info('Rider type changed via clone', [
                'performed_by_id' => $adminId,
                'performed_by_name' => $adminName,
                'old_user_id' => $oldUserId,
                'new_user_id' => $newUser->id,
                'old_type' => $this->rider->user_type,
                'new_type' => $this->rider_type,
                'organization_id' => $this->organization_id,
                'timestamp' => now()->toDateTimeString(),
            ]);

            session()->flash('success', 'Rider recreated with new type successfully');

            $this->resetRiderData();
            $this->rider_mobile_or_email = '';

        } catch (\Exception $e) {

            DB::rollBack();

            //  ERROR LOG
            Log::error('Rider type change failed', [
                'performed_by_id' => auth()->id(),
                'rider_id' => $this->rider->id ?? null,
                'error_message' => $e->getMessage(),
                'timestamp' => now()->toDateTimeString(),
            ]);

            session()->flash('error', 'Failed: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.master.rider-change-type');
    }
}