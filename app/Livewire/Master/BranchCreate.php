<?php

namespace App\Livewire\Master;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Branch;
use App\Models\Admin;
use App\Models\City;
use App\Models\Designation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class BranchCreate extends Component
{
    use WithFileUploads;

    public $branch_name, $branch_code, $address, $city_id;

    public $name, $designation, $mobile, $email, $image;

    public $cities = [];
    public $designations = [];

    public function mount()
    {
        $this->cities = City::orderBy('name','ASC')->get();
        $this->designations = Designation::orderBy('name','ASC')->get();
        $this->branch_code = $this->generateBranchCode();
    }

    public function save()
    {
        $this->validate([
            'branch_name' => 'required|string|max:255',
            'city_id'     => 'required|exists:cities,id',
            'address'     => 'required|string|max:500',
            'designation' => 'required|exists:designations,id',
            'name'        => 'required|string|max:255',
            'mobile'      => 'required|string|max:15|unique:admins,mobile|regex:/^[0-9]{10,15}$/',
            'email'       => 'required|email|max:255|unique:admins,email',
            'image'       => 'nullable|image|max:2048|mimes:jpg,jpeg,png,webp',
        ]);

        DB::beginTransaction();

        try {

            $this->branch_code = $this->generateBranchCode();

            $branch = Branch::create([
                'name'        => ucwords($this->branch_name),
                'branch_code' => $this->branch_code,
                'address'     => $this->address,
                'city_id'     => $this->city_id,
                'status'      => 1,
            ]);

            $imagePath = 'assets/img/profile-image.webp';

            if ($this->image) {
                $imagePath = storeFileWithCustomName($this->image, 'uploads/Admin');
            }
            $admin = new Admin();
            $admin->name = ucwords($this->name);
            $admin->designation = $this->designation;
            $admin->mobile = $this->mobile;
            $admin->email = $this->email;
            $admin->image = $imagePath;
            $admin->password = Hash::make(123456);
            $admin->branch_id = $branch->id;
            $admin->save();

            DB::commit();

            session()->flash('message', 'Branch & Employee created successfully!');
            return redirect()->route('admin.branch.list');

        } catch (\Exception $e) {

            DB::rollBack();
            session()->flash('error', 'Error: '.$e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.master.branch-create');
    }

    private function generateBranchCode()
    {
        $lastBranch = Branch::orderBy('id', 'desc')->first();

        if (!$lastBranch) {
            return 'BR00001';
        }
        // Extract numeric part
        $lastNumber = (int) substr($lastBranch->branch_code, 2);

        $newNumber = $lastNumber + 1;

        return 'BR' . str_pad($newNumber, 5, '0', STR_PAD_LEFT);
    }
}