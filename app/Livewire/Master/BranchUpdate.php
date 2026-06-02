<?php

namespace App\Livewire\Master;

use Livewire\Component;
use App\Models\Branch;
use App\Models\City;
use App\Models\State;

class BranchUpdate extends Component
{
    public $branch_id;
    public $branch_name, $branch_code, $address, $city_id, $status;
    public $state_id;
    public $states = [];
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

        // load states
        $this->states = State::where('status', 1)
            ->orderBy('name','ASC')
            ->get();

        // set state from selected city
        $city = City::find($this->city_id);
        $this->state_id = $city?->state_id;

        // load cities based on state
        $this->cities = City::where('state_id', $this->state_id)
            ->orderBy('name','ASC')
            ->get();
    }

    public function updatedStateId($value)
    {
        $this->city_id = null;

        $this->cities = City::where('state_id', $value)
            ->orderBy('name', 'ASC')
            ->get();
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