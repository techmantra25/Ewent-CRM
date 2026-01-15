<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Organization;
use Livewire\WithFileUploads;
use Illuminate\Pagination\Paginator;
use Livewire\WithPagination;
use App\Models\OrganizationLog;
use App\Models\OrganizationDiscount;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;

class AdminOrganizationIndex extends Component
{
    use WithFileUploads, WithPagination; // Include WithPagination trait
    protected $paginationTheme = 'bootstrap';
    public $search = "";
    public $activeTab = 'list';
    public $organization_id, $name, $email, $mobile, $password, $image,$discount_percentage, $rider_visibility_percentage, $gst_number, $pan_number;
    public $gst_file, $pan_file;

    public $gst_file_preview, $pan_file_preview;
    public $street_address, $city, $state, $pincode,$edit_id = null;
    public $old_image;
    public $subscription_type = 'weekly';
    public $renewal_day;
    public $renewal_day_of_month;


     public function boot()
    {
        Paginator::useBootstrap();
    }
    public function mount(){
    }
    public function searchButtonClicked()
    {
        $this->resetPage(); // Reset to the first page
    }
     public function resetSearch()
    {
        $this->reset('search');     // Reset pagination
    }
     public function toggleStatus($id)
    {
        $organization = Organization::findOrFail($id);
        $organization->status = !$organization->status;
        $organization->save();

        session()->flash('message', 'Organization status updated successfully!');
    }

    public function AddNewOrganization(){
        $this->resetForm();
        $this->organization_id = makeOrganizationID();
        $this->activeTab = 'create';
    }

   public function saveOrganization()
    {
        $rules = [
            'name'   => 'required|string|max:255',
            'organization_id'  => 'nullable|string|unique:organizations,organization_id,' . $this->edit_id,
            'email'  => 'required|email|unique:organizations,email,' . $this->edit_id,
            'mobile' => 'required|string|max:10|unique:organizations,mobile,' . $this->edit_id,
            'subscription_type' => 'required|in:weekly,monthly',
            'discount_percentage' => 'nullable|numeric|min:0|max:99',
            'rider_visibility_percentage' => 'nullable|numeric|min:0|max:99',
        ];

        // 🔥 Fetch existing organization if updating
        $org = $this->edit_id ? Organization::find($this->edit_id) : null;

        /*
        |--------------------------------------------------------------------------
        | GST Validation
        |--------------------------------------------------------------------------
        | CREATE: Either GST number or GST file is required.
        | UPDATE: Required only if BOTH are empty (no number + no saved file).
        |--------------------------------------------------------------------------
        */
        $rules['gst_number'] = 'nullable';
        $rules['gst_file']   = 'nullable|file|mimes:jpg,png,jpeg,pdf,webp|max:2048';

        if (!$this->edit_id || (!$this->gst_number && !$org?->gst_file)) {
            // If creating OR both values empty during update → require one
            $rules['gst_number'] = 'required';
            $rules['gst_file']   = 'required|file|mimes:jpg,png,jpeg,pdf,webp|max:2048';
        }

        /*
        |--------------------------------------------------------------------------
        | PAN Validation
        |--------------------------------------------------------------------------
        | CREATE: Either PAN number or PAN file required.
        | UPDATE: Required only if BOTH are empty.
        |--------------------------------------------------------------------------
        */
        $rules['pan_number'] = 'nullable';
        $rules['pan_file']   = 'nullable|file|mimes:jpg,png,jpeg,pdf|max:2048';

        if (!$this->edit_id || (!$this->pan_number && !$org?->pan_file)) {
            $rules['pan_number'] = 'required_without:pan_file';
            $rules['pan_file']   = 'required_without:pan_number|file|mimes:jpg,png,jpeg,pdf|max:2048';
        }

        // Conditional rules
        if ($this->subscription_type === 'weekly') {
            $rules['renewal_day'] = 'required|in:sunday,monday,tuesday,wednesday,thursday,friday,saturday';
            $rules['renewal_day_of_month'] = 'nullable';
        } elseif ($this->subscription_type === 'monthly') {
            $rules['renewal_day_of_month'] = 'required|integer|min:1|max:30';
            $rules['renewal_day'] = 'nullable';
        }

        // If creating new, password required
        if (!$this->edit_id) {
            $rules['password'] = 'required|string|min:6';
        } elseif ($this->password) {
            $rules['password'] = 'string|min:6';
        }

        $this->validate($rules);

        // Track old data if updating
        $oldData = [];
        if ($this->edit_id) {
            $org = Organization::findOrFail($this->edit_id);
            $oldData = $org->toArray();
        } else {
            $oldData = null; // nothing before create
            $org = new Organization();
            $org->organization_id = $this->organization_id ?: makeOrganizationID();
        }

        $org->name = $this->name;
        $org->email = $this->email;
        $org->mobile = $this->mobile;
        if ($this->password) {
            $org->password = Hash::make($this->password);
        }
        $org->street_address = $this->street_address;
        $org->city = $this->city;
        $org->state = $this->state;
        $org->pincode = $this->pincode;
        $org->discount_percentage = $this->discount_percentage;
        $org->rider_visibility_percentage = $this->rider_visibility_percentage;
        $org->subscription_type = $this->subscription_type;
        $org->renewal_day = $this->subscription_type === 'weekly' ? $this->renewal_day : null;
        $org->renewal_day_of_month = $this->subscription_type === 'monthly' ? $this->renewal_day_of_month : null;

        $imagePath = $this->old_image ?? 'assets/img/organization.png';

        if ($this->gst_file) {
            $gstPath = storeFileWithCustomName($this->gst_file, 'uploads/organization');
            $org->gst_file = $gstPath;
        }
        if ($this->pan_file) {
            $panPath = storeFileWithCustomName($this->pan_file, 'uploads/organization');
            $org->pan_file = $panPath;
        }
        if ($this->image) {
            $imagePath = storeFileWithCustomName($this->image, 'uploads/organization');
        }

        $org->gst_number = $this->gst_number;
        $org->pan_number = $this->pan_number;
       
     

        $org->image = $imagePath;

        $org->save();

        // Track changes (only if editing)
        $newData = $org->fresh()->toArray();
        // Save log
        if ($this->edit_id) {
            // Check last active discount
            $lastDiscount = OrganizationDiscount::where('organization_id', $org->id)
                ->whereNull('end_date')
                ->latest('start_date')
                ->first();

            $isChanged = !$lastDiscount ||
                        $lastDiscount->discount_percentage != $org->discount_percentage;

            if ($isChanged) {
                if ($lastDiscount) {
                    $newStartDate = Carbon::today()->toDateString();

                    // Default close = yesterday
                    $calculatedEnd = Carbon::now()->subDay()->toDateString();

                    // But if same day, keep end_date = today instead of yesterday
                    if ($lastDiscount->start_date->toDateString() === $newStartDate) {
                        $calculatedEnd = $newStartDate;
                    }
                    $lastDiscount->update([
                        'end_date' => $calculatedEnd,
                    ]);
                }

                // Insert new discount
                OrganizationDiscount::create([
                    'organization_id'      => $org->id,
                    'discount_percentage'  => $org->discount_percentage,
                    'start_date'           => Carbon::today()->toDateString(),
                    'end_date'             => null,
                ]);
            }




            // Compare old and new
            if ($oldData && json_encode($oldData) !== json_encode($newData)) {
                OrganizationLog::create([
                    'organization_id' => $org->id,
                    'updated_by'      => auth()->id(),
                    'trigger_type'    => 'update',
                    'old_data'        => json_encode($oldData),
                    'new_data'        => json_encode($newData),
                ]);
            }
        } else {
            // Store discount history
            OrganizationDiscount::create([
                'organization_id'      => $org->id,
                'discount_percentage'  => $org->discount_percentage,
                'start_date'           => now()->toDateString(),
                'end_date'             => null, // will be closed later automatically by next create
            ]);
            // Always create log on create
            OrganizationLog::create([
                'organization_id' => $org->id,
                'updated_by'      => auth()->id(),
                'trigger_type'    => 'create',
                'old_data'        => null,
                'new_data'        => json_encode($newData),
            ]);
        }

        session()->flash('message', $this->edit_id ? 'Organization updated successfully!' : 'Organization created successfully!');
        $this->resetForm();
    }



    public function editOrganization($org_id)
    {
   
        $org = Organization::findOrFail($org_id);

        $this->edit_id = $org->id;
        $this->organization_id = $org->organization_id;
        $this->name = $org->name;
        $this->email = $org->email;
        $this->mobile = $org->mobile;
        $this->street_address = $org->street_address;
        $this->city = $org->city;
        $this->state = $org->state;
        $this->pincode = $org->pincode;
        $this->discount_percentage = rtrim(rtrim(number_format($org->discount_percentage, 2, '.', ''), '0'), '.');
        $this->rider_visibility_percentage = rtrim(rtrim(number_format($org->rider_visibility_percentage, 2, '.', ''), '0'), '.');
        $this->subscription_type = $org->subscription_type;
        $this->renewal_day = $org->renewal_day;
        $this->renewal_day_of_month = $org->renewal_day_of_month;
        $this->old_image = $org->image ?? 'assets/img/organization.png';
        $this->image = null; // reset file input
        $this->gst_number = $org->gst_number;
        $this->pan_number = $org->pan_number;

        $this->gst_file_preview = $org->gst_file;
        $this->gst_file = null;
        $this->pan_file_preview = $org->pan_file;
        $this->pan_file = null;

        $this->activeTab = 'create'; // switch to form
    }



    public function resetForm()
    {
        $this->reset(['edit_id', 'organization_id', 'name', 'email', 'mobile', 'password', 'street_address', 'city', 'state', 'pincode', 'image','subscription_type','renewal_day','renewal_day_of_month','discount_percentage','rider_visibility_percentage']);
         $this->activeTab = 'list';
    }

      public function SubscriptionTypeChange()
    {
        if ($this->subscription_type === 'weekly') {
            $this->renewal_day_of_month = null; // reset
        } elseif ($this->subscription_type === 'monthly') {
            $this->renewal_day = null; // reset
        }
    }
    public function render()
    {
      
        $organizations = Organization::when($this->search, function ($query) {
            $searchTerm = '%' . $this->search . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', $searchTerm)
                ->orWhere('organization_id', 'like', $searchTerm)
                ->orWhere('mobile', 'like', $searchTerm)
                ->orWhere('email', 'like', $searchTerm);
            });
        })
        ->orderBy('id', 'DESC')
        ->paginate(20);
        return view('livewire.admin.admin-organization-index',[
            'organizations'=>$organizations,
        ]);
    }
}
