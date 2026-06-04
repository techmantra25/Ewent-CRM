<?php

namespace App\Livewire\Master;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Branch;
use App\Models\Admin;
use App\Models\City;
use App\Models\Designation;
use App\Models\RentalPrice;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class BranchCreate extends Component
{
    use WithFileUploads;

    public $branch_name, $branch_code, $address, $city_id;

    public $name, $designation, $mobile, $email, $image;

    public $cities = [];
    public $designations = [];
    public $city_branches = []; 

    public function mount()
    {
        $this->designations = Designation::orderBy('name','ASC')->get();
        $this->branch_code = $this->generateBranchCode();
        $this->cities = City::with('state')
            ->where('status', 1)
            ->orderBy('name', 'ASC')
            ->get();
    }

    public function updatedCityId($value)
    {
        $this->city_branches = Branch::where('city_id', $value)
        ->where('status', 1)
        ->whereHas('rentalPrices')
        ->orderBy('name')
        ->get();
    }

    public function copySubscription($sourceBranchId)
    {
        $this->validate([
            'branch_name' => 'required|string|max:255',
            'city_id'     => 'required|exists:cities,id',
            'address'     => 'required|string|max:500',
        ]);

        DB::beginTransaction();

        try {

            $newBranch = Branch::create([
                'name'        => ucwords($this->branch_name),
                'branch_code' => $this->generateBranchCode(),
                'address'     => $this->address,
                'city_id'     => $this->city_id,
                'status'      => 1,
            ]);

            $subscriptions = RentalPrice::where('branch_id', $sourceBranchId)->get();

            foreach ($subscriptions as $subscription) {

                RentalPrice::create([
                    'branch_id'         => $newBranch->id,
                    'product_id'        => $subscription->product_id,
                    'subscription_type' => $subscription->subscription_type,
                    'customer_type'     => $subscription->customer_type,
                    'duration'          => $subscription->duration,
                    'deposit_amount'    => $subscription->deposit_amount,
                    'rental_amount'     => $subscription->rental_amount,
                    'status'            => $subscription->status,
                ]);
            }

            DB::commit();

            session()->flash(
                'message',
                'Branch created and subscriptions copied successfully!'
            );

            return redirect()->route('admin.branch.list');

        } catch (\Exception $e) {

            DB::rollBack();

            session()->flash('error', $e->getMessage());
        }
    }

    public function save()
    {
        $this->validate([
            'branch_name' => 'required|string|max:255',
            'city_id'     => 'required|exists:cities,id',
            'address'     => 'required|string|max:500',
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

            DB::commit();

            return redirect()->route(
                'admin.model.subscriptions',
                [
                    'branch_id' => $branch->id,
                    'tab' => 2
                ]
            );

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