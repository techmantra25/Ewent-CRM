<?php

namespace App\Livewire\Master;

use Livewire\Component;
use App\Models\Admin;
use App\Models\Branch;
use Illuminate\Pagination\Paginator;
use Livewire\WithPagination;
use App\Models\State;
use App\Models\City;

class BranchIndex extends Component
{   
    use WithPagination;
    public $state_id = '';
    public $city_id = '';

    public $states = [];
    public $cities = [];
    public $search = "";

    public function boot()
    {
        Paginator::useBootstrap();
    }

    public function mount()
    {
        $this->states = State::where('status', 1)
            ->orderBy('name', 'ASC')
            ->get();

        $this->cities = [];
    }
    
    public function searchButtonClicked()
    {
        $this->resetPage();
    }

    public function resetSearch()
    {
        $this->reset(['search', 'state_id', 'city_id']);
        $this->cities = [];
        $this->resetPage();
    }

    public function changeState($stateId)
    {
        $this->state_id = $stateId;
        $this->city_id = '';

        $this->cities = City::where('state_id', $stateId)
            ->orderBy('name', 'ASC')
            ->get();

        $this->resetPage();

        $this->dispatch('refreshChosen');
    }
     public function toggleStatus($id)
    {
        $branch = Branch::findOrFail($id);
        $branch->status = !$branch->status;
        $branch->save();
        session()->flash('message', 'Branch status updated successfully!');
    }
    public function render()
    {
        $branch = Branch::with('city.state')
            ->withCount('employees')
            ->when($this->search, function ($query) {
                $searchTerm = '%' . $this->search . '%';

                $query->where(function ($q) use ($searchTerm) {
                    $q->where('name', 'like', $searchTerm)
                    ->orWhere('branch_code', 'like', $searchTerm)
                    ->orWhereHas('city', function ($q2) use ($searchTerm) {
                        $q2->where('name', 'like', $searchTerm);
                    });
                });
            })
            // CITY FILTER
            ->when($this->city_id, function ($q) {
                $q->where('city_id', $this->city_id);
            })

            ->orderBy('id', 'DESC')
            ->paginate(20);
        return view('livewire.master.branch-index',[
            'branches'=>$branch,
        ]);
    }
}
