<?php

namespace App\Livewire\Master;

use Livewire\Component;
use App\Models\Admin;
use App\Models\Designation;
use App\Models\Branch;
use Illuminate\Support\Facades\Hash;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\DB;

class EmployeeManagementUpdate extends Component
{
   use WithFileUploads;
        public $name ,$designation, $image, $mobile, $email ,$id ,$employee ,$branch_id;
        
        public $designations = [];
        public $branches = [];
        public function mount($id)
        {   
            $this->id = $id;
            $this->designations = Designation::where('status', 1)->orderBY('name', 'ASC')->get();
            $this->branches = Branch::where('status', 1)
                        ->orderBy('name', 'ASC')
                        ->get();

            $this->employee = Admin::find($this->id);
            if(!$this->employee){
                abort(404);
            }
            $this->name = $this->employee->name;
            $this->email = $this->employee->email;
            $this->mobile = $this->employee->mobile;
            $this->designation = $this->employee->designation;
            $this->branch_id = $this->employee->branch_id;
        }

        public function GetDesignation($designation_id)
        {
            $this->designation = $designation_id;
        }

        public function BranchUpdate($value){
                $this->branch_id = $value;
                $this->resetPage();
        }

        public function saveProduct()
        {
             $this->validate([
                'designation' => 'required|exists:designations,id', // Designation must exist in the designations table
                'name' => 'required|string|max:255',
                'mobile' => 'required|string|max:15|unique:admins,mobile,' . $this->id . '|regex:/^[0-9]{10,15}$/', // Improved mobile validation
                'email' => 'required|email|max:255|unique:admins,email,' . $this->id, // Valid email format
                'image' => 'nullable|image|max:2048|mimes:jpg,jpeg,png,webp', // Increased size to 2MB (2048KB)
                'branch_id'   => 'required|exists:branches,id',
            ]);

            DB::beginTransaction();

            try {
                // Handle main image upload
                $imagePath = 'assets/img/profile-image.webp';
                if ($this->image) {
                    $imagePath = storeFileWithCustomName($this->image, 'uploads/Admin');
                }

                // Check if the employee exists
                $store = Admin::find($this->id);
                if (!$store) {
                    throw new \Exception('Employee not found.');
                }

                // Update the Employee
                $store->name = ucwords($this->name);
                $store->designation = $this->designation;
                $store->mobile = $this->mobile;
                $store->email = $this->email;
                $store->branch_id = $this->branch_id;   
                if ($this->image) {
                    $store->image = $imagePath; // Update image only if provided
                }
                $store->save();

                DB::commit();

                // Flash a success message and redirect
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
