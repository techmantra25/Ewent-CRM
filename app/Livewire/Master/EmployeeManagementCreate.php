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

class EmployeeManagementCreate extends Component
{
    use WithFileUploads;
    
    public $name, $designation, $image, $mobile, $email, $city_id, $branch_id;
    
    public $designations = [];
    public $cities = [];  
    public $branches = [];

    public function mount()
    {
        $this->designations = Designation::where('status', 1)->orderBy('name', 'ASC')->get();
        $this->cities = City::with('state')->where('status', 1)->orderBy('name', 'ASC')->get();
        $this->branches = collect(); 
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

    public function saveProduct()
    {
         $this->validate([
            'designation' => 'required|exists:designations,id',
            'name' => 'required|string|max:255',
            'mobile' => 'required|string|max:15|unique:admins,mobile|regex:/^[0-9]{10,15}$/',
            'email' => 'required|email|max:255|unique:admins,email',
            'image' => 'nullable|image|max:2048|mimes:jpg,jpeg,png,webp',
            'city_id' => 'required|exists:cities,id', 
            'branch_id' => 'required|exists:branches,id',
        ]);

        DB::beginTransaction();

        try {
            $imagePath = 'assets/img/profile-image.webp';
            if ($this->image) {
                $imagePath = storeFileWithCustomName($this->image, 'uploads/Admin');
            }

            $store = new Admin;
            $store->name = ucwords($this->name);
            $store->designation = $this->designation;
            $store->mobile = $this->mobile;
            $store->email = $this->email;
            $store->branch_id = $this->branch_id;
            $store->image = $imagePath;
            $store->password = Hash::make(123456);
            $store->save();

            DB::commit();

            session()->flash('message', 'Employee created successfully!');
            return redirect()->route('admin.employee.list');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error creating user: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.master.employee-management-create');
    }
}