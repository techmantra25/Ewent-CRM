<?php

namespace App\Livewire\Master;

use Livewire\Component;
use App\Models\Branch;
use App\Models\City;

class BranchUpdate extends Component
{
    public $branch_id;
    public $branch_name, $branch_code, $address, $city_id, $status;

    public $cities = [];

    public function mount($id)
    {
        $branch = Branch::findOrFail($id);

        $this->branch_id   = $branch->id;
        $this->branch_name = $branch->name;
        $this->branch_code = $branch->branch_code;
        $this->address     = $branch->address;
        $this->city_id     = $branch->city_id;
        $this->status      = $branch->status;

        $this->cities = City::orderBy('name','ASC')->get();
    }

    public function update()
    {
        $this->validate([
            'branch_name' => 'required|string|max:255',
            'city_id'     => 'required|exists:cities,id',
        ]);

        $branch = Branch::findOrFail($this->branch_id);

        $branch->update([
            'name'    => ucwords($this->branch_name),
            'address' => $this->address,
            'city_id' => $this->city_id,
            'status'  => $this->status,
        ]);

        session()->flash('message', 'Branch updated successfully!');
        return redirect()->route('admin.branch.list');
    }

    public function render()
    {
        return view('livewire.master.branch-update');
    }
}