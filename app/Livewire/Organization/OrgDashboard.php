<?php

namespace App\Livewire\Organization;

use Livewire\Component;
use App\Models\Organization;
use App\Models\OrganizationInvoice;
use App\Models\OrganizationPayment;
use App\Models\OrganizationInvoiceItem;
use App\Models\OrganizationInvoiceItemDetail;
use App\Models\User;
use App\Models\OrgInvoiceMerchantNumber;
use Livewire\WithPagination;
use App\Models\OrganizationProduct;

use Illuminate\Support\Facades\Auth;

class OrgDashboard extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $search = '';
    public $page = 1;
    public $organization;
    public $allRidersCount = null;
    public $assignedVehiclesCount  = null;
    public $pendingInvoice = null;
    public $InvoicePaidAmount = 0;
    public $activeTab = 'overview';
    public $paymentMessage = [];
    public $OrganizationModels;
    public $isModalOpen = false;
    public $selectedCustomer;
    public $isPreviewimageModal = false;
    public $preview_front_image;
    public $preview_back_image;
    public $document_type;
    public $isRejectModal = false;
    public $field;
    public $id;

    public function mount(){

        $type = request()->get('type');
        if ($type && in_array($type, ['invoice', 'models', 'payment', 'riders'])) {
            $this->activeTab = $type;
        }
        $this->organization = Auth::guard('organization')->user();
         $this->assignedVehiclesCount = User::where('user_type', 'B2B')
            ->where('organization_id', $this->organization->id)
            ->whereHas('active_vehicle')
            ->count();
            $this->pendingInvoice = OrganizationInvoice::where('organization_id', $this->organization->id)
            ->whereIn('status', ['pending','overdue'])
            ->orderBy('created_at', 'asc')
            ->first();
            $this->InvoicePaidAmount = OrganizationInvoice::where('organization_id', $this->organization->id)
            ->where('status', 'paid')->sum('amount');
    }
    
    public function gotoPage($value, $pageName = 'page')
    {
        $this->setPage($value, $pageName);
        $this->page = $value;
    }

    public function changeTab($value){
        $this->activeTab = $value;
        $this->resetPageField();
        $this->dispatch('update-url', [
            'type' => $value
        ]);
    }
    public function FilterRider($value)
    {
        $this->reset(['page']);
        $this->search = $value;
        $this->resetPage();
    }
    public function resetPageField(){
        $this->reset(['search']);
    }
    // Add this at the top of your component

    public function invoiceInitiatePayment($invoice_id)
    {
        $invoice = OrganizationInvoice::find($invoice_id);
        if($invoice->status=="paid"){
            $this->paymentMessage = [
                'status'       => false,
                'response'     => 'This invice has already beed paid. No further payment is required',
                'redirect_url' => null,
            ];
        }
        
        $formattedAmount = number_format((float)$invoice->amount, 2, '.', '');

        $data = [
            "merchantId"=> env('ICICI_MARCHANT_ID'),
            "merchantTxnNo"=> $invoice->invoice_number.'-'.rand(1000, 9999),
            "amount"=> $formattedAmount,
            "currencyCode"=> "356",
            "payType"=> "0",
            "customerEmailID"=> optional($invoice->organization)->email ?? "testmail123@gmail.com",
            "transactionType"=> "SALE",
            "txnDate"=> date('YmdHis'),
            // "returnURL"=> 'http://127.0.0.1:8000/api/organization/thankyou',
            "returnURL"=> secure_url('api/organization/thankyou'),
            "customerMobileNo"=> "91".optional($invoice->organization)->mobile ?? "9876543210",
            "customerName"=> optional($invoice->organization)->name ?? "N/A",
        ];

        $hashKey = implode('', [
            $data["amount"],
            $data["currencyCode"],
            $data["customerEmailID"],
            $data["customerMobileNo"],
            $data["customerName"],
            $data["merchantId"],
            $data["merchantTxnNo"],
            $data["payType"],
            $data["returnURL"],
            $data["transactionType"],
            $data["txnDate"]
        ]);

        $data['secureHash'] = hash_hmac('sha256', $hashKey, env('ICICI_MARCHANT_SECRET_KEY'));

        $ch = curl_init(env('ICICI_PAYMENT_INITIATE_BASH_URL'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        // For UAT self-signed certificate
        if (app()->environment('local', 'uat')) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        }

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            $this->paymentMessage = [
                'status'       => false,
                'response'     => 'Payment request failed: ' . curl_error($ch),
                'redirect_url' => null,
            ];
            curl_close($ch);
            return;
        }


        curl_close($ch);
        $responseData = json_decode($response, true);
        if (isset($responseData['responseCode']) && $responseData['responseCode'] === 'R1000') {
            // Save merchant number
            OrgInvoiceMerchantNumber::updateOrCreate(
                ['invoice_id' => $invoice->id],
                [
                    'organization_id' => $this->organization->id,
                    'merchantTxnNo'    => $responseData['merchantTxnNo'] ?? null,
                    'redirect_url'    => $responseData['redirectURI'] ?? null,
                    'secureHash'      => $responseData['secureHash'] ?? null,
                    'tranCtx'         => $responseData['tranCtx'] ?? null,
                    'amount'          => $formattedAmount,
                ]
            );

            // Update or create Payment record
            $payment = OrganizationPayment::where('invoice_id', $invoice->id)
                ->where('payment_status', 'initiated')
                ->first();

            if ($payment) {
                $payment->update([
                    'icici_merchantTxnNo' => $responseData['merchantTxnNo'],
                    'payment_status' => 'initiated',
                    'amount' => $formattedAmount,
                ]);
            } else {
                OrganizationPayment::create([
                    'organization_id' => $this->organization->id,
                    'invoice_id' => $invoice->id,
                    'invoice_type' => $invoice->type,
                    'icici_merchantTxnNo' => $responseData['merchantTxnNo'],
                    'amount' => $formattedAmount,
                ]);
            }

            $redirectURI = isset($responseData['redirectURI'], $responseData['tranCtx'])
                ? $responseData['redirectURI'] . '?tranCtx=' . $responseData['tranCtx']
                : null;

            if ($redirectURI) {
                $this->dispatch('payment_redirect_url', [
                    'redirect_url' => $redirectURI
                ]);
                $this->paymentMessage = [
                    'status'       => true,
                    'response'     => "Payment initiated successfully. You will be redirected to the payment page in 3 seconds...",
                    'redirect_url' => $redirectURI,
                ];
            } else {
                $this->paymentMessage = [
                    'status'       => false,
                    'response'     => "Failed to initiate payment. Please try again.",
                    'redirect_url' => null,
                ];
            }
        } else {
            $this->paymentMessage = [
                'status'       => false,
                'response'     => 'Payment initiation failed: ' . ($responseData['responseMessage'] ?? 'Please contact administration'),
                'redirect_url' => null,
            ];
        }
    }


    public function showCustomerDetails($customerId)
    {
        $this->selectedCustomer = User::with('doc_logs')->find($customerId);
        $this->isModalOpen = true;
    }
    public function closeModal()
    {
        $this->isModalOpen = false;
    }
    public function OpenPreviewImage($front_image, $back_image,$document_type)
    {
        $this->preview_front_image = $front_image;
        $this->preview_back_image = $back_image;
        $this->document_type = $document_type;
        $this->isPreviewimageModal = true;
    }
       public function OpenRejectForm($field, $document_type, $id)
    {
        $this->field = $field;
        $this->document_type = $document_type;
        $this->id = $id; // Changed from $this->id to avoid conflicts
        $this->isRejectModal = true;
    }
     public function closePreviewImage()
    {
        $this->isPreviewimageModal = false;
        $this->reset(['preview_front_image', 'preview_back_image','document_type']);
    }

     public function VerifyKyc($status, $id){
        $user = User::find($id);
        if($user){
            if($status=="verified"){
                if($user->aadhar_card_status!=2){
                    session()->flash('error_kyc_message', 'Aadhar card is not verified. Please verify the Aadhar card.');
                    return false;
                }
                if($user->pan_card_status!=2){
                    session()->flash('error_kyc_message', 'Pan card is not verified. Please verify the Pan card.');
                    return false;
                }
                if($user->current_address_proof_status!=2){
                    session()->flash('error_kyc_message', 'Address proof is not verified. Please verify the current address proof.');
                    return false;
                }
                if($user->passbook_status!=2){
                    session()->flash('error_kyc_message', 'Passbook/Cancelled cheque is not verified. Please verify the passbook/cancelled cheque.');
                    return false;
                }
                if($user->profile_image_status!=2){
                    session()->flash('error_kyc_message', 'Rider image is not verified. Please verify the rider image.');
                    return false;
                }

                $user->org_kyc_verified_at = now()->toDateTimeString();
                $user->org_kyc_verified_by = $this->organization->id;
                $user->org_is_verified = "verified";
                $user->org_date_of_rejection = NULL;
                $user->org_rejected_by = NULL;

            }elseif($status=="rejected"){
                $user->org_date_of_rejection = now()->toDateTimeString();
                $user->org_rejected_by = $this->organization->id;
                $user->org_is_verified = "rejected";
            }else{
                $user->org_kyc_verified_at = now()->toDateTimeString();
                $user->org_kyc_verified_by = $this->organization->id;
                $user->org_is_verified = "unverified";
                $user->org_date_of_rejection = NULL;
                $user->org_rejected_by = NULL;
            }
            $user->save();
            $this->showCustomerDetails($id);
            // Optionally, show a confirmation message
            session()->flash('modal_message', 'KYC status updated successfully.');
        }
    }

    public function render()
    {
        $this->OrganizationModels = OrganizationProduct::where('organization_id', $this->organization->id)->get();
        $riders = User::with('doc_logs','latest_order','active_vehicle')
            ->when($this->search, function ($query) {
                $searchTerm = '%' . $this->search . '%';
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('name', 'like', $searchTerm)
                    ->orWhere('mobile', 'like', $searchTerm)
                    ->orWhere('email', 'like', $searchTerm)
                    ->orWhere('customer_id', 'like', $searchTerm)
                    ->orWhereHas('active_vehicle.stock', function ($q2) use ($searchTerm) {
                        $q2->where('vehicle_number', 'like', $searchTerm)
                            ->orWhere('vehicle_track_id', 'like', $searchTerm)
                            ->orWhere('imei_number', 'like', $searchTerm)
                            ->orWhere('chassis_number', 'like', $searchTerm)
                            ->orWhere('friendly_name', 'like', $searchTerm)
                            ->orWhereHas('product', function ($productQuery) use ($searchTerm) {
                                $productQuery->where('title', 'like', $searchTerm)
                                    ->orWhere('types', 'like', $searchTerm)
                                    ->orWhere('product_sku', 'like', $searchTerm);
                            });
                    });
                });
            })
            ->where('user_type', 'B2B')
            ->where('organization_id', $this->organization->id)
            ->orderBy('id', 'DESC')
            ->paginate(20,['*'],'riders');

        $invoices = OrganizationInvoice::with([
            'items.user', // load rider
            'items.details' // load day-wise breakdown
        ])
        ->where('organization_id', $this->organization->id)
        ->when($this->search, function ($query) {
            $searchTerm = '%' . $this->search . '%';

            $query->where(function ($q) use ($searchTerm) {
                $q->where('invoice_number', 'like', $searchTerm)
                ->orWhere('type', 'like', $searchTerm)
                ->orWhere('billing_start_date', 'like', $searchTerm)
                ->orWhere('billing_end_date', 'like', $searchTerm)
                ->orWhere('status', 'like', $searchTerm)
                ->orWhere('amount', 'like', $searchTerm)
                ->orWhere('payment_date', 'like', $searchTerm)
                ->orWhere('due_date', 'like', $searchTerm);
            });
        })
        ->orderByDesc('id')
        ->paginate(10, ['*'], 'invoices');


        $this->allRidersCount = $riders->total();

        $payment_query = OrganizationPayment::with('organization')
            ->where('organization_id', $this->organization->id)
            ->when($this->search, function ($query) {
                $searchTerm = '%' . $this->search . '%';
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('invoice_id', 'like', $searchTerm)
                        ->orWhere('invoice_type', 'like', $searchTerm)
                        ->orWhere('payment_method', 'like', $searchTerm)
                        ->orWhere('transaction_id', 'like', $searchTerm)
                        ->orWhere('icici_merchantTxnNo', 'like', $searchTerm)
                        ->orWhere('icici_txnID', 'like', $searchTerm)
                        ->orWhere('currency', 'like', $searchTerm)
                        ->orWhere('amount', 'like', $searchTerm)
                        ->orWhere('payment_date', 'like', $searchTerm)
                        ->orWhere('payment_status', 'like', $searchTerm);

                    $q->orWhereHas('organization', function ($orgQuery) use ($searchTerm) {
                        $orgQuery->where('name', 'like', $searchTerm)
                            ->orWhere('email', 'like', $searchTerm)
                            ->orWhere('mobile', 'like', $searchTerm)
                            ->orWhere('organization_id', 'like', $searchTerm);
                    });

                    $q->orWhereHas('invoice', function ($invoiceQuery) use ($searchTerm) {
                        $invoiceQuery->where('invoice_number', 'like', "%{$searchTerm}%")
                            ->orWhere('status', 'like', "%{$searchTerm}%")
                            ->orWhere('type', 'like', "%{$searchTerm}%")
                            ->orWhere('amount', 'like', "%{$searchTerm}%");
                    });
                });
            })
            ->orderByDesc('id')->paginate(20, ['*'], 'payments');
            $payments = $payment_query;

        return view('livewire.organization.org-dashboard', [
            'riders' => $riders,
            'invoices' => $invoices,
            'payments' => $payments,
        ]);
    }
}
