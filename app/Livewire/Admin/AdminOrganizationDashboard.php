<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Organization;
use App\Models\OrganizationInvoice;
use App\Models\User;
use App\Models\OrganizationDiscount;
use App\Models\OrganizationProduct;
use App\Models\Product;
use Livewire\WithPagination;

class AdminOrganizationDashboard extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
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
    public function render()
    {
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


            $this->allRidersCount = $riders->total();


        return view('livewire.admin.admin-organization-dashboard', [
            'riders' => $riders,
            'invoices' => $invoices,
        ]);
    }
}
