<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\request;
use App\Models\Stock;
use App\Models\CronLog;
use App\Models\VehicleTimeline;
use App\Models\AsignedVehicle;
use App\Models\ExchangeVehicle;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Organization;
use App\Models\User;
use App\Models\OrganizationInvoice;
use App\Models\OrganizationInvoiceItem;
use App\Models\OrganizationInvoiceItemDetail;
use App\Models\PaymentLog;
use App\Models\Payment;
use App\Models\UserLocationLog;
use Illuminate\Support\Facades\Log;

class CronController extends Controller
{
    public function TestLog(){
        $logData = [
            'job_name' => 'TestCronLog',
            'url' => 'CLI or Scheduled Task',
            'request_payload' => null,
            'response' => null,
            'success' => true,
            'error_message' => 'test cron',
            'executed_at' => Carbon::now(),
        ];

        CronLog::create($logData);
    }
    public function DailyVehicleLog()
    {
        DB::beginTransaction();
        try {
            $timezone = env('APP_LOCAL_TIMEZONE', 'Asia/Kolkata'); // Default fallback
            $vehicles = Stock::select('id', 'vehicle_number', 'vehicle_track_id')->get();

            $logData = [
                'job_name' => 'DailyVehicleLog',
                'url' => request()->fullUrl() ?? url()->current() ?? 'CLI or Scheduled Task',
                'request_payload' => null,
                'response' => null,
                'success' => false,
                'error_message' => null,
                'executed_at' => now(),
            ];
            foreach ($vehicles as $item) {
                // Start and end timestamps for today in local timezone
                $startTime = Carbon::now($timezone)->startOfDay()->timestamp;
                $endTime = Carbon::now($timezone)->timestamp;

                $vehiclesUrl = 'https://app.loconav.sensorise.net/integration/api/v1/vehicles/' . $item->vehicle_track_id . '/timeline?startTime=' . $startTime . '&endTime=' . $endTime;

                $ch = curl_init($vehiclesUrl);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    "User-Authentication: " . env('LOCONAV_TOKEN'),
                    "Accept: application/json"
                ]);

                $vehiclesResponse = curl_exec($ch);
                $curlError = curl_error($ch);
                curl_close($ch);

                if ($curlError) {
                    throw new \Exception("Curl Error: " . $curlError);
                }

                $response = json_decode($vehiclesResponse, true);
               
                $todayDate = Carbon::now($timezone)->toDateString();

                // if (isset($response['success']) && $response['success'] === true && !empty($response['data']['timeline'])) {
                //     $timeline = $response['data']['timeline'];
                //     $existing = VehicleTimeline::whereDate('created_at', $todayDate)->where('field', 'timeline')
                //         ->where('stock_id', $item->id)
                //         ->first();

                //     if ($existing) {
                //         $existing->value = json_encode($timeline);
                //         $existing->save();
                //     } else {
                //         $store = new VehicleTimeline;
                //         $store->stock_id = $item->id;
                //         $store->field = 'timeline';
                //         $store->value = json_encode($timeline);
                //         $store->save();
                //     }
                // }
                
                if (isset($response['success']) && $response['success'] === true && !empty($response['data']['stats'])) {
                    // Distance
                    if(!empty($response['data']['stats']['distance'])){
                        $existing = VehicleTimeline::whereDate('created_at', $todayDate)->where('field', 'distance')
                        ->where('stock_id', $item->id)
                        ->first();
                        if ($existing) {
                            $existing->value = $response['data']['stats']['distance']['value'];
                            $existing->unit = $response['data']['stats']['distance']['unit'];
                            $existing->save();
                        } else {
                            $store = new VehicleTimeline;
                            $store->stock_id = $item->id;
                            $store->field = 'distance';
                            $store->value = $response['data']['stats']['distance']['value'];
                            $store->unit = $response['data']['stats']['distance']['unit'];
                            $store->save();
                        }
                    }

                    // runningTime
                    if(!empty($response['data']['stats']['runningTime'])){
                        $existing = VehicleTimeline::whereDate('created_at', $todayDate)->where('field', 'runningTime')
                        ->where('stock_id', $item->id)
                        ->first();
                        if ($existing) {
                            $existing->value = $response['data']['stats']['runningTime']['value'];
                            $existing->unit = $response['data']['stats']['runningTime']['unit'];
                            $existing->save();
                        } else {
                            $store = new VehicleTimeline;
                            $store->stock_id = $item->id;
                            $store->field = 'runningTime';
                            $store->value = $response['data']['stats']['runningTime']['value'];
                            $store->unit = $response['data']['stats']['runningTime']['unit'];
                            $store->save();
                        }
                    }

                    // stoppageTime
                    if(!empty($response['data']['stats']['stoppageTime'])){
                        $existing = VehicleTimeline::whereDate('created_at', $todayDate)->where('field', 'stoppageTime')
                        ->where('stock_id', $item->id)
                        ->first();
                        if ($existing) {
                            $existing->value = $response['data']['stats']['stoppageTime']['value'];
                            $existing->unit = $response['data']['stats']['stoppageTime']['unit'];
                            $existing->save();
                        } else {
                            $store = new VehicleTimeline;
                            $store->stock_id = $item->id;
                            $store->field = 'stoppageTime';
                            $store->value = $response['data']['stats']['stoppageTime']['value'];
                            $store->unit = $response['data']['stats']['stoppageTime']['unit'];
                            $store->save();
                        }
                    }

                    // offlineTime
                    if(!empty($response['data']['stats']['offlineTime'])){
                        $existing = VehicleTimeline::whereDate('created_at', $todayDate)->where('field', 'offlineTime')
                        ->where('stock_id', $item->id)
                        ->first();
                        if ($existing) {
                            $existing->value = $response['data']['stats']['offlineTime']['value'];
                            $existing->unit = $response['data']['stats']['offlineTime']['unit'];
                            $existing->save();
                        } else {
                            $store = new VehicleTimeline;
                            $store->stock_id = $item->id;
                            $store->field = 'offlineTime';
                            $store->value = $response['data']['stats']['offlineTime']['value'];
                            $store->unit = $response['data']['stats']['offlineTime']['unit'];
                            $store->save();
                        }
                    }
                    // averageSpeed
                    if(!empty($response['data']['stats']['averageSpeed'])){
                        $existing = VehicleTimeline::whereDate('created_at', $todayDate)->where('field', 'averageSpeed')
                        ->where('stock_id', $item->id)
                        ->first();
                        if ($existing) {
                            $existing->value = $response['data']['stats']['averageSpeed']['value'];
                            $existing->unit = $response['data']['stats']['averageSpeed']['unit'];
                            $existing->save();
                        } else {
                            $store = new VehicleTimeline;
                            $store->stock_id = $item->id;
                            $store->field = 'averageSpeed';
                            $store->value = $response['data']['stats']['averageSpeed']['value'];
                            $store->unit = $response['data']['stats']['averageSpeed']['unit'];
                            $store->save();
                        }
                    }
                }

                DB::commit();
            }

            $logData['success'] = true;
            $logData['response'] = 'Timeline processed successfully.';

            CronLog::create($logData);

            return response()->json([
                'status' => true,
                'message' => 'Timeline processed successfully.',
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();

            $logData['error_message'] = $e->getMessage();
            $logData['response'] = 'TSomething went wrong.';

            CronLog::create($logData);
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong. ' . $e->getMessage(),
            ], 500);
        }
    }

    public function VehiclePaymentOverDue()
    {
        DB::beginTransaction();
        try {
            $timezone = env('APP_LOCAL_TIMEZONE', 'Asia/Kolkata'); // Default fallback
            $startTime = Carbon::now($timezone);

            $Asigned = AsignedVehicle::whereIn('status', ['overdue'])
                                ->get();
            // foreach($Asigned as $ov){
            //     $data = [];
            //     sendPushNotification($ov->user_id, 'weather_update', $data);
            // }
            // return false;       
            $AsignedVehicles = AsignedVehicle::where('status', 'assigned')
                                ->where('end_date', '<', $startTime)
                                ->get();

            
            foreach ($AsignedVehicles as $item) {
                $item->status = 'overdue';
                $item->save();
            }

            $overdueVehicles = AsignedVehicle::where('status', 'overdue')
                                ->get();
            foreach($overdueVehicles as $ov){
                if($ov->order){
                    $data = [
                        'amount' =>(string)$ov->order?->rental_amount??'..',
                    ];
                    sendPushNotification($ov->user_id, 'payment_overdue', $data);
                }
            }
            $message = count($AsignedVehicles) . ' vehicle(s) marked as overdue.';

            CronLog::create([
                'job_name'         => 'VehiclePaymentOverDue',
                'url'              => request()->fullUrl(),
                'request_payload'  => json_encode([]), // if any input payload
                'response'         => $message,
                'success'          => 1,
                'error_message'    => null,
                'executed_at'      => Carbon::now(),
            ]);
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => count($AsignedVehicles) . ' vehicle(s) marked as overdue.',
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            CronLog::create([
                'job_name'         => 'VehiclePaymentOverDue',
                'url'              => request()->fullUrl(),
                'request_payload'  => json_encode([]),
                'response'         => null,
                'success'          => 0,
                'error_message'    => $e->getMessage(),
                'executed_at'      => Carbon::now(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to update vehicle statuses.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
    public function OverDueImmobilizerRequests()
    {
        DB::beginTransaction();

        try {
            $timezone = env('APP_LOCAL_TIMEZONE', 'Asia/Kolkata');
            $startTime = Carbon::now($timezone);
            $message = '';
            $payloadLog = [];
            $errors = [];

            $AsignedVehicles = AsignedVehicle::where('status', 'overdue')
                                ->where('end_date', '<', Carbon::now($timezone)->subDays(2))
                                ->get();

            foreach ($AsignedVehicles as $item) {
                if ($item->stock) {
                    $vehiclesUrl = 'https://app.loconav.sensorise.net/integration/api/v1/vehicles/' . $item->stock->vehicle_track_id . '/immobilizer_requests';
                    $payload = [
                        "value" => "IMMOBILIZE",
                    ];
                    $payloadLog[] = [
                        'vehicle_track_id' => $item->stock->vehicle_track_id,
                        'payload' => $payload,
                    ];

                    $ch = curl_init($vehiclesUrl);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, [
                        "User-Authentication: " . env('LOCONAV_TOKEN'),
                        "Accept: application/json",
                        "Content-Type: application/json"
                    ]);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
                    $vehiclesResponse = curl_exec($ch);
                    curl_close($ch);

                    $response = json_decode($vehiclesResponse, true);

                    if (isset($response['success']) && $response['success'] === true) {
                        if (!empty($response['data']['id'])) {
                            $stock = Stock::find($item->vehicle_id);
                            if ($stock) {
                                $stock->immobilizer_request_id = $response['data']['id'];
                                $stock->save();
                            }
                        }

                        if (!empty($response['data']['errors'])) {
                            $errors[] = $response['data']['errors'];
                        }

                    } else {
                        $errors[] = $response['data']['errors'][0]['message'] ?? 'Unknown error';
                    }
                }
            }

            $message = count($AsignedVehicles) . ' vehicle(s) processed for immobilizer.';

            CronLog::create([
                'job_name'         => 'OverDueImmobilizerRequests',
                'url'              => request()->fullUrl(),
                'request_payload'  => json_encode($payloadLog),
                'response'         => $message,
                'success'          => 1,
                'error_message'    => !empty($errors) ? json_encode($errors) : null,
                'executed_at'      => Carbon::now(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $message,
            ]);

        } catch (\Exception $e) {
            DB::rollback();

            CronLog::create([
                'job_name'         => 'OverDueImmobilizerRequests',
                'url'              => request()->fullUrl(),
                'request_payload'  => json_encode([]),
                'response'         => null,
                'success'          => 0,
                'error_message'    => $e->getMessage(),
                'executed_at'      => Carbon::now(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to send immobilizer requests.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    // Daily Invoice generate for Organization
    public function generateOrganizationInvoice() {
        $timezone = env('APP_LOCAL_TIMEZONE', 'Asia/Kolkata');
        
        $today_date = Carbon::now($timezone)->day;
        $today_day  = Carbon::now($timezone)->format('l');

        $summary = [
            'monthly' => ['organizations' => 0, 'invoices' => 0],
            'weekly'  => ['organizations' => 0, 'invoices' => 0],
            'custom'  => ['organizations' => 0, 'invoices' => 0],
        ];
        // 1. Monthly Subscription
        $monthlySubscriber = Organization::where('subscription_type', 'monthly')
            ->where('renewal_day_of_month', $today_date)
            ->get();

        if ($monthlySubscriber->count() > 0) {
            $summary['monthly']['organizations'] = $monthlySubscriber->count();

            foreach ($monthlySubscriber as $org) {
                $latestInvoice = OrganizationInvoice::where('organization_id', $org->id)
                    ->orderBy('id', 'desc')
                    ->first();

                $invoice_start_date = $latestInvoice && $latestInvoice->billing_end_date
                    ? Carbon::parse($latestInvoice->billing_end_date)->addDay()
                    : Carbon::parse($org->created_at);

                $invoice_end_date = Carbon::now($timezone);
                $due_date = Carbon::parse($invoice_end_date)
                    ->addDays(config('subscription.monthly_due_period'))
                    ->toDateString();

                $invoice = createInvoiceForOrganization($org->id, 'monthly', $invoice_start_date, $invoice_end_date, $due_date);

                if ($invoice) {
                    $summary['monthly']['invoices']++;
                }
            }
        }

        // 2. Weekly Subscription
        $weeklySubscriber = Organization::where('subscription_type', 'weekly')
            ->where('renewal_day', strtolower($today_day))
            ->get();
        if ($weeklySubscriber->count() > 0) {
            $summary['weekly']['organizations'] = $weeklySubscriber->count();

            foreach ($weeklySubscriber as $org) {
                $latestInvoice = OrganizationInvoice::where('organization_id', $org->id)
                    ->orderBy('id', 'desc')
                    ->first();

                $invoice_start_date = $latestInvoice && $latestInvoice->billing_end_date
                    ? Carbon::parse($latestInvoice->billing_end_date)->addDay()
                    : Carbon::parse($org->created_at);

                $invoice_end_date = Carbon::now($timezone);
                if ($invoice_start_date->greaterThan($invoice_end_date)) {
                    continue; // skip this org
                }
                $due_date = Carbon::parse($invoice_end_date)
                    ->addDays(config('subscription.weekly_due_period'))
                    ->toDateString();

                $invoice = createInvoiceForOrganization($org->id, 'weekly', $invoice_start_date, $invoice_end_date, $due_date);

                if ($invoice) {
                    $summary['weekly']['invoices']++;
                }
            }
        }

        // 3. Custom Subscription
        $customSubscriber = Organization::where('subscription_type', 'custom')
            ->whereNotNull('renewal_interval_days')
            ->get();

        if ($customSubscriber->count() > 0) {
            $summary['custom']['organizations'] = $customSubscriber->count();

            foreach ($customSubscriber as $org) {
                $latestInvoice = OrganizationInvoice::where('organization_id', $org->id)
                    ->orderBy('id', 'desc')
                    ->first();
                if($latestInvoice){
                    $today = Carbon::now($timezone)->startOfDay();
                    // Last invoice date
                    $lastInvoiceDate = Carbon::parse($latestInvoice->created_at, $timezone)->startOfDay();
                    
                    // Next renewal date = last invoice date + interval days
                    $nextRenewalDate = $lastInvoiceDate->copy()->addDays((int) $org->renewal_interval_days);
                    // dd($nextRenewalDate);
                    // ✅ If today matches renewal date
                    if ($nextRenewalDate->equalTo($today)) {
                    } else {
                        continue; // date match na → skip
                    }
                }
                

                $invoice_start_date = $latestInvoice && $latestInvoice->billing_end_date
                    ? Carbon::parse($latestInvoice->billing_end_date)->addDay()
                    : Carbon::parse($org->created_at);

                $invoice_end_date = Carbon::now($timezone);
                $due_date = Carbon::parse($invoice_end_date)
                    ->addDays(config('subscription.custom_due_period'))
                    ->toDateString();

                $invoice = createInvoiceForOrganization($org->id, 'custom', $invoice_start_date, $invoice_end_date, $due_date);

                if ($invoice) {
                    $summary['custom']['invoices']++;
                }
            }
        }

        return $summary; // Return executed summary
    }

    public function removeOldCronLogs()
    {
        $cutoffDate = Carbon::now()->subDays(15);

        $deleted = CronLog::where('created_at', '<', $cutoffDate)->delete();

        return response()->json([
            'status' => 'success',
            'message' => "Old cron logs removed successfully",
            'deleted_count' => $deleted
        ]);
    }
    public function removeVehicleTimeline(){
        $cutoffDate = Carbon::now()->subDays(30);

        $deleted = VehicleTimeline::where('created_at','<',$cutoffDate)->delete();

        return response()->json([
            'status'=>'success',
            'message'=>'Old vehicle timeline removed successfully',
            'deleted_count'=>$deleted,
        ]);
    }
    public function removePaymentLog(){
        $cutoffDate = Carbon::now()->subDays(90);

        $deleted = PaymentLog::where('created_at','<',$cutoffDate)->delete();

        return response()->json([
            'status'=>'success',
            'message'=>'Old peyment log older than 90 days have been removed succesfully.',
            'deleted_count'=>$deleted,
        ]);
    }
    public function removeUserLocationLog(){
        $cutoffDate = Carbon::now()->subDays(30);

        $deleted = UserLocationLog::where('created_at','<',$cutoffDate)->delete();

        return response()->json([
            'status'=>'success',
            'message'=>'Old user location log older than 30 days have been removed succesfully.',
            'deleted_count'=>$deleted,
        ]);
    }
    // will run 11.50pm every day
    public function updateOverdueOrgInvoices(){
         $today = Carbon::today()->toDateString();

        $userIds = DB::transaction(function () use ($today) {
            // Fetch pending invoices with their organizations & users
            $invoices = OrganizationInvoice::with('organization.user:id,organization_id') 
                ->where('status', 'pending')
                ->whereDate('due_date', '<=', $today)
                ->get();

            // Collect all user IDs from related organizations
            $userIds = $invoices->flatMap(function ($invoice) {
                return $invoice->organization->user->pluck('id');
            })->unique()->values();
            // Update the invoices to overdue
            OrganizationInvoice::whereIn('id', $invoices->pluck('id'))
                ->update(['status' => 'overdue']);

            return $userIds;
        });
        if(count($userIds)>0){
            $this->immobilizeOverdueORGRiders($userIds);
        }
       
        return response()->json([
            'message' => 'Overdue invoices updated successfully',
            'users'   => $userIds,
            'count'   => $userIds->count(),
        ]);
    }
    
    protected function immobilizeOverdueOrgRiders($userIds)
    {
        $timezone = env('APP_LOCAL_TIMEZONE', 'Asia/Kolkata');
        $startTime = Carbon::now($timezone);
        $payloadLog = [];
        $errors = [];

        $assignedVehicles = AsignedVehicle::with('stock')
            ->whereIn('user_id', $userIds)
            ->where('status', 'assigned')
            ->get();

        foreach ($assignedVehicles as $item) {
            if (!$item->stock || !$item->stock->vehicle_track_id) {
                $errors[] = "Missing vehicle_track_id for assigned vehicle {$item->id}";
                continue;
            }

            $vehiclesUrl = 'https://app.loconav.sensorise.net/integration/api/v1/vehicles/' .
                $item->stock->vehicle_track_id . '/immobilizer_requests';
            $payload = ["value" => "IMMOBILIZE"];
            $payloadLog[] = [
                'vehicle_track_id' => $item->stock->vehicle_track_id,
                'payload' => $payload,
            ];

            try {
                $ch = curl_init($vehiclesUrl);
                curl_setopt_array($ch, [
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_POST => true,
                    CURLOPT_HTTPHEADER => [
                        "User-Authentication: " . env('LOCONAV_TOKEN'),
                        "Accept: application/json",
                        "Content-Type: application/json"
                    ],
                    CURLOPT_POSTFIELDS => json_encode($payload),
                    CURLOPT_TIMEOUT => 30,
                ]);
                $vehiclesResponse = curl_exec($ch);

                if ($vehiclesResponse === false) {
                    throw new \Exception(curl_error($ch));
                }
                curl_close($ch);

                $response = json_decode($vehiclesResponse, true);

                if (isset($response['success']) && $response['success'] === true) {
                    if (!empty($response['data']['id'])) {
                        DB::transaction(function () use ($item, $response) {
                            $stock = Stock::find($item->vehicle_id);
                            if ($stock) {
                                $stock->immobilizer_request_id = $response['data']['id'];
                                $stock->save();
                            }
                        });
                    }

                    if (!empty($response['data']['errors'])) {
                        $errors[] = $response['data']['errors'];
                    }
                } else {
                    $errors[] = $response['data']['errors'][0]['message'] ?? 'Unknown error';
                }
            } catch (\Throwable $e) {
                $errors[] = $e->getMessage();
            }
        }

        $message = $assignedVehicles->count() . ' vehicle(s) processed for immobilizer.';

        CronLog::create([
            'job_name'        => 'immobilizeOverdueOrgRiders',
            'url'             => request()->fullUrl(),
            'request_payload' => json_encode($payloadLog),
            'response'        => $message,
            'success'         => empty($errors) ? 1 : 0,
            'error_message'   => !empty($errors) ? json_encode($errors) : null,
            'executed_at'     => Carbon::now(),
        ]);
    }


    // For just Testing

    // public function paymentAmountUpdate()
    // {
    //     try {

    //         // -------------------------------
    //         // 1️⃣ Renewal orders: rental = amount, deposit = 0
    //         // -------------------------------
    //         $renewalUpdated = Payment::where('order_type', 'like', 'renewal_%')
    //             ->update([
    //                 'deposit_amount' => 0.00,
    //                 'rental_amount'  => DB::raw('amount'),
    //             ]);

    //         // -------------------------------
    //         // 2️⃣ New subscriptions: split deposit + rental
    //         // -------------------------------
    //         $processed = 0;
    //         $skipped   = 0;

    //         DB::transaction(function () use (&$processed, &$skipped) {

    //             Payment::with(['order.subscription'])
    //                 ->where('order_type', 'like', 'new_subscription%')
    //                 ->where(function ($q) {
    //                     $q->whereNull('deposit_amount')
    //                     ->orWhere('deposit_amount', 0)
    //                     ->orWhereNull('rental_amount')
    //                     ->orWhere('rental_amount', 0);
    //                 })
    //                 ->chunkById(200, function ($payments) use (&$processed, &$skipped) {

    //                     Log::info('Chunk fetched', ['count' => $payments->count()]);

    //                     foreach ($payments as $item) {

    //                         // ---- Safety checks ----
    //                         if (
    //                             !$item->order ||
    //                             !$item->order->subscription ||
    //                             $item->amount === null
    //                         ) {
    //                             Log::warning('Skipped payment ID '.$item->id.' due to missing relations or amount.');
    //                             $skipped++;
    //                             continue;
    //                         }

    //                         $subscriptionDeposit = (float) $item->order->subscription->deposit_amount;
    //                         $amount              = (float) $item->amount;

    //                         // Prevent negative rental values
    //                         $rentalAmount  = max(0, $amount - $subscriptionDeposit);
    //                         $depositAmount = $subscriptionDeposit;

    //                         $item->deposit_amount = $depositAmount;
    //                         $item->rental_amount  = $rentalAmount;
    //                         $item->save();

    //                         $processed++;
    //                     }
    //                 });
    //         });

    //         // -------------------------------
    //         // ✅ Success response
    //         // -------------------------------
    //         return response()->json([
    //             'status'            => true,
    //             'message'           => 'Payment amounts updated successfully.',
    //             'renewals_updated'  => $renewalUpdated,
    //             'new_processed'     => $processed,
    //             'new_skipped'       => $skipped,
    //         ]);

    //     } catch (\Throwable $e) {

    //         // -------------------------------
    //         // ❌ Log full error for devs
    //         // -------------------------------
    //         Log::error('Payment amount update failed', [
    //             'message' => $e->getMessage(),
    //             'file'    => $e->getFile(),
    //             'line'    => $e->getLine(),
    //             'trace'   => $e->getTraceAsString(),
    //         ]);

    //         // -------------------------------
    //         // ❌ Return clean error to API
    //         // -------------------------------
    //         return response()->json([
    //             'status'  => false,
    //             'message' => 'Payment amount update failed.',
    //             'error'   => $e->getMessage(), // ⚠️ hide in production if sensitive
    //         ], 500);
    //     }
    // }

    //    public function ActiveVehicleAmountUpdate()
    //     {
    //         try {

    //             $processed = 0;
    //             $skipped   = 0;

    //             ExchangeVehicle::
    //             // whereHas('order.product', function ($q) {
    //             //         $q->where('id', '!=', 7);   // product_id != 7
    //             //     })
    //             //     ->with(['order.product'])
    //                 chunkById(200, function ($vehicles) use (&$processed, &$skipped) {

    //                     Log::info('AssignedVehicle chunk fetched', ['count' => $vehicles->count()]);

    //                     foreach ($vehicles as $item) {

    //                         // ---- Safety checks ----
    //                         if (
    //                             !$item->order ||
    //                             !$item->order->product
    //                         ) {
    //                             Log::warning('Skipped assigned_vehicle ID '.$item->id.' due to missing relations.');
    //                             $skipped++;
    //                             continue;
    //                         }
    //                         $item->amount         = (float) $item->order->final_amount;
    //                         $item->deposit_amount = (float) $item->order->deposit_amount;
    //                         $item->rental_amount  = (float) $item->order->rental_amount;
    //                         $item->save();

    //                         $processed++;
    //                     }
    //                 });

    //             return response()->json([
    //                 'status'    => true,
    //                 'message'   => 'Active vehicle amounts processed successfully.',
    //                 'processed' => $processed,
    //                 'skipped'   => $skipped,
    //             ]);

    //         } catch (\Throwable $e) {

    //             Log::error('ActiveVehicleAmountUpdate failed', [
    //                 'message' => $e->getMessage(),
    //                 'file'    => $e->getFile(),
    //                 'line'    => $e->getLine(),
    //                 'trace'   => $e->getTraceAsString(),
    //             ]);

    //             return response()->json([
    //                 'status'  => false,
    //                 'message' => 'Active vehicle amount update failed.',
    //                 'error'   => $e->getMessage(), // ⚠️ hide in prod
    //             ], 500);
    //         }
    //     }



}
