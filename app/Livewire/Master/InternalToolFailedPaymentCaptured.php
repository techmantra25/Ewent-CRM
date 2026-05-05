<?php

namespace App\Livewire\Master;

use Livewire\Component;
use App\Models\OrderMerchantNumber;
use App\Models\OrgInvoiceMerchantNumber;
use App\Models\OrgDepositInvoiceMerchantNumber;
use App\Models\Payment;
use App\Models\OrganizationPayment;
use App\Models\OrganizationDepositPayment;
use App\Models\Stock;
use App\Models\OrganizationInvoice;
use App\Models\OrganizationDepositInvoice;

use App\Models\Order;
use App\Models\PaymentItem;
use App\Models\AsignedVehicle;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Livewire\WithPagination;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Http;

class InternalToolFailedPaymentCaptured extends Component
{   
    public $payment_type;
    public $merchant_ref;
    public $transaction_id;

    public $result = null;
    public $finalStatus = null;
    public $is_success = false;
    public $errorMessage = null;
    public $successMessage = null;
    public $isactiveTransactionNumber = true;

    // Reset when payment type changes
    public function updatedPaymentType()
    {
        $this->reset([
            'result',
            'errorMessage',
            'successMessage',
            'isactiveTransactionNumber',
            'is_success',
            'finalStatus',
        ]);
    }
    public function resetFormData()
    {
        $this->reset([
            'result',
            'errorMessage',
            'successMessage',
            'is_success',
            'finalStatus',
        ]);
    }

    public function searchPayment()
    {
        // Reset messages only
        $this->is_success = false;
        $this->finalStatus = null;
        $this->successMessage = null;
        $this->successMessage = null;

        //  Dynamic validation
        $rules = [
            'payment_type' => 'required',
            'merchant_ref' => 'required',
            'transaction_id' => 'required',
        ];

        $this->validate($rules);
        
        $ref = trim($this->merchant_ref);

        // Fetch Data
        if ($this->payment_type === "rider_subscription") {
            if (!empty($this->transaction_id)) {

                $existingMerchantTxnNo = Payment::where('icici_merchantTxnNo', $this->merchant_ref)->where('payment_status', '!=', 'completed')->first();
                if(!$existingMerchantTxnNo){
                    $this->successMessage = null;
                    $this->result = null;
                    $this->errorMessage = "This Merchant Ref may already be linked to another Transaction ID, or no transaction was initiated for it.<br>Merchant Ref: {$this->merchant_ref}.";
                    return;
                }

                $existingPayment = Payment::where('icici_txnID', $this->transaction_id)->first();

                if ($existingPayment && $existingPayment->payment_status=="completed") {
                    $this->successMessage = null;
                    $this->result = null;

                    $status = ucfirst($existingPayment->payment_status ?? 'unknown');
                    $merchantRef = $existingPayment->icici_merchantTxnNo ?? 'unknown';

                    $this->errorMessage = "Transaction ID '{$this->transaction_id}' already exists with status: {$status}.<br>Merchant Ref: {$merchantRef}.";

                    return;
                }
            }
            $data = OrderMerchantNumber::with('order.user')
                ->where('merchantTxnNo', $ref)
                ->first();

        } elseif ($this->payment_type === "organization_subscription") {
            if (!empty($this->transaction_id)) {

                $existingMerchantTxnNo = OrganizationPayment::where('icici_merchantTxnNo', $this->merchant_ref)->where('payment_status', '!=', 'success')->first();

                if(!$existingMerchantTxnNo){
                    $this->successMessage = null;
                    $this->result = null;
                    $this->errorMessage = "This Merchant Ref may already be linked to another Transaction ID, or no transaction was initiated for it.<br>Merchant Ref: {$this->merchant_ref}.";
                    return;
                }

                $existingPayment = OrganizationPayment::where('icici_txnID', $this->transaction_id)->first();
                
                if ($existingPayment && $existingPayment->payment_status=="success") {
                    $this->successMessage = null;
                    $this->result = null;

                    $status = ucfirst($existingPayment->payment_status ?? 'unknown');
                    $merchantRef = $existingPayment->icici_merchantTxnNo ?? 'unknown';

                    $this->errorMessage = "Transaction ID '{$this->transaction_id}' already exists with status: {$status}.<br>Merchant Ref: {$merchantRef}.";

                    return;
                }
            }
            $data = OrgInvoiceMerchantNumber::with('organization')
                ->where('merchantTxnNo', $ref)
                ->first();

        } elseif ($this->payment_type === "organization_deposit") {
            if (!empty($this->transaction_id)) {

                $existingMerchantTxnNo = OrganizationDepositPayment::where('icici_merchantTxnNo', $this->merchant_ref)->where('payment_status', '!=', 'success')->first();
                if(!$existingMerchantTxnNo){
                    $this->successMessage = null;
                    $this->result = null;
                    $this->errorMessage = "This Merchant Ref may already be linked to another Transaction ID, or no transaction was initiated for it.<br>Merchant Ref: {$this->merchant_ref}.";
                    return;
                }

                $existingPayment = OrganizationDepositPayment::where('icici_txnID', $this->transaction_id)->first();

                if ($existingPayment && $existingPayment->payment_status=="success") {
                    $this->successMessage = null;
                    $this->result = null;

                    $status = ucfirst($existingPayment->payment_status ?? 'unknown');
                    $merchantRef = $existingPayment->icici_merchantTxnNo ?? 'unknown';

                    $this->errorMessage = "Transaction ID '{$this->transaction_id}' already exists with status: {$status}.<br>Merchant Ref: {$merchantRef}.";

                    return;
                }
            }
            $data = OrgDepositInvoiceMerchantNumber::with('organization')
                ->where('merchantTxnNo', $ref)
                ->first();

        } else {
            $this->successMessage = null;
            $this->errorMessage = "Invalid payment type.";
            return;
        }

        //  Not Found
        if (!$data) {
            $this->successMessage = null;
            $this->errorMessage = "No record found with Merchant Ref: {$ref}";
            return;
        }

        //  Found
        $this->result = $data;
        $this->errorMessage = null;
        $this->successMessage = "Data found for Merchant Ref: {$ref}";

        //  Enable Transaction ID field
        $this->isactiveTransactionNumber = true;
        if ($this->transaction_id) {

            $apiResponse = $this->paymentFetch($this->transaction_id, $data->amount);

            if (!$apiResponse['status']) {

                $this->successMessage = null;
                $this->errorMessage = $apiResponse['message'] 
                    . "<br>Transaction ID: {$this->transaction_id}";

                return;
            }

            //  SUCCESS → allow next step
            $this->errorMessage = null;
            $this->successMessage = "Payment verified successfully.<br>Transaction ID: {$this->transaction_id}";
        }
    }


    public function paymentFetch($transaction_id, $amount)
    {
        $merchantID = env('ICICI_MARCHANT_ID');
        $transactionType = 'STATUS';
        $originalTxnNo = $transaction_id;
        $secretKey = env('ICICI_MARCHANT_SECRET_KEY');

        $hashString = $amount . $merchantID . $transaction_id . $originalTxnNo . $transactionType;
        $secureHash = hash_hmac('sha256', $hashString, $secretKey);

        $postData = [
            'merchantID'      => $merchantID,
            'merchantTxnNo'   => $transaction_id,
            'originalTxnNo'   => $originalTxnNo,
            'transactionType' => $transactionType,
            'amount'          => $amount,
            'secureHash'      => $secureHash,
        ];

        try {
            $response = Http::asForm()
                ->withoutVerifying() // local fix
                ->timeout(30)
                ->post(env('ICICI_PAYMENT_CHECK_STATUS_BASH_URL'), $postData);

            $data = $response->json();

            //  SUCCESS CONDITION
            if (
                isset($data['txnStatus']) &&
                $data['txnStatus'] === 'SUC' &&
                $data['txnResponseCode'] === '0000'
            ) {
                $this->is_success = true;
                $this->result = null;
                $this->finalStatus = $data;
                return [
                    'status' => true,
                    'message' => $data['txnRespDescription'] ?? 'Transaction successful'
                ];
            }

            //  FAILED RESPONSE
            return [
                'status' => false,
                'message' => $data['txnRespDescription'] ?? 'Transaction failed',
                'data' => $data
            ];

        } catch (\Exception $e) {

            return [
                'status' => false,
                'message' => 'API Error: ' . $e->getMessage(),
            ];
        }
    }

    public function FetchRiderPayment(){
        $paymentMode = $this->finalStatus['paymentMode'] ?? "UPI";

        $paymentDateTime = $this->finalStatus['paymentDateTime'] 
            ?? now()->format('YmdHis'); // same format

        $OrderMerchantNumber = OrderMerchantNumber::where('merchantTxnNo',$this->merchant_ref)->first();
        if($OrderMerchantNumber->type==='new'){
            DB::beginTransaction();
            try{
                $status = true;
                $order_amount = $OrderMerchantNumber->amount;
                if($status==true){
                    $order = Order::find($OrderMerchantNumber->order_id);
                    $amount = number_format($order_amount, 2, '.', '');
                    $orderAmount = number_format($order->final_amount, 2, '.', '');

                    if($order->payment_status=="completed"){
                        $this->errorMessage = "Payment already completed for this subscription.";
                        return;
                    }

                    $order_type = $order->subscription?Str::snake($order->subscription->subscription_type):"";
                    
                    $payment = Payment::where('icici_merchantTxnNo',$OrderMerchantNumber->merchantTxnNo)->first();
                    
                    $payment->order_id = $order->id;
                    $payment->user_id = $order->user_id;
                    $payment->order_type = 'new_subscription_'.$order_type;
                    $payment->payment_method = $paymentMode;
                    $payment->currency = "INR";
                    $payment->payment_status = 'completed';
                    $payment->transaction_id = $paymentDateTime;
                    $payment->amount = $order->final_amount;
                    $payment->icici_txnID = $this->transaction_id;
                    // $payment->payment_date = date('Y-m-d h:i:s', strtotime($paymentDateTime));
                    $payment->save();
                    if($payment){
                        // Deposit Amount
                        PaymentItem::updateOrCreate(
                            [
                                'payment_id' => $payment->id,
                                'product_id' => $order->product_id,
                                'type'       => 'deposit',
                            ],
                            [
                                'payment_for' => 'new_subscription_' . $order_type,
                                'duration'    => $order->rent_duration,
                                'amount'      => $order->deposit_amount,
                            ]
                        );

                        // Rental Amount
                        PaymentItem::updateOrCreate(
                            [
                                'payment_id' => $payment->id,
                                'product_id' => $order->product_id,
                                'type'       => 'rental',
                            ],
                            [
                                'payment_for' => 'new_subscription_' . $order_type,
                                'duration'    => $order->rent_duration,
                                'amount'      => $order->rental_amount,
                            ]
                        );
                    }

                    $order->payment_mode = "Online";
                    $order->payment_status = "completed";
                    $order->rent_status = "ready to assign";
                    $order->subscription_type = 'new_subscription_'.$order_type;
                    $order->save();

                    DB::commit();

                    Log::info('Manual Payment Captured (New)', [
                        'user_id' => Auth::id(),
                        'merchant_ref' => $this->merchant_ref,
                        'transaction_id' => $this->transaction_id,
                        'order_id' => $order->id ?? null,
                        'payment_id' => $payment->id ?? null,
                    ]);

                    $this->reset([
                        'result',
                        'is_success',
                        'finalStatus',
                    ]);
                    $this->errorMessage = null;
                    $this->successMessage = "Payment has been successfully created.";
                    return;
                    
                }else{
                    $this->errorMessage = "Payment failed. Please try again.";
                    return;
                }    
            } catch (\Exception $e) {
                DB::rollBack();
                // dd($e->getMessage());
                $this->errorMessage = $e->getMessage();
                return;
            }
        }else{
            $status = true;
            $order = Order::with('subscription')->find($OrderMerchantNumber->order_id);
            DB::beginTransaction();
            try{
                if($status==true){
                    $existing_payment = Payment::where('icici_merchantTxnNo',$this->merchant_ref)->first();
                    if(!$existing_payment){
                        $this->errorMessage = "Payment details not found on this merchantTxnNo.";
                        return;
                    }else{
                        $assignRider = AsignedVehicle::where('order_id', $order->id)->first();

                        $order_type = $order->subscription?Str::snake($order->subscription->subscription_type):"";
                        $payment = Payment::find($existing_payment['id']);
                        $payment->order_id = $order->id;
                        $payment->user_id = $order->user_id;
                        $payment->order_type = 'renewal_subscription_'.$order_type;
                        $payment->payment_method = $paymentMode;
                        $payment->currency = "INR";
                        $payment->payment_status = 'completed';
                        $payment->transaction_id = $paymentDateTime;
                        $payment->icici_txnID = $this->transaction_id;
                        $payment->payment_date = date('Y-m-d h:i:s', strtotime($paymentDateTime));

                        $payment->amount = $order->subscription ? $order->subscription->rental_amount : $order->rental_amount;
                        // $payment->payment_date = date('Y-m-d h:i:s');
                        $payment->save();
            
                        if($payment){
                            // Rental Amount using updateOrCreate
                            $payment_item = PaymentItem::updateOrCreate(
                                [
                                    'payment_id' => $payment->id,
                                    'type' => 'rental',
                                ],
                                [
                                    'product_id' => $order->product_id,
                                    'payment_for' => 'renewal_subscription_' . $order_type,
                                    'vehicle_id' => $assignRider->vehicle_id,
                                    'amount' => $order->subscription ? $order->subscription->rental_amount : $order->rental_amount,
                                    'duration' => $order->subscription ? $order->subscription->duration : $order->rent_duration,
                                ]
                            );

                            // Calculate dates
                            $startDate = Carbon::parse($assignRider->end_date);
                            $endDate = $startDate->copy()->addDays($payment_item->duration);

                            // Update Order
                            $order->payment_mode = "Online";
                            $order->payment_status = "completed";
                            $order->rental_amount = $payment_item->amount;
                            $order->total_price = $order->deposit_amount + $payment_item->amount;
                            $order->final_amount = $order->deposit_amount + $payment_item->amount;
                            $order->rent_duration = $payment_item->duration;
                            $order->rent_start_date = $startDate;
                            $order->rent_end_date = $endDate;
                            $order->subscription_type = 'renewal_subscription_' . $order_type;
                            $order->save();
            
                            
            
                            DB::table('exchange_vehicles')->insert([
                                'status'       => "renewal",
                                'user_id'      => $assignRider->user_id,
                                'order_id'     => $assignRider->order_id,
                                'vehicle_id'   => $assignRider->vehicle_id,
                                'start_date'   => $assignRider->start_date,
                                'end_date'     => $assignRider->end_date,
                                'amount'       => $assignRider->amount,
                                'deposit_amount'  => $assignRider->deposit_amount,
                                'rental_amount'   => $assignRider->rental_amount,
                                'created_at'   => now(),
                                'updated_at'   => now(),
                            ]); 
            
                            $assignRider->start_date = $startDate;
                            $assignRider->end_date = $endDate;
                            $assignRider->status = "assigned";
                            $assignRider->rental_amount = $payment_item->amount;
                            $assignRider->deposit_amount = 0;
                            $assignRider->amount = $payment_item->amount;
                            $assignRider->save();

                            DB::commit();

                            Log::info('Manual Payment Captured (Renewal)', [
                                'user_id' => Auth::id(),
                                'merchant_ref' => $this->merchant_ref,
                                'transaction_id' => $this->transaction_id,
                                'order_id' => $order->id ?? null,
                                'payment_id' => $payment->id ?? null,
                            ]);

                            $asigned_vehicle = Stock::where('id',$assignRider->vehicle_id)->first();
                            if ($asigned_vehicle) {
                                if ($asigned_vehicle->immobilizer_status == "IMMOBILIZE") {
                                    $this->MobilizationRequest($assignRider->vehicle_id);
                                }
                            }
                            $this->reset([
                                'result',
                                'is_success',
                                'finalStatus',
                            ]);
                            $this->errorMessage = null;
                            $this->successMessage = "Payment completed and subscription renewed successfully.";
                            return;
                        }
                    }
                }
            } catch (\Exception $e) {
                DB::rollBack();
                $this->successMessage = $e->getMessage();
                return;
            }
        }
        
    }

    public function capturePayment(){
        if ($this->payment_type === "rider_subscription") {
            $this->FetchRiderPayment();
        }
        elseif ($this->payment_type === "organization_subscription") {
            $this->FetchOrgPayment();
        }
        elseif ($this->payment_type === "organization_deposit") {
            $this->FetchOrgDepositPayment();
        }
    }

    public function FetchOrgPayment(){
        DB::beginTransaction();

        try {
            $paymentMode = $this->finalStatus['paymentMode'] ?? "UPI";
            $paymentDateTime = $this->finalStatus['paymentDateTime'] ?? now()->format('YmdHis');
            $invoice_payment_date = Carbon::createFromFormat('YmdHis', $paymentDateTime)->format('Y-m-d');
            $payment = OrganizationPayment::where('icici_merchantTxnNo', $this->merchant_ref)->first();

            if (!$payment) {
                $this->errorMessage = "Payment details not found!";
                return;
            }

            $payment->update([
                'payment_method'  => $paymentMode,
                'icici_txnID'     => $this->transaction_id,
                'transaction_id'  => $this->transaction_id,
                'captured_by'     => Auth::id(),
                'payment_date'    => date('Y-m-d h:i:s', strtotime($paymentDateTime)),
                'payment_status'  => 'success',
            ]);

            $organizationInvoice = OrganizationInvoice::find($payment->invoice_id);

            if ($organizationInvoice) {
                $organizationInvoice->update([
                    'status'       => 'paid',
                    'payment_date' => $invoice_payment_date,
                ]);
            }

            // Success log
            Log::info('Organization payment successful', [
                'merchantTxnNo'   => $this->merchant_ref,
                'txnID'           => $this->transaction_id,
                'paymentMode'     => $paymentMode,
                'paymentDateTime' => $paymentDateTime,
            ]);

            DB::commit();
            $this->reset([
                'errorMessage',
                'result',
                'is_success',
                'finalStatus',
            ]);
            $this->errorMessage = null;
            $this->successMessage = "Payment has been successfully created.";
            return;

        } catch (\Exception $e) {
            DB::rollBack();
            $this->errorMessage = $e->getMessage();
            return;
        }
    }
    public function FetchOrgDepositPayment(){
        DB::beginTransaction();

        try {
            $paymentMode = $this->finalStatus['paymentMode'] ?? "UPI";
            $paymentDateTime = $this->finalStatus['paymentDateTime'] ?? now()->format('YmdHis');
            $invoice_payment_date = Carbon::createFromFormat('YmdHis', $paymentDateTime)->format('Y-m-d');
            $payment = OrganizationDepositPayment::where('icici_merchantTxnNo', $this->merchant_ref)->first();

            if (!$payment) {
                $this->errorMessage = "Payment details not found!";
                return;
            }

            $payment->update([
                'payment_method'  => $paymentMode,
                'icici_txnID'     => $this->transaction_id,
                'transaction_id'  => $this->transaction_id,
                'captured_by'     => Auth::id(),
                'payment_date'    => date('Y-m-d h:i:s', strtotime($paymentDateTime)),
                'payment_status'  => 'success',
            ]);

            $organizationInvoice = OrganizationDepositInvoice::find($payment->invoice_id);
            if ($organizationInvoice) {
                $organizationInvoice->update([
                    'status'       => 'paid',
                    'payment_date' => $invoice_payment_date,
                ]);
            }

            // Success log
            Log::info('Organization deposit payment successful', [
                'merchantTxnNo'   => $this->merchant_ref,
                'txnID'           => $this->transaction_id,
                'paymentMode'     => $paymentMode,
                'paymentDateTime' => $paymentDateTime,
            ]);

            DB::commit();
            $this->reset([
                'errorMessage',
                'result',
                'is_success',
                'finalStatus',
            ]);
            $this->errorMessage = null;
            $this->successMessage = "Payment has been successfully created.";
            return;

        } catch (\Exception $e) {
            DB::rollBack();
            $this->errorMessage = $e->getMessage();
            return;
        }
    }

    protected function MobilizationRequest($value){
        $stock = Stock::find($value);
        $vehiclesUrl = 'https://app.loconav.sensorise.net/integration/api/v1/vehicles/'.$stock->vehicle_track_id.'/immobilizer_requests';
        $payload = [
            "value" => 'MOBILIZE',
        ];
        $ch = curl_init($vehiclesUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true); // Set as POST request
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "User-Authentication: " . env('LOCONAV_TOKEN'),
            "Accept: application/json",
            "Content-Type: application/json"
        ]);

        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload)); // Pass JSON body

        $vehiclesResponse = curl_exec($ch);
        curl_close($ch);
        $response = json_decode($vehiclesResponse, true);
        if($response['success']==true){
            if(isset($response['data']['id'])){
                $stock->immobilizer_status = "MOBILIZE";
                $stock->immobilizer_request_id = null;
                $stock->save();
            }
        }
            Log::error('mobilization_request', [
                'response' => $response
            ]);
    }

    public function render()
    {
        return view('livewire.master.internal-tool-failed-payment-captured');
    }
}