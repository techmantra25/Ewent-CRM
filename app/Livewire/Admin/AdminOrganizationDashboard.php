<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Organization;
use App\Models\OrganizationInvoice;
use App\Models\User;
use App\Models\OrganizationDiscount;
use App\Models\OrganizationProduct;
use App\Models\Product;
use App\Models\AsignedVehicle;
use App\Models\Stock;
use App\Models\OrganizationDepositInvoice;
use Livewire\WithPagination;

class AdminOrganizationDashboard extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $vehicleStatus = "lock";
    public $riderFilter = "all";
    public $selectedVehicle = [];
    public $selectedInvoiceItem = [];
    public $invoiceWiseSelectedRider = [];
    public $page = 1;
    public $organization;
    public $allRidersCount = null;
    public $assignedVehiclesCount  = null;
    public $pendingInvoice = null;
    public $InvoicePaidAmount = 0;
    public $activeTab = 'overview';
    public $search = '';
    public $OrganizationModels;
    public $models;

     // Form fields
    public $invoice_id;
    public $invoice_number;
    public $number_of_vehicle;
    public $vehicle_price_per_piece;
    public $total_amount;
    public $isEdit = false;

    protected $rules = [
        'number_of_vehicle' => 'required|integer|min:1',
        'vehicle_price_per_piece' => 'required|numeric|min:0',
    ];

    protected $messages = [
        'number_of_vehicle.required' => 'Number of vehicles is required.',
        'number_of_vehicle.integer' => 'Vehicles must be a valid number.',
        'number_of_vehicle.min' => 'At least 1 vehicle is required.',

        'vehicle_price_per_piece.required' => 'Price per vehicle is required.',
        'vehicle_price_per_piece.numeric' => 'Price must be a valid amount.',
        'vehicle_price_per_piece.min' => 'Price cannot be negative.',
    ];
    public function mount($id){
        $this->organization = Organization::findOrFail($id);
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
        if($value=="vehicle_lock"){
            $this->reset(['vehicleStatus','riderFilter','selectedVehicle']);
            $this->ActiveRider();
        }
        $this->resetPageField();
    }
    public function FilterRider($value)
    {
        $this->search = $value;
        $this->resetPage();
    }
    public function resetPageField(){
        $this->reset(['search']);
    }
    public function assignModel($model_id){
        $existingAssignment = OrganizationProduct::where('organization_id', $this->organization->id)
            ->where('product_id', $model_id)
            ->first();

        if (!$existingAssignment) {
            OrganizationProduct::create([
                'organization_id' => $this->organization->id,
                'product_id' => $model_id,
            ]);
            session()->flash('model_success', 'Model assigned successfully.');
        } else {
            session()->flash('model_error', 'This model is already assigned to the organization.');
        }
    }
    public function deleteModel($org_model_id){
        $orgModel = OrganizationProduct::find($org_model_id);
        if ($orgModel) {
            $usersWithModel = User::where('organization_id', $this->organization->id)
                ->whereHas('active_vehicle.stock', function ($query) use ($orgModel) {
                    $query->where('product_id', $orgModel->product_id);
                })
                ->count();
            if($usersWithModel > 0){
                session()->flash('model_error', 'Cannot unassign model. There are riders currently assigned to this model.');
                return;
            }
            $orgModel->delete();
            session()->flash('model_success', 'Model unassigned successfully.');
        } else {
            session()->flash('model_error', 'Model not found.');
        }
    }

   public function resetForm()
    {
        $this->reset([
            'invoice_id',
            'invoice_number',
            'number_of_vehicle',
            'vehicle_price_per_piece',
            'total_amount',
            'isEdit'
        ]);
    }


    public function store()
    {
        $this->validate();

        OrganizationDepositInvoice::create([
            'organization_id' => auth()->user()->organization_id ?? 1,
            'invoice_number' => $this->invoice_number,
            'number_of_vehicle' => $this->number_of_vehicle,
            'vehicle_price_per_piece' => $this->vehicle_price_per_piece,
            'total_amount' => $this->total_amount,
        ]);

        $this->resetForm();
        session()->flash('success', 'Deposit invoice added successfully!');
    }

    public function edit($id)
    {
        $invoice = OrganizationDepositInvoice::findOrFail($id);

        $this->invoice_id = $invoice->id;
        $this->invoice_number = $invoice->invoice_number;
        $this->number_of_vehicle = $invoice->number_of_vehicle;
        $this->vehicle_price_per_piece = $invoice->vehicle_price_per_piece;
        $this->total_amount = $invoice->total_amount;
        $this->isEdit = true;
    }

    public function update()
    {
        $this->validate();

        OrganizationDepositInvoice::where('id', $this->invoice_id)->update([
            'number_of_vehicle' => $this->number_of_vehicle,
            'vehicle_price_per_piece' => $this->vehicle_price_per_piece,
            'total_amount' => $this->total_amount,
        ]);

        $this->resetForm();
        session()->flash('success', 'Deposit invoice updated successfully!');
    }
    public function CalculateAmount(){
        $this->total_amount =
                (float) $this->number_of_vehicle * (float) $this->vehicle_price_per_piece;
    }
    public function DepositInvoiceDelete($id)
    {
        OrganizationDepositInvoice::findOrFail($id)->delete();
    }

    public function toggleVehicle($value)
    {
        $this->vehicleStatus = $value;
        $this->ActiveRider();
    }

    public function toggleRiderFilter($value)
    {
        $this->riderFilter = $value;
        $this->ActiveRider();
    }

    public function ActiveRider(){
        $this->reset(['selectedVehicle','selectedInvoiceItem','invoiceWiseSelectedRider']);
        if($this->riderFilter=="all"){
           $selectedVehicle = User::with(['assigned_vehicle.stock'])
            ->where('user_type', 'B2B')
            ->where('organization_id', $this->organization->id)
            ->whereHas('assigned_vehicle.stock', function ($q) {
                $q->whereNotNull('vehicle_track_id');

                if ($this->vehicleStatus == 'lock') {
                    $q->where('immobilizer_status', 'MOBILIZE');
                } else {
                    $q->where('immobilizer_status', 'IMMOBILIZE');
                }
            })
            ->orderBy('id', 'DESC')
            ->get();


            // 👉 FIRST map vehicles
            $vehicles = $selectedVehicle->map(function ($user) {
                return [
                    'id' => $user->id,
                    'rider_name' => $user->name,
                    'vehicle_number' => $user->assigned_vehicle->stock->vehicle_number,
                    'vehicle_track_id' => $user->assigned_vehicle->stock->vehicle_track_id,
                    'immobilizer_status' => $user->assigned_vehicle->stock->immobilizer_status,
                ];
            });
            // 👉 table data
            $this->selectedVehicle = $vehicles;

        }else{
            $pendingInvoice = OrganizationInvoice::with('items')
                ->where('organization_id', $this->organization->id)
                ->whereIn('status', ['pending','overdue'])
                ->orderBy('created_at', 'DESC')
                ->get();

            $invoice_rider_data = [];

            foreach ($pendingInvoice as $invoice) {

                // 👉 Get all rider ids from this invoice items
                $selectedRider = $invoice->items
                    ->pluck('user_id')
                    ->unique()
                    ->toArray();

                // 👉 Get vehicles of those riders
                $selectedVehicle = User::with(['assigned_vehicle.stock'])
                    ->whereIn('id', $selectedRider)
                    ->where('user_type', 'B2B')
                    ->where('organization_id', $this->organization->id)
                    ->whereHas('assigned_vehicle.stock', function ($q) {
                        $q->whereNotNull('vehicle_track_id');

                        if ($this->vehicleStatus == 'lock') {
                            $q->where('immobilizer_status', 'MOBILIZE');
                        } else {
                            $q->where('immobilizer_status', 'IMMOBILIZE');
                        }
                    })
                    ->orderBy('id', 'DESC')
                    ->get();

                // 👉 Format vehicles array
                $vehicles = $selectedVehicle->map(function ($user) {
                    return [
                        'id' => $user->id,
                        'rider_name' => $user->name,
                        'vehicle_number' => optional($user->assigned_vehicle->stock)->vehicle_number,
                        'vehicle_track_id' => optional($user->assigned_vehicle->stock)->vehicle_track_id,
                        'immobilizer_status' => optional($user->assigned_vehicle->stock)->immobilizer_status,
                    ];
                })->values()->toArray();

                // 👉 Push invoice-wise data (IMPORTANT)
                $invoice_rider_data[] = [
                    'invoice_number' => $invoice->invoice_number,
                    'billing_start_date' => $invoice->billing_start_date,
                    'billing_end_date' => $invoice->billing_end_date,
                    'status' => $invoice->status,
                    'vehicles' => $vehicles,
                ];
            }

            // Final result
            $this->selectedInvoiceItem = $invoice_rider_data;

        }
        
    }

    public function toggleInvoiceVehicles($userIds, $checked)
    {
        $ids = array_filter(explode(',', $userIds));

        if ($checked) {
            //  ADD riders
            $this->invoiceWiseSelectedRider = array_unique(
                array_merge($this->invoiceWiseSelectedRider, $ids)
            );
        } else {
            //  REMOVE riders
            $this->invoiceWiseSelectedRider = array_values(
                array_diff($this->invoiceWiseSelectedRider, $ids)
            );
        }

        // remove duplicates safety
        $this->invoiceWiseSelectedRider = array_values(
            array_unique($this->invoiceWiseSelectedRider)
        );

        // Fetch riders from DB
        $users = User::with(['assigned_vehicle.stock'])
            ->whereIn('id', $this->invoiceWiseSelectedRider)
            ->where('user_type', 'B2B')
            ->where('organization_id', $this->organization->id)
            ->whereHas('assigned_vehicle.stock', function ($q) {
                $q->whereNotNull('vehicle_track_id');

                if ($this->vehicleStatus == 'lock') {
                    $q->where('immobilizer_status', 'MOBILIZE');
                } else {
                    $q->where('immobilizer_status', 'IMMOBILIZE');
                }
            })
            ->get();

        // Format array
        $vehicles = $users->map(function ($user) {
            return [
                'id' => $user->id,
                'rider_name' => $user->name,
                'vehicle_number' => optional($user->assigned_vehicle->stock)->vehicle_number,
                'vehicle_track_id' => optional($user->assigned_vehicle->stock)->vehicle_track_id,
                'immobilizer_status' => optional($user->assigned_vehicle->stock)->immobilizer_status,
            ];
        })->toArray();
        $this->selectedVehicle = $vehicles;
    }


    public function MobilizationRequest($vehicle_id, $value)
    {
        // dd($value);
        $url = 'https://app.loconav.sensorise.net/integration/api/v1/vehicles/'.$vehicle_id.'/immobilizer_requests';

        $payload = ["value" => $value];

        $ch = curl_init($url);
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
        // dd($response);
        // ❌ API not responding / invalid JSON
        if (!$response || !isset($response['success'])) {
            session()->flash('error', 'API not responding. Please try again.');
            return false;
        }

        // ❌ API returned failure
        if ($response['success'] === false) {
            $msg = $response['message'] ?? 'Something went wrong.';
            session()->flash('error', $msg);
            return false;
        }

        // ❌ API returned business error inside data.errors (IMPORTANT)
        if (isset($response['data']['errors'])) {
            session()->flash('error', $response['data']['errors']);
            return false;
        }

        // ✅ SUCCESS CASES
        $stock = Stock::where('vehicle_track_id', $vehicle_id)->first();

        if ($stock) {

            if ($value == "IMMOBILIZE" && isset($response['data']['id'])) {
                $stock->immobilizer_request_id = $response['data']['id'];
                $stock->immobilizer_status = "IMMOBILIZE";
            }

            if ($value == "MOBILIZE") {
                $stock->immobilizer_request_id = null;
                $stock->immobilizer_status = "MOBILIZE";
            }

            $stock->save();
        }

        session()->flash('success', 'Vehicle status updated successfully.');
        return true;
    }

    public function lockUnlockAllVehicles()
    {
        if(empty($this->selectedVehicle)){
            session()->flash('error', 'No vehicles found');
            return;
        }

        $successCount = 0;
        $failCount = 0;

        foreach($this->selectedVehicle as $item){
            // Decide action
            if($item['immobilizer_status'] == "MOBILIZE"){
                $value = "IMMOBILIZE";
            }else{
                $value = "MOBILIZE";
            }

            $result = $this->MobilizationRequest($item['vehicle_track_id'], $value);

            if($result){
                $successCount++;
            }else{
                $failCount++;
            }
        }

        // 🎉 Final message
        if($successCount > 0 && $failCount == 0){
            $msg = $this->vehicleStatus == 'lock'
                ? 'All vehicles locked successfully'
                : 'All vehicles unlocked successfully';

            session()->flash('success', $msg);

        }elseif($successCount > 0 && $failCount > 0){
            session()->flash('error', 'Some vehicles failed to update');

        }else{
            session()->flash('error', 'Failed to update vehicles');
        }

        // refresh list
        $this->vehicleStatus=='lock'?'unlock':'lock';
        $this->ActiveRider();
    }



    public function render()
    {
        if (!$this->isEdit && empty($this->invoice_number)) {
            $this->invoice_number = makeOrganizationDepositInvoiceID();
        }
        $this->models = Product::where('status', 1)->orderBy('title', 'ASC')->get();
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

            $deposit_invoices = OrganizationDepositInvoice::where('organization_id', $this->organization->id)
            ->when($this->search, function ($query) {
                $searchTerm = '%' . $this->search . '%';

                $query->where(function ($q) use ($searchTerm) {
                    $q->where('invoice_number', 'like', $searchTerm)
                    ->orWhere('type', 'like', $searchTerm)
                    ->orWhere('status', 'like', $searchTerm)
                    ->orWhere('total_amount', 'like', $searchTerm)
                    ->orWhere('payment_date', 'like', $searchTerm);
                });
            })
            ->orderByDesc('id')
            ->paginate(10, ['*'], 'deposit_invoices');


            $this->allRidersCount = $riders->total();


        return view('livewire.admin.admin-organization-dashboard', [
            'riders' => $riders,
            'invoices' => $invoices,
            'deposit_invoices' => $deposit_invoices,
        ]);
    }
}
