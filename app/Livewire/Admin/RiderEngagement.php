<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use App\Models\Order;
use App\Models\Stock;
use App\Models\Payment;
use App\Models\PaymentItem;
use App\Models\AsignedVehicle;
use App\Models\UserKycLog;
use App\Models\Organization;
use App\Models\CancelRequestHistory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;


class RiderEngagement extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $page = 1;
    public $search = '';
    public $remarks,$field,$document_type,$id,$vehicle_model;
    public $active_tab = 1;
    public $organizations = [];
    public $vehicles = [];
    public $customers = [];
    public $selectedCustomer = null; // Stores the selected customer data
    public $isModalOpen = false; // Track modal visibility
    public $isRejectModal = false;
    public $isAssignedModal = false;
    public $isExchangeModal = false;
    public $closeAssignedtModal = false;
    public $isPreviewimageModal = false;
    public $targetRiderId;
    public $targetOrderId;
    public $preview_front_image, $preview_back_image,$selected_organization;

    /**
     * Search button click handler to reset pagination.
     */
    public function mount(){
        $this->organizations = Organization::select('id', 'name')->orderBy('name', 'ASC')->get()->toArray();
    }
    public function OrganizationUpdate($value){
        $this->selected_organization = $value;
        $this->resetPage();
    }
    public function btn_search()
    {
        $this->resetPage(); // Reset to the first page
    }
    public function updateLog($status,$field,$document_type,$id){
        $user = User::find($id);
        if (!$user) {
            session()->flash('modal_message', 'User not found.');
            return false;
        }

        // Check if the provided field exists in the User model
        if (!Schema::hasColumn('users', $field)) {
            session()->flash('modal_message', 'Invalid field name.');
            return false;
        }
        $remarks = null;
        $doc_status = 'approved';
        $message = $document_type." is successfully verified for KYC.";
        if($status==3){
            $user->date_of_rejection = date('Y-m-d h:i:s');
            $user->rejected_by = Auth::guard('admin')->user()->id;
            $user->is_verified = "rejected";
           

            if(empty($this->remarks)){
                session()->flash('remarks', 'Please enter a remark for the rejection reason.');
                return false;
            }
            $user->save();
            $doc_status = 'rejected';
            $remarks = $this->remarks;
            $message = $document_type." has been rejected. Please upload a valid document.";
        }

        $log = UserKycLog::create([
            'user_id' => $user->id,
            'document_type' => $document_type,
            'status' => $status,
            'remarks' => $remarks,
            'message' => $message,
            'created_by' => Auth::guard('admin')->user()->id, // Corrected Auth syntax
        ]);
        // Update the field value and save
        $user->$field = $status;
        $user->save();

         sendPushNotification($user->id, 'document_status_update', [
            'document_name' => $document_type,
            'status' => $doc_status,
        ]);
        $this->showCustomerDetails($user->id);
        $this->closeRejectModal();
        session()->flash('modal_message', 'Status updated successfully.');
    }

    public function OpenRejectForm($field, $document_type, $id)
    {
        $this->field = $field;
        $this->document_type = $document_type;
        $this->id = $id; // Changed from $this->id to avoid conflicts
        $this->isRejectModal = true;
    }
    public function OpenAssignedForm($rider_id,$product_id,$order_id)
    {
        $this->targetRiderId = $rider_id;
        $this->targetOrderId = $order_id;
        $this->vehicles = Stock::whereDoesntHave('assignedVehicle')->whereDoesntHave('overdueVehicle')->where('product_id', $product_id)->orderBy('vehicle_number')->get();
        $this->isAssignedModal = true;
    }
    public function OpenExchangeForm($rider_id,$product_id,$order_id,$vehicle_number)
    {
        $this->targetRiderId = $rider_id;
        $this->targetOrderId = $order_id;
        $this->vehicles = Stock::where('product_id', $product_id)
        ->where('vehicle_number', '!=', $vehicle_number)
        ->whereDoesntHave('assignedVehicle')
        ->whereDoesntHave('overdueVehicle')
        ->orderBy('vehicle_number')
        ->get();

        $this->isExchangeModal = true;
    }

    public function closeExchangeModal()
    {
        $this->isExchangeModal = false;
        $this->reset(['vehicle_model']);
    }

    public function updateAssignRider(){
        try {
            if (!$this->vehicle_model) {
                session()->flash('assign_error', 'Please select vehicle model first.');
                    return false;
            }
            $assignRider = AsignedVehicle::where('order_id', $this->targetOrderId)->first();
            if ($assignRider) {
                session()->flash('assign_error', 'Sorry! A vehicle has already been assigned for this rider.');
                return false;
            }

            $order = Order::find($this->targetOrderId);

            if (!$order) {
                session()->flash('assign_error', 'Sorry! Something went wrong. Please reload the page and try again.');
                return false;
            }
            if (!$order->rent_duration && $order->user_type=="B2C") {
                session()->flash('assign_error', 'Sorry! Rent duration not found. Please set the rent duration before proceeding.');
                return false;
            }
            $rider = User::find($this->targetRiderId);
            if($rider->org_is_verified != "verified" && $rider->user_type =="B2B"){
               session()->flash(
                    'assign_error',
                    'Rider KYC is not verified by the organization. Please wait or contact the organization for verification.'
                );
                return false;
            }
            DB::beginTransaction();

                $startDate = Carbon::now();
                $endDate = $startDate->copy()->addDays($order->rent_duration);

                $log = AsignedVehicle::create([
                    'user_id' => $this->targetRiderId,
                    'order_id' => $this->targetOrderId,
                    'vehicle_id' => $this->vehicle_model,
                    'start_date' => $startDate,
                    'end_date' => $order->user_type=="B2C"?$startDate->copy()->addDays($order->rent_duration):NULL,
                    'assigned_at' => $startDate,
                    'amount'     => $order->final_amount,
                    'deposit_amount'  => $order->final_amount - $order->rental_amount,
                    'rental_amount'   => $order->rental_amount,
                    'assigned_by' => Auth::guard('admin')->user()->id, // Corrected Auth syntax
                ]);

                $order->rent_status = "active";
                $order->rent_start_date = $startDate;
                $order->rent_end_date = $order->user_type=="B2C"?$startDate->copy()->addDays($order->rent_duration):NULL;
                $order->save();

                if($order->user_type=="B2C"){
                    $payment = Payment::where('order_id', $order->id)
                    ->where('order_type', $order->subscription_type)
                    ->latest('id')
                    ->first();

                    if ($payment) {
                        PaymentItem::where('payment_id', $payment->id)
                            ->where('payment_for',  $order->subscription_type)
                            ->update(['vehicle_id' => $this->vehicle_model]);
                    }
                }
                
            
            DB::commit();

            $new_vehicle = Stock::find($this->vehicle_model); // simpler than where+first

            if ($new_vehicle) {
                $data = [
                    'vehicle_number' => $new_vehicle->vehicle_number,
                ];

                // send push only if vehicle exists
                sendPushNotification($this->targetRiderId, 'assign_vehicle', $data);
            } else {
                // Optionally log or skip silently
                \Log::info("Vehicle not found for ID: {$this->vehicle_model}, skipping push notification.");
            }

            session()->flash('message', 'Vehicle assigned to rider successfully.');
            $this->isAssignedModal = false;
            $this->active_tab = 4;
            $this->reset(['vehicle_model','targetOrderId','targetRiderId']);

        } catch (\Exception $e) {
            DB::rollBack();
        //    dd($e->getMessage());
            session()->flash('assign_error', 'An unexpected error occurred. Please try again later.');
            return false;
        }

    }
    public function updateExchangeModel(){
        try {
            if (!$this->vehicle_model) {
                session()->flash('exchange_error', 'Please select vehicle model first.');
                    return false;
            }

            DB::beginTransaction();

            $assignRider = AsignedVehicle::where('order_id', $this->targetOrderId)->first();

            $order = Order::find($this->targetOrderId);
                $old_vehicle = Stock::where('id',$assignRider->vehicle_id)->first();
                DB::table('exchange_vehicles')->insert([
                    'user_id'      => $assignRider->user_id,
                    'order_id'     => $assignRider->order_id,
                    'vehicle_id'   => $assignRider->vehicle_id,
                    'start_date'   => $assignRider->start_date,
                    'end_date'     => now(),
                    'exchanged_by' => Auth::guard('admin')->user()->id, // Fixed typo (extra space)
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ]);

                $assignRider->vehicle_id = $this->vehicle_model;
                $assignRider->assigned_by = Auth::guard('admin')->user()->id;
                $assignRider->save();

                if($order->user_type=="B2C"){
                    $payment = Payment::where('order_id', $assignRider->order_id)
                    ->where('order_type', $order->subscription_type)
                    ->latest('id')
                    ->first();

                    if ($payment) {
                        PaymentItem::where('payment_id', $payment->id)
                            ->where('payment_for', $order->subscription_type)
                            ->update(['vehicle_id' => $this->vehicle_model]);
                    }
                }
                
            DB::commit();
            $new_vehicle = Stock::find($assignRider->vehicle_id); // simpler than where+first

            if ($new_vehicle) {
                $data =[
                    'old_vehicle_number' =>$old_vehicle->vehicle_number,
                    'new_vehicle_number' =>$new_vehicle->vehicle_number
                ];

                sendPushNotification($assignRider->user_id, 'exchange_vehicle', $data);
            }else{
                // Optionally log or skip silently
                \Log::info("New vehicle not found for ID: {$assignRider->vehicle_id}, skipping push notification.");
            }

            session()->flash('message', 'Vehicle exchange to rider successfully.');
            $this->isExchangeModal = false;
            $this->active_tab = 4;
            $this->reset(['vehicle_model','targetOrderId','targetRiderId']);

        } catch (\Exception $e) {
            DB::rollBack();
            dd($e->getMessage());
            session()->flash('exchange_error', 'An unexpected error occurred. Please try again later.');
            return false;
        }

    }
    public function OpenPreviewImage($front_image, $back_image,$document_type)
    {
        $this->preview_front_image = $front_image;
        $this->preview_back_image = $back_image;
        $this->document_type = $document_type;
        $this->isPreviewimageModal = true;
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

                $user->kyc_verified_at = now()->toDateTimeString();
                $user->kyc_verified_by = Auth::guard('admin')->user()->id;
                $user->is_verified = "verified";
                $user->date_of_rejection = NULL;
                $user->rejected_by = NULL;
                
                esign_pdf_generate($user->email);
                sendPushNotification($user->id, 'kyc_verified', $data = []);

            }elseif($status=="rejected"){
                $user->date_of_rejection = now()->toDateTimeString();
                $user->rejected_by = Auth::guard('admin')->user()->id;
                $user->is_verified = "rejected";

                sendPushNotification($user->id, 'kyc_rejected', $data = []);
            }else{
                $user->kyc_verified_at = now()->toDateTimeString();
                $user->kyc_verified_by = Auth::guard('admin')->user()->id;
                $user->is_verified = "unverified";
                 $user->date_of_rejection = NULL;
                $user->rejected_by = NULL;
            }
            $user->save();
            $this->showCustomerDetails($id);
            // Optionally, show a confirmation message
            session()->flash('modal_message', 'KYC status updated successfully.');
        }
    }

    public function closePreviewImage()
    {
        $this->isPreviewimageModal = false;
        $this->reset(['preview_front_image', 'preview_back_image','document_type']);
    }
    public function closeRejectModal()
    {
        $this->isRejectModal = false;
        $this->reset(['remarks', 'field','document_type', 'id']);
    }

    public function closeAssignedModal()
    {
        $this->isAssignedModal = false;
        $this->reset(['vehicle_model']);
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

    public function updateStatus($id, $document_type, $status)
    {
        $update = User::where('id', $id)->first();
        $update->$document_type = $status;
        $update->save();
        $this->showCustomerDetails($id);
        // Optionally, show a confirmation message
        session()->flash('modal_message', 'Status updated successfully.');
    }
    /**
     * Refresh button click handler to reset the search input and reload data.
     */
    public function reset_search(){
        $this->reset(['search','selected_organization']); // Reset the search term
        $this->resetPage();     // Reset pagination
    }
    public function toggleStatus($id){
        $user = User::findOrFail($id);
        $user->status = !$user->status;
        $user->save();
        session()->flash('message', 'Customer status updated successfully.');
    }

    public function tab_change($value){
        $this->active_tab = $value;
        $this->search = "";
        $this->resetPage();
    }

    public function confirmDeallocate($order_id){
        $this->dispatch('showConfirm', ['itemId' => $order_id]);
    }
    public function suspendRiderWarning($id){
        $this->dispatch('showWarningConfirm', ['itemId' => $id]);
    }
    public function activeRiderWarning($id){
        $this->dispatch('showactiveRiderWarning', ['itemId' => $id]);
    }
    public function updateUserData($itemId)
    {
        DB::beginTransaction();

        try {
            $order = Order::find($itemId);
            if (!$order) {
                session()->flash('error', 'Order not found!');
                return;
            }

            $AsignedVehicle = AsignedVehicle::where('order_id', $itemId)->first();
            if (!$AsignedVehicle) {
                session()->flash('error', 'Assigned vehicle not found!');
                return;
            }

            // Log exchange
            DB::table('exchange_vehicles')->insert([
                'status'       => "returned",
                'user_id'      => $AsignedVehicle->user_id,
                'order_id'     => $AsignedVehicle->order_id,
                'vehicle_id'   => $AsignedVehicle->vehicle_id,
                'start_date'   => $AsignedVehicle->start_date,
                'amount'       => $AsignedVehicle->amount,
                'deposit_amount'    => $AsignedVehicle->deposit_amount,
                'rental_amount'     => $AsignedVehicle->rental_amount,
                'end_date'     => $order->user_type=="B2C"?$AsignedVehicle->end_date:now(),
                'exchanged_by' => Auth::guard('admin')->user()->id,
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);

            $AsignedVehicle->delete();

            if ($order->cancel_request == "Yes") {
                CancelRequestHistory::create([
                    'type'          => "accepted",
                    'order_id'      => $AsignedVehicle->order_id,
                    'user_id'       => $AsignedVehicle->user_id,
                    'vehicle_id'    => $AsignedVehicle->vehicle_id,
                    'request_date'  => $order->cancel_request_at,
                    'accepted_date' => now(),
                    'accepted_by'   => Auth::guard('admin')->user()->id,
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ]);
            }

            // Update order
            $order->return_date = now();
            $order->rent_status = 'returned';
            $order->cancel_request = 'No';
            if($order->user_type=='B2B'){
                $order->rent_end_date = now();
            }
            $order->cancel_request_at = null;
            $order->save();

            DB::commit();

            $new_vehicle = Stock::find($AsignedVehicle->vehicle_id); // simpler than where+first

            if ($new_vehicle) {
                $data = [
                    'vehicle_number' => $new_vehicle->vehicle_number,
                ];
                // send push only if vehicle exists
                sendPushNotification($AsignedVehicle->user_id, 'deallocate_vehicle', $data);
            }else{
                \Log::info("New vehicle not found for ID: {$AsignedVehicle->vehicle_id}, skipping push notification.");
            }
            $this->reset_search();
            session()->flash('success', 'The vehicle has been deallocated for this user!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Vehicle update failed: ' . $e->getMessage());
            session()->flash('error', $e->getMessage());
        }
    }


    public function suspendRider($itemId){
        if($itemId){
            $user = User::find($itemId);
            $user->vehicle_assign_status = 'suspended';
            $user->suspended_by = Auth::guard('admin')->user()->id;
            $user->save();

            // dd($user);
            // $order = Order::find($order_id);
            // $order->rent_status = "deallocated";
            // $order->save();
            // $AsignedVehicle = AsignedVehicle::where('order_id', $order_id)->first();
            // $AsignedVehicle->status = "deallocated";
            // $AsignedVehicle->assigned_by = Auth::guard('admin')->user()->id;
            // $AsignedVehicle->save();
            session()->flash('success', 'The rider has been suspended and deallocated for this vehicle.');
        }
    }
    public function activeRider($itemId){
        if($itemId){
            $user = User::find($itemId);
            $user->vehicle_assign_status = NULL;
            $user->suspended_by = NULL;
            $user->save();
            session()->flash('success', 'The rider has been activated for ride.');
        }
    }
    public function gotoPage($value, $pageName = 'page')
    {
        $this->setPage($value, $pageName);
        $this->page = $value;
    }

    public function render()
    {
        $searchTerm = '%' . $this->search . '%';
        
        // Await users
        $await_users = User::whereHas('accessToken')
            ->when($this->search, function ($query) use ($searchTerm) {
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('name', 'like', $searchTerm)
                    ->orWhere('mobile', 'like', $searchTerm)
                    ->orWhere('email', 'like', $searchTerm)
                    ->orWhere('customer_id', 'like', $searchTerm)
                    ->orWhereHas('organization_details', function ($q3) use ($searchTerm) {
                        $q3->where('name', 'like', $searchTerm)
                            ->orWhere('organization_id', 'like', $searchTerm)
                            ->orWhere('email', 'like', $searchTerm)
                            ->orWhere('mobile', 'like', $searchTerm);
                    });
                });
            })
            ->when($this->selected_organization, function ($query){
                $query->where('organization_id', $this->selected_organization);
            })
            ->doesntHave('await_order')
            ->where('is_verified', 'verified')
            ->whereNull('vehicle_assign_status')
            ->orderBy('id', 'DESC')
            ->paginate(20);

        // Ready to assign
        $ready_to_assigns = User::when($this->search, function ($query) use ($searchTerm) {
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('name', 'like', $searchTerm)
                    ->orWhere('mobile', 'like', $searchTerm)
                    ->orWhere('email', 'like', $searchTerm)
                    ->orWhere('customer_id', 'like', $searchTerm)
                    ->orWhereHas('organization_details', function ($q3) use ($searchTerm) {
                        $q3->where('name', 'like', $searchTerm)
                            ->orWhere('organization_id', 'like', $searchTerm)
                            ->orWhere('email', 'like', $searchTerm)
                            ->orWhere('mobile', 'like', $searchTerm);
                    });
                });
            })
            ->when($this->selected_organization, function ($query){
                $query->where('organization_id', $this->selected_organization);
            })
            ->whereHas('ready_to_assign_order')
            ->where('is_verified', 'verified')
            ->orderBy('id', 'DESC')
            ->paginate(20);

        // Active users
        $active_users = User::where('is_verified', 'verified')
            ->whereHas('active_order')
            ->when($this->search, function ($query) use ($searchTerm) {
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
                    })
                    ->orWhereHas('organization_details', function ($q3) use ($searchTerm) {
                        $q3->where('name', 'like', $searchTerm)
                            ->orWhere('organization_id', 'like', $searchTerm)
                            ->orWhere('email', 'like', $searchTerm)
                            ->orWhere('mobile', 'like', $searchTerm);
                    });
                });
            })
            ->when($this->selected_organization, function ($query){
                $query->where('organization_id', $this->selected_organization);
            })
            ->orderBy('id', 'DESC')
            ->paginate(20);

        // All users = active + inactive (merged)
        $all_users = User::with('doc_logs','latest_order','active_vehicle')
            ->when($this->selected_organization, function ($query){
                $query->where('organization_id', $this->selected_organization);
            })
            ->when($this->search, function ($query) {
                $searchTerm = '%' . $this->search . '%';
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('name', 'like', $searchTerm)
                    ->orWhere('mobile', 'like', $searchTerm)
                    ->orWhere('email', 'like', $searchTerm)
                    ->orWhere('customer_id', 'like', $searchTerm)
                    ->orWhereHas('organization_details', function ($q3) use ($searchTerm) {
                        $q3->where('name', 'like', $searchTerm)
                            ->orWhere('organization_id', 'like', $searchTerm)
                            ->orWhere('email', 'like', $searchTerm)
                            ->orWhere('mobile', 'like', $searchTerm);
                    })
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
            ->where('is_verified', 'verified')
            ->whereNull('vehicle_assign_status')
            ->orderBy('id', 'DESC')
            ->paginate(20);
        // $inactive_users = User::with('doc_logs')
        //     ->whereDoesntHave('accessToken')
        //     ->when($this->search, function ($query) use ($searchTerm) {
        //         $query->where(function ($q) use ($searchTerm) {
        //             $q->where('name', 'like', $searchTerm)
        //             ->orWhere('mobile', 'like', $searchTerm)
        //             ->orWhere('email', 'like', $searchTerm)
        //             ->orWhere('customer_id', 'like', $searchTerm);
        //         });
        //     })
        //     ->where('is_verified', 'verified')
        //     ->orderBy('id', 'DESC')
        //     ->get();
        // Merge and paginate manually
        // $merged = $all_users_active->merge($inactive_users)->sortByDesc('id')->values();
        // $perPage = 20;
        // $currentPage = (int) ($this->page ?? 1);
        // $all_users = new \Illuminate\Pagination\LengthAwarePaginator(
        //     $merged->forPage($currentPage, $perPage),
        //     $merged->count(),
        //     $perPage,
        //     $currentPage,
        //     ['path' => request()->url(), 'query' => request()->query()]
        // );

        // Inactive rider
        $inactive_rider = User::with('doc_logs')
            ->whereDoesntHave('accessToken')
            ->when($this->search, function ($query) use ($searchTerm) {
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('name', 'like', $searchTerm)
                    ->orWhere('mobile', 'like', $searchTerm)
                    ->orWhere('email', 'like', $searchTerm)
                    ->orWhere('customer_id', 'like', $searchTerm);
                });
            })
            ->when($this->selected_organization, function ($query){
                $query->where('organization_id', $this->selected_organization);
            })
            ->where('is_verified', 'verified')
            ->orderBy('id', 'DESC')
            ->paginate(20);

        // Suspended users
        $suspended_users = User::with('doc_logs')
            ->when($this->search, function ($query) use ($searchTerm) {
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('name', 'like', $searchTerm)
                    ->orWhere('mobile', 'like', $searchTerm)
                    ->orWhere('email', 'like', $searchTerm)
                    ->orWhere('customer_id', 'like', $searchTerm);
                });
            })
            ->when($this->selected_organization, function ($query){
                $query->where('organization_id', $this->selected_organization);
            })
            ->where('vehicle_assign_status', 'suspended')
            ->orderBy('id', 'DESC')
            ->paginate(20);
        $cancel_requested_users = User::where('is_verified', 'verified')
        ->whereHas('cancel_requested_order')
        ->when($this->search, function ($query) use ($searchTerm) {
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
        ->orderBy('id', 'DESC')
        ->paginate(20);
        return view('livewire.admin.rider-engagement', [
            'all_users' => $all_users,
            'await_users' => $await_users,
            'ready_to_assigns' => $ready_to_assigns,
            'active_users' => $active_users,
            'inactive_users' => $inactive_rider,
            'suspended_users' => $suspended_users,
            'cancel_requested_users' => $cancel_requested_users,
        ]);
    }
}
