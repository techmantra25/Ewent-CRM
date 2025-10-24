<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\FCMService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendPushNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected array $mobiles;
    protected string $message;
    /**
     * Create a new job instance.
     */
    public function __construct(array $mobiles, string $message)
    {
        $this->mobiles = $mobiles;
        $this->message = $message;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $fcm = new FCMService();
        $sentUsers  = [];
        $failedUsers = [];
        foreach($this->mobiles as $mobile){
            $user = User::where('mobile', $mobile)->first();
            if (!$user || !$user->fcm_token) {
                $failedUsers[] = $mobile;
                continue;
            }
            try {
                $fcm->sendPushNotification(
                    $user->fcm_token,
                    'E-went',
                    $this->message,
                    ['message' => $this->message]
                );
                $sentUsers[] = $user->name ?? $mobile;
            } catch (\Exception $e) {
                $failedUsers[] = $mobile;
                Log::error('Push Notification failed for ' . $mobile . ': ' . $e->getMessage());
            }
        }

        Log::info('Push notifications processed.', [
            'sent' => $sentUsers,
            'failed' => $failedUsers,
        ]);
    }
}
