<?php
use App\Models\User;
use App\Models\AdminRating;
use App\Models\ProductReview;
use App\Models\Offer;
use App\Models\Order;
use App\Models\Pincode;
use App\Models\Stock;
use App\Models\AsignedVehicle;
use App\Models\Permission;
use App\Models\Organization;
use App\Models\OrganizationDepositInvoice;
use App\Models\RentalPrice;
use App\Models\DesignationPermission;
use Illuminate\Support\Facades\Auth;
use App\Services\FCMService;
use App\Models\OrganizationInvoice;
use App\Models\OrganizationInvoiceItem;
use App\Models\OrganizationDiscount;
use App\Models\OrganizationInvoiceItemDetail;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

if (!function_exists('storeFileWithCustomName')) {
    function storeFileWithCustomName($file, $directory)
    {
       // Ensure the directory exists
       $path = storage_path("app/public/$directory");
       if (!is_dir($path)) {
           mkdir($path, 0755, true);
       }
       // Generate a custom filename: random 4 digits + timestamp + file extension
       $filename = rand(1000, 9999) . '_' . time() . '.' . $file->getClientOriginalExtension();
    //    dd($filename);
       // Store the file in the specified directory and return its path
       $file->storeAs($directory, $filename, 'public');
       return 'storage/' . $directory . '/' . $filename;
    }
}
if (!function_exists('MakingCustomerId')) {
    function MakingCustomerId(){
        do {
            // Generate a new customer ID
            $lastCustomer = User::orderBy('id', 'desc')->first();
            $customerId = 'EW-' . str_pad($lastCustomer ? $lastCustomer->id + 1 : 1, 8, '0', STR_PAD_LEFT);
    
            // Check if the generated ID already exists in the database
            $exists = User::where('customer_id', $customerId)->exists();
    
        } while ($exists);
    
        return $customerId;
    }
}
if (!function_exists('loggedUser')) {
    /**
     * Get the currently logged-in user and role.
     *
     * @return array|null ['user' => User|null, 'role' => string|null]
     */
    function loggedUser(): array|null
    {
        $user = null;
        $role = null;

        if (Auth::guard('admin')->check()) {
            $user = Auth::guard('admin')->user();
            $role = 'admin';
        } elseif (Auth::guard('organization')->check()) {
            $user = Auth::guard('organization')->user();
            $role = 'organization';
        }

        return ['user' => $user, 'role' => $role];
    }
}

if (!function_exists('CheckUserStatus')) {
    function CheckUserStatus($id) {
        // Get the user's status for all required fields
        $data = User::select('driving_licence_status', 'aadhar_card_status', 'pan_card_status', 'passbook_status', 'current_address_proof_status','profile_image_status')
                    ->where('id', $id)
                    ->first();

        // Check if all the status fields are equal to 2
        if ($data && $data->driving_licence_status == 2 && 
            $data->aadhar_card_status == 2 && 
            $data->pan_card_status == 2 && 
            $data->passbook_status == 2 && 
            $data->profile_image_status == 2 && 
            $data->current_address_proof_status == 2) {
            return true;  // Return 1 if all fields are 2
        }

        return false;  // Return 0 if any field is not 2
    }
}
if(!function_exists('AdminRatings')){
    function AdminRatings($id){
        $ratings =AdminRating::where('user_id', $id)->get();

        //check if any ratings founds
        if($ratings->isEmpty()){
            return 0; //If not ratings, return 0
        }

        //Calculate the average rating
        $averateRating = $ratings->avg('rating');//Assuming 'rating' is the column for ratings
        return round($averateRating, 1);//Round to 1 decimal for better precision
    }
}

if (!function_exists('ProductReviews')) {
    function ProductReviews($id) {
        // Retrieve product reviews with user details (name, profile_image)
        $reviews = ProductReview::select('product_id', 'user_id', 'rating', 'review')
            ->with([
                'user' => function($query) {
                    $query->select('id', 'name', 'profile_image');  // Specify columns to retrieve from the user table
                }
            ])
            ->where('product_id', $id)
            ->get();

        // Format the reviews to match the desired structure
        return $reviews->map(function($review) {
            if($review->user){
                return [
                    'name' => $review->user->name,
                    'profile_image' => $review->user->profile_image,
                    'rating' => $review->rating,
                    'review' => $review->review
                ];
            }
        });
    }
}

if (!function_exists('GetAllActivePaymentType')) {
    function GetAllActivePaymentType() {
        return $data = [
            [
                'title' => 'Card',
                'value'=>'Card',
                'image' => 'assets/img/card.png'
            ],
            [
                'title' => 'Cash on Delivery (COD)',
                'value'=>'COD',
                'image' => 'assets/img/cash-on-delivery.png'
            ],
            [
                'title' => 'PhonePay',
                'value'=>'PhonePay',
                'image' => 'assets/img/phonepe.png'
            ],
            [
                'title' => 'GooglePay',
                'value'=>'GooglePay',
                'image' => 'assets/img/gpay.png'
            ],
            [
                'title' => 'UPI',
                'value'=>'UPI',
                'image' => 'assets/img/upi.png'
            ],
            [
                'title' => 'NetBanking',
                'value'=>'NetBanking',
                'image' => 'assets/img/net-banking.png'
            ],
            [
                'title' => 'Wallet',
                'value'=>'Wallet',
                'image' => 'assets/img/wallet.png'
            ]
        ];
    }
}

if(!function_exists('checkCouponValue')){
    function checkCouponValue($couponCode, $orderAmount, $userId){

        $current_date = date('Y-m-d H:i:s'); // Use 'H' for 24-hour format

        // Fetch Offer
        $offer = Offer::where('coupon_code', $couponCode)
            ->where('status', 'active')
            ->whereDate('start_date', '<=', $current_date)
            ->whereDate('end_date', '>=', $current_date)
            ->first();
        if (!$offer) {
            return [
                'status' => false,
                'message' => 'Invalid or expired coupon.',
            ];
        }
        // Validate the minimum order amount
        if ($offer->minimum_order_amount && $orderAmount < $offer->minimum_order_amount) {
            return [
                'status' => false,
                'message' => 'Order amount does not meet the minimum required for this coupon.',
            ];
        }

        // Check global usage limit
        if($offer->usage_limit){
            $global_usage_order = Order::where('offer_id', $offer->id)->count();
            if ($global_usage_order >= $offer->usage_limit) {
                return [
                    'status' => false,
                    'message' => 'This coupon has reached its global usage limit.',
                ];
            }
        }
        

        // Check usage limit per user
        if($offer->usage_per_user){
            $usage_per_user_order = Order::where('offer_id', $offer->id)
                ->where('user_id', $userId)
                ->count();
            if ($usage_per_user_order >= $offer->usage_per_user) {
                return [
                    'status' => false,
                    'message' => 'You have reached the usage limit for this coupon.',
                ];
            }
        }

        // Calculate discount
        $discount = 0;
        if ($offer->discount_type === 'flat') {
            $discount = $offer->discount_value;
        } elseif ($offer->discount_type === 'percentage') {
            $discount = $orderAmount * $offer->discount_value / 100;
            if ($offer->maximum_discount) {
                $discount = min($discount, $offer->maximum_discount);
            }
        }

        $finalAmount = max(0, $orderAmount - $discount);

        return [
            'status' => true,
            'offer_id' => $offer->id,
            'discount' => $discount,
            'final_amount' => $finalAmount,
            'message' => 'Coupon applied successfully.',
        ];
    }
}

if(!function_exists('generateOrderNumber')){
    function generateOrderNumber(){
        $prefix = 'EW-'.date('Ym');
        $orderNumber = null;

        do{
            $lastOrder = Order::where('order_number', 'LIKE', "{$prefix}%")
            ->orderBy('order_number', 'desc')
            ->first();
            $lastNumber = $lastOrder ? (int)substr($lastOrder->order_number, -6) : 0;

            // Increment the last number and format it as a 6-digit number
            $newNumber = str_pad($lastNumber + 1, 6, '0', STR_PAD_LEFT);

            // Generate the new order number
            $orderNumber = "{$prefix}{$newNumber}";
        } while (Order::where('order_number', $orderNumber)->exists()); // Ensure uniqueness

        return $orderNumber;
    }
}
if(!function_exists('PincodeStatus')){
    function PincodeStatus($code){
        $Pincode = Pincode::where('pincode', $code)->first();
        if($Pincode){
            return $Pincode->status;
        }
        return 1;
    }
}
if(!function_exists('VehicleStatus')){
    function VehicleStatus($id){
        $data = AsignedVehicle::with('order')->where('vehicle_id', $id)->whereIn('status', ['sold','assigned'])->orderBy('id','DESC')->first();
        
        if($data){
            $return = [];
            $return['order_id']= $data->order?$data->order->id:null;
            if($data->status=="assigned"){
                $return['class'] = "warning";
                $return['message'] = "Assigned Now";
            }elseif($data->status=="sold"){
                $return['class'] = "danger";
                $return['message'] = "Sold";
            }
            return $return; //Assigned
        }
        return null; // Return null instead of empty array
    }
}
if(!function_exists('vehicleLog')){
    function vehicleLog($id){
        $data = AsignedVehicle::where('vehicle_id', $id)->get()->count();
        if($data){
            return $data; //Assigned
        }
        return 0; //Not Assigned
    }
}
if (!function_exists('GetProductWiseAvailableStock')) {
    function GetProductWiseAvailableStock($product_id) {
        $all_vehicle_ids = Stock::where('product_id', $product_id)->pluck('id')->toArray();

        $used_data = AsignedVehicle::whereIn('status', ['assigned', 'sold'])
            ->whereIn('vehicle_id', $all_vehicle_ids)
            ->count();

        return count($all_vehicle_ids) - $used_data; // Available Stock
    }
}

if (!function_exists('GetProductWiseAssignedStock')) {
    function GetProductWiseAssignedStock($product_id) {
        $all_vehicle_ids = Stock::where('product_id', $product_id)->pluck('id')->toArray();
        
        return AsignedVehicle::where('status', 'assigned')
            ->whereIn('vehicle_id', $all_vehicle_ids)
            ->count();
    }
}

if (!function_exists('GetProductWiseSoldStock')) {
    function GetProductWiseSoldStock($product_id) {
        $all_vehicle_ids = Stock::where('product_id', $product_id)->pluck('id')->toArray();

        return AsignedVehicle::where('status', 'sold')
            ->whereIn('vehicle_id', $all_vehicle_ids)
            ->count();
    }
}



if(!function_exists('PincodeId')){
    function PincodeId($code){
        $Pincode = Pincode::where('pincode', $code)->first();
        if($Pincode){
            return $Pincode->id;
        }
        return 1;
    }
}
if(!function_exists('GetIgnitionStatus')){
    function GetIgnitionStatus($vehicle_id){
        $vehiclesUrl = 'https://api.a.loconav.com/integration/api/v1/vehicles/telematics/last_known';
        $payload = [
            "vehicleIds" => [$vehicle_id],
            "sensors" => [
                "gps",
                "vehicleBatteryLevel",
                "numberOfSatellites"
            ]
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
            $VehicleLastKnow = $response['data']['values'][0];

            if (
                isset($VehicleLastKnow['gps']) &&
                isset($VehicleLastKnow['gps']['ignition']) &&
                isset($VehicleLastKnow['gps']['ignition']['value'])
            ) {
                return $VehicleLastKnow['gps']['ignition']['value'];
            }
        }else{
           return "OFF";
        }
    }
}
if(!function_exists('hasPermissionByParent')){
    function hasPermissionByParent($parentName){
        // Ensure designation is loaded
        $user = Auth::guard('admin')->user();
        if (!$user || !$user->designation) {
            return false;
        }
        $permission_id = Permission::where('parent_name', $parentName)->value('id');
        if($permission_id){
            return DesignationPermission::where('permission_id', $permission_id)->where('designation_id', $user->designation)->exists();
        }else{
            return false;
        }
    }
}
if(!function_exists('UserCurrentLocation')){
    function UserCurrentLocation($lat,$lng){
        $url = "https://nominatim.openstreetmap.org/reverse?format=json&lat={$lat}&lon={$lng}&zoom=18&addressdetails=1";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, 'E-went'); // Required by Nominatim

        $response = curl_exec($ch);
        curl_close($ch);

        $response = json_decode($response, true);
        return $response;
        
    }
}

if (!function_exists('sendSms')) {
    function sendSms($mobile, $otp, $user_type) {

        // Get user name
        if($user_type=='user'){
            $user = \App\Models\User::where('mobile', $mobile)->first();
            $name = $user ? $user->name : 'User';
        }else{
            $user = \App\Models\Admin::where('mobile', $mobile)->first();
            $name = $user ? $user->name : 'User';
        }
      

        // SMS API details
        $api_key = '26835A7C8208EB';
        // $mobile = 9851807106;
        $contacts = preg_replace('/^0+/', '', $mobile); // remove leading zeros
        $contacts = '91' . $contacts; // prefix with country code (if always India)
        $from = 'EWFMPL';
        $template_id = '1207175077110715551';
        $routeid = 13;

        // SMS body
        $sms_text = "Dear {$name}, use {$otp} to proceed with your password reset. – E-went";
        
        // Encode SMS
        $sms_text_encoded = urlencode($sms_text);

        // Build API URL
        $api_url = "https://bulksms.smsroot.com/app/smsapi/index.php"
            . "?key={$api_key}"
            . "&campaign=0"
            . "&routeid={$routeid}"
            . "&type=text"
            . "&contacts={$contacts}"
            . "&senderid={$from}"
            . "&msg={$sms_text_encoded}"
            . "&template_id={$template_id}";

        // Send request
        $response = file_get_contents($api_url);

        // Debug
        // dd($response, $api_url);
    }
}

if (!function_exists('sendPushNotification')) {
    function sendPushNotification($user_id, $type, $data = [])
    {
        // dd($user_id, $type, $data);
        $messages = [
            'weather_update' => '🚨 Dear :name, due to a payment issue we’ve unlocked your vehicles temporarily. Please continue your ride hassle-free.',
            'payment_overdue' => '🚨 Dear :name, your subscription is due for renewal. Please renew by paying ₹:amount to continue enjoying our services.',

            'ready_to_assign'    => '✅ Dear :name, your request is ready to be assigned. Please wait for confirmation.',
            'continue_with_vehicle'  => '✅ Dear :name, you can continue with your current vehicle :vehicle_number for the next booking.',
            'payment_complete'   => '✅ Dear :name, your payment of ₹:amount has been received successfully. Thank you!',
            'kyc_verified'       => '✅ Dear :name, your KYC has been verified successfully. You can now enjoy all services.',
            'kyc_rejected'       => '🚨 Dear :name, unfortunately your KYC has been rejected. Please update your documents and try again.',
            'register'           => 'Welcome :name! Your registration is successful. You can now log in to your account.',
            'login'              => 'Hello :name, you have successfully logged in. Welcome back!',
            'assign_vehicle'     => '✅ Dear :name, your vehicle 🛵 :vehicle_number has been assigned successfully. Please check your booking details.',
            'deallocate_vehicle' => '🚨 Dear :name, your vehicle 🛵 :vehicle_number has been deallocated successfully.',
            'exchange_vehicle'   => '✅ Dear :name, your vehicle 🛵 :old_vehicle_number ➝ 🛵 :new_vehicle_number has been exchanged successfully',
            'document_status_update'=>'',
        ];

        $user = User::find($user_id);
        if (!$user || !$user->fcm_token) {
            return false; // No token to send
        }


        if ($type === 'document_status_update') {
            if (!isset($data['status'])) {
                $data['status'] = 'pending';
            }

            switch ($data['status']) {
                case 'approved':
                    $messageText = "✅ Dear {$user->name}, your document {$data['document_name']} has been approved successfully.";
                    break;

                case 'rejected':
                    $messageText = "❌ Dear {$user->name}, your document {$data['document_name']} has been rejected. Please update and resubmit.";
                    break;

                default:
                    $messageText = "ℹ️ Dear {$user->name}, your document {$data['document_name']} status has been updated.";
                    break;
            }
        } else {    
            $messageText = $messages[$type] ?? 'Notification from E-went.';

            // Replace all possible placeholders dynamically
            $messageText = str_replace(
                [
                    ':name',
                    ':amount',
                    ':vehicle_number',
                    ':old_vehicle_number',
                    ':new_vehicle_number'
                ],
                [
                    $user->name ?? 'User',
                    $data['amount'] ?? '',
                    $data['vehicle_number'] ?? '',
                    $data['old_vehicle_number'] ?? '',
                    $data['new_vehicle_number'] ?? ''
                ],
                $messageText
            );
        }

        try {
            $fcm = new FCMService();
            $response = $fcm->sendPushNotification(
                $user->fcm_token,
                'E-went',
                $messageText,
                array_merge(['type' => $type], $data)
            );
            return $response;

        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
}
if (!function_exists('makeOrganizationID')) {
    function makeOrganizationID()
    {
        do {
            // Get last organization
            $lastOrganization = Organization::latest('id')->first();

            if (!$lastOrganization || !$lastOrganization->organization_id) {
                $newId = 'ORG0001';
            } else {
                // Extract numeric part from organization_id (e.g., ORG0005 → 5)
                $lastNumber = (int) str_replace('ORG', '', $lastOrganization->organization_id);

                // Increment the number
                $newNumber = $lastNumber + 1;

                // Format with leading zeros
                $newId = 'ORG' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
            }

            // Keep looping if ID already exists in DB
        } while (Organization::where('organization_id', $newId)->exists());

        return $newId;
    }
}
if (!function_exists('makeOrganizationInvoiceID')) {
    function makeOrganizationInvoiceID()
    {
        $year = Carbon::now()->year;

        do {
            // Get last invoice of current year
            $lastInvoice = OrganizationInvoice::whereYear('created_at', $year)
                ->latest('id')
                ->first();

            if (!$lastInvoice || !$lastInvoice->invoice_number) {
                $newNumber = 1;
            } else {
                // Extract last 6 digits
                $lastNumber = (int) substr($lastInvoice->invoice_number, -6);
                $newNumber = $lastNumber + 1;
            }

            // Build invoice number
            $newId = sprintf(
                'ORG-INV-%s-%06d',
                $year,
                $newNumber
            );

        } while (OrganizationInvoice::where('invoice_number', $newId)->exists());

        return $newId;
    }
}
if (!function_exists('makeOrganizationDepositInvoiceID')) {
    function makeOrganizationDepositInvoiceID()
    {
        $year = Carbon::now()->year;

        do {
            // Get last Deposit invoice for current year
            $lastInvoice = OrganizationDepositInvoice::where('type', 'Deposit')
                ->whereYear('created_at', $year)
                ->latest('id')
                ->first();

            if (!$lastInvoice || !$lastInvoice->invoice_number) {
                $newNumber = 1;
            } else {
                // Extract last 6 digits
                $lastNumber = (int) substr($lastInvoice->invoice_number, -6);
                $newNumber = $lastNumber + 1;
            }

            // Build invoice number
            $newId = sprintf(
                'ORG-DEP-INV-%s-%06d',
                $year,
                $newNumber
            );

        } while (OrganizationDepositInvoice::where('invoice_number', $newId)->exists());

        return $newId;
    }
}
if (!function_exists('getB2BproductPrice')) {
    function getB2BproductPrice($org_id, $subscription_id)
    {
        $org = Organization::find($org_id);
        $rental = RentalPrice::find($subscription_id);
        if (!$org || !$rental) {
            return null;
        }

        $rider_visibility_percentage = $org->rider_visibility_percentage ?? 0;

        $finalPrice = $rental->rental_amount + ($rental->rental_amount * $rider_visibility_percentage / 100);

        return (int) round($finalPrice);
    }
}
if (!function_exists('createInvoiceForOrganization')) {
    function createInvoiceForOrganization($org_id, $type, $invoice_start_date, $invoice_end_date, $due_date)
    {
        $org = Organization::with('user')->find($org_id);
        if (!$org) {
            return null; // return null if org or rental not found
        }
        DB::beginTransaction();
        try {
            $invoice = new OrganizationInvoice;
            $invoice->organization_id = $org->id;
            $invoice->invoice_number = makeOrganizationInvoiceID();
            $invoice->type = $type;
            $invoice->billing_start_date = $invoice_start_date->toDateString();
            $invoice->billing_end_date = $invoice_end_date->toDateString();
            $invoice->amount = 0;
            $invoice->due_date = $due_date;
            $invoice->save();
            if($invoice){
                $total_amount = 0;
                foreach($org->user as $key=>$user){
                    $invoice_item = new OrganizationInvoiceItem;
                    $invoice_item->user_id = $user->id;
                    $invoice_item->invoice_id = $invoice->id;
                    $invoice_item->total_day = 0;
                    $invoice_item->total_price = 0;
                    $invoice_item->save();
                    if($invoice_item){
                        $start = Carbon::parse($invoice_start_date);
                        $end = Carbon::parse($invoice_end_date);

                        // Get all dates between start and end
                        $invoice_item_total_day = 0;
                        $invoice_item_total_price = 0;
                        $all_date_between_invoice_date = CarbonPeriod::create($start, $end);
                         
                        foreach ($all_date_between_invoice_date as $invoice_date_item) {
                            // $invoice_date_item is a Carbon instance
                            $date = $invoice_date_item->toDateString();
                            // Find the current order for that day
                           $CurrentOrder = Order::where('user_id', $user->id)
                            ->where('rent_start_date', '<=', $invoice_date_item)
                            ->whereIn('rent_status', ['active', 'returned'])
                            ->where(function($q) use ($invoice_date_item) {
                                $q->where(function ($sub) use ($invoice_date_item) {
                                    // Either still ongoing OR ended after this invoice date
                                    $sub->whereNull('rent_end_date')
                                        ->orWhere('rent_end_date', '>=', $invoice_date_item->toDateString());
                                })
                                ->orWhere(function ($sub) use ($invoice_date_item) {
                                    // OR return_date is still open / after this invoice date
                                    $sub->whereNull('return_date')
                                        ->orWhere('return_date', '>=', $invoice_date_item->toDateString());
                                });
                            })
                            ->orderBy('id', 'desc')
                            ->first();

                            // Skip this date if no order found
                            if (!$CurrentOrder) continue;
                            $checkexistingData = OrganizationInvoiceItemDetail::where('date', $date)->where('order_id', $CurrentOrder->id)->first();
                            if($checkexistingData) continue;
                            // Safely get subscription
                            $subscription = $CurrentOrder->subscription;
                            // Skip if subscription missing
                            if (!$subscription || $subscription->duration <= 0) continue;

                            // Safely calculate per-date amount
                            $discountPercentage = $user->organization_details->discount_percentage ?? 0;

                            $discountAmount = ($subscription->rental_amount * $discountPercentage) / 100;
                            $finalAmount    = $subscription->rental_amount - $discountAmount;
                            $subscription_rental_amount = $subscription->rental_amount / $subscription->duration;

                            $per_date_amount = $finalAmount / $subscription->duration;
                           
                            $org_discount = OrganizationDiscount::where('organization_id', $org->id)
                            ->whereDate('start_date', '<=', $date)
                            ->where(function ($q) use ($date) {
                                $q->whereNull('end_date')
                                ->orWhereDate('end_date', '>=', $date);
                            })
                            ->orderByDesc('id') // latest one
                            ->first();
                            // Optional: round to 2 decimals
                            $per_date_amount = $org_discount
                            ? FetchActualDiscountedAmount(
                                $org_discount->discount_percentage,$subscription_rental_amount
                            )
                            : round($per_date_amount, 2);
                            $invoice_item_detail = new  OrganizationInvoiceItemDetail;
                            $invoice_item_detail->invoice_item_id = $invoice_item->id;
                            $invoice_item_detail->order_id = $CurrentOrder->id;
                            $invoice_item_detail->date = $date;
                            $invoice_item_detail->day_amount = $per_date_amount;
                            $invoice_item_detail->save();

                            $invoice_item_total_day +=1;
                            $invoice_item_total_price +=$per_date_amount;
                        }

                        // ADDED: if invoice_item total price is zero or less, delete the item + its details and skip this user
                        if ($invoice_item_total_price <= 0) {
                            // delete any details that may have been created for this item
                            OrganizationInvoiceItemDetail::where('invoice_item_id', $invoice_item->id)->delete();
                            // delete the (empty) invoice item
                            $invoice_item->delete();
                            // skip adding this user to total_amount
                            continue;
                        }

                        // update invoice_item for total price & total day
                        $invoice_item->total_day = $invoice_item_total_day;
                        $invoice_item->total_price = $invoice_item_total_price;
                        $invoice_item->save();
                    }
                    $total_amount +=$invoice_item->total_price;
                }

                // ADDED: if total invoice amount is zero or less, remove the invoice and any leftover items/details
                if ($total_amount <= 0) {
                    // delete any details for items linked to this invoice
                    $itemIds = OrganizationInvoiceItem::where('invoice_id', $invoice->id)->pluck('id');
                    if ($itemIds->isNotEmpty()) {
                        OrganizationInvoiceItemDetail::whereIn('invoice_item_id', $itemIds)->delete();
                        OrganizationInvoiceItem::whereIn('id', $itemIds)->delete();
                    }
                    // delete the invoice itself (do not generate empty invoices)
                    $invoice->delete();
                } else {
                    $invoice->amount = $total_amount;
                    $invoice->save();
                }
            }

            DB::commit();
            return $invoice;
        } catch (\Exception $e) {
            DB::rollBack();
            $e->getMessage();
        }

    }
}
if (!function_exists('FetchActualDiscountedAmount')) {
    function FetchActualDiscountedAmount($discountPercentage, $amount)
    {
        $discountValue = ($amount * $discountPercentage) / 100;
        $amount -= $discountValue;
        return round($amount, 2);
    }
}
if(!function_exists('FetchUserVehicleStatus')){
    function FetchUserVehicleStatus($user_id){
        $user = User::find($user_id);
        $response = ['status'=>'','color'=>'', 'tooltip' => ''];
        if($user && $user->active_vehicle){
            $response['status'] = $user->active_vehicle->status;
            $response['color'] = $user->active_vehicle->status == 'assigned' ? 'success' : 'danger';

            $model = optional(optional($user->active_vehicle)->stock->product)->title ?? 'N/A';
            $vehicle_number = optional(optional($user->active_vehicle)->stock)->vehicle_number ?? 'N/A';
            $response['tooltip'] = 'Model: ' . $model . '<br>Vehicle: ' . $vehicle_number;
        }else{
            $response['status'] = 'Unassigned';
            $response['color'] = 'secondary';
            $response['tooltip'] = 'No active <br> vehicle assigned';
        }
        return $response;
    }
}

if (!function_exists('esign_pdf_generate')) {
    function esign_pdf_generate($email)
    {
        $data = \App\Models\UserTermsConditions::where('email', $email)
            ->whereNull('signed_url')
            ->first();
        // If no record found, exit early
        if (!$data) {
            return [
                'success' => false,
                'message' => 'No pending record found for this email.'
            ];
        }
        if (!$data || !$data->request_id) {
            throw new \Exception('Request ID not found for email: ' . $email);
        }

        $requestId = $data->request_id;
        $url = 'https://live.zoop.one/contract/esign/v5/fetch/request?request_id=' . $requestId;

        // Initialize cURL
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'app-id: ' . env('ZOOP_APP_ID'),
            'api-key: ' . env('ZOOP_APP_KEY'),
        ]);

        $response = curl_exec($ch);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError) {
            \Log::error('Curl Error for Request ID ' . $requestId . ': ' . $curlError);
            throw new \Exception('Curl Error: ' . $curlError);
        }

        $responseData = json_decode($response, true);
        if (
            isset($responseData['transaction_status']) &&
            $responseData['transaction_status'] === 'SUCCESS' &&
            $data->signed_url == null &&
            !empty($responseData['document']['signed_url'])
        ) {
            try {
                // Download the signed PDF
                $pdfContent = @file_get_contents($responseData['document']['signed_url']);
                if ($pdfContent === false) {
                    throw new \Exception('Failed to download signed PDF for Request ID: ' . $requestId);
                }

                // Save temporarily
                $tempPath = storage_path('app/temp_signed.pdf');
                file_put_contents($tempPath, $pdfContent);

                // Convert to UploadedFile for storing
                $uploadedFile = new \Illuminate\Http\UploadedFile(
                    $tempPath,
                    'signed.pdf',
                    'application/pdf',
                    null,
                    true
                );

                // Create directory if not exists
                $storedPath = storeFileWithCustomName($uploadedFile, 'signed_docs');
                $data->signed_url = asset($storedPath);
                $data->status = 'success';
                $data->save();

                // Delete temp file
                unlink($tempPath);

                return [
                    'success' => true,
                    'message' => 'PDF generated successfully.',
                    'signed_url' => $data->signed_url
                ];

            } catch (\Exception $e) {
                \Log::error('Error saving signed PDF for Request ID ' . $requestId . ': ' . $e->getMessage());
                throw new \Exception('Error saving PDF: ' . $e->getMessage());
            }
        } else {
            return [
                'success' => false,
                'message' => 'Transaction not successful or signed_url already exists.'
            ];
        }
    }
}






