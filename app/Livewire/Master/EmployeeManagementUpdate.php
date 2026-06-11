<?php

namespace App\Livewire\Master;

use Livewire\Component;
use App\Models\Admin;
use App\Models\Designation;
use App\Models\Branch;
use App\Models\City;
use Illuminate\Support\Facades\Hash;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\DB;

class EmployeeManagementUpdate extends Component
{
    use WithFileUploads;
    
    public $name, $designation, $image, $mobile, $email, $id, $employee, $city_id, $branch_id;
    
    public $designations = [];
    public $cities = [];   
    public $branches = [];

    public function mount($id)
    {   
        $this->id = $id;
        $this->designations = Designation::where('status', 1)->orderBy('name', 'ASC')->get();
        
        $this->cities = City::with('state')->where('status', 1)->orderBy('name', 'ASC')->get();

        $this->employee = Admin::find($this->id);
        if (!$this->employee) {
            abort(404);
        }
        
        $this->name = $this->employee->name;
        $this->email = $this->employee->email;
        $this->mobile = $this->employee->mobile;
        $this->designation = $this->employee->designation;
        $this->branch_id = $this->employee->branch_id;

        if ($this->branch_id) {
            $currentBranch = Branch::find($this->branch_id);
            if ($currentBranch) {
                $this->city_id = $currentBranch->city_id; 
                $this->branches = Branch::where('city_id', $this->city_id)
                                        ->where('status', 1)
                                        ->orderBy('name', 'ASC')
                                        ->get();
            }
        } else {
            $this->branches = collect();
        }
    }

    public function updatedCityId($value)
    {
        if (!empty($value)) {
            $this->branches = Branch::where('city_id', $value)
                                    ->where('status', 1)
                                    ->orderBy('name', 'ASC')
                                    ->get();
        } else {
            $this->branches = collect();
        }
        $this->branch_id = null; 
    }

    public function GetDesignation($designation_id)
    {
        $this->designation = $designation_id;
    }

    public function BranchUpdate($value)
    {
        $this->branch_id = $value;
    }

    public function saveProduct()
    {
        $rules = [
            'designation' => 'required|exists:designations,id',
            'name' => 'required|string|max:255',
            'mobile' => 'required|string|max:15|unique:admins,mobile,' . $this->id . '|regex:/^[0-9]{10,15}$/',
            'email' => 'required|email|max:255|unique:admins,email,' . $this->id,
            'image' => 'nullable|image|max:2048|mimes:jpg,jpeg,png,webp',
        ];

        if (auth('admin')->user()->branch_id == 1) {
            $rules['branch_id'] = 'required|exists:branches,id';
        }

        $this->validate($rules);

        DB::beginTransaction();

        try {
            $imagePath = 'assets/img/profile-image.webp';
            if ($this->image) {
                $imagePath = storeFileWithCustomName($this->image, 'uploads/Admin');
            }

            $store = Admin::find($this->id);
            if (!$store) {
                throw new \Exception('Employee not found.');
            }

            $store->name = ucwords($this->name);
            $store->designation = $this->designation;
            $store->mobile = $this->mobile;
            $store->email = $this->email;
            $store->branch_id = $this->branch_id ?: current_branch();
             
            if ($this->image) {
                $store->image = $imagePath;
            }
            $store->save();

            DB::commit();

            session()->flash('message', 'Employee updated successfully!');
            return redirect()->route('admin.employee.list');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error updating user: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.master.employee-management-update');
    }
}