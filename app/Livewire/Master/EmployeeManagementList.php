<?php

namespace App\Livewire\Master;

use Livewire\Component;
use App\Models\Admin;
use App\Models\City;
use App\Models\Branch;
use Livewire\WithFileUploads;
use Illuminate\Pagination\Paginator;
use Livewire\WithPagination;

class EmployeeManagementList extends Component
{   
    use WithFileUploads, WithPagination;
    
    public $search = "";
    public $city_id;
    public $branch_id;
    
    // Dynamic collections for the dropdown elements
    public $cities = [];
    public $branches = [];

    public function boot()
    {
        Paginator::useBootstrap();
    }
    
    public function mount()
    {
        $this->cities = City::with('state')->where('status', 1)->orderBy('name', 'ASC')->get();
        $this->branch_id = current_branch();
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
        $this->resetPage();
    }

    // A reliable explicit wrapper to set branch filters
    public function setBranchId($value)
    {
        $this->branch_id = $value ?: null;
        $this->resetPage();
    }

    public function updatedBranchId()
    {
        $this->resetPage();
    }

    public function searchButtonClicked()
    {
        $this->resetPage();
    }
    
    public function resetSearch()
    {
        $this->reset(['search', 'city_id', 'branch_id']);
        $this->branch_id = current_branch();
        $this->branches = collect();
        $this->resetPage();
        
        $this->dispatch('reset-filters');
    }
    
    public function toggleStatus($id)
    {
        $admin = Admin::findOrFail($id);
        $admin->status = !$admin->status;
        $admin->save();
        session()->flash('message', 'Employee status updated successfully!');
    }
    
    public function setBranchFilter($value)
    {
        $this->branch_id = $value ?: null;
        $this->resetPage();
    }
    
    public function render()
    {
        $employees = Admin::with(['designationData','branchData'])
            // 1. If branch selection exists, strictly isolate search results to that branch
            ->when($this->branch_id, function ($query) {
                $query->where('branch_id', $this->branch_id);
            })
            // 2. Only fallback to city filtering if branch has NOT been chosen
            ->when(!$this->branch_id && $this->city_id, function ($query) {
                $query->whereHas('branchData', function($q) {
                    $q->where('city_id', $this->city_id);
                });
            })
            // 3. Search query filters matching string text conditions
            ->when($this->search, function ($query) {
                $searchTerm = '%' . $this->search . '%';
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('name', 'like', $searchTerm)
                    ->orWhere('mobile', 'like', $searchTerm)
                    ->orWhere('email', 'like', $searchTerm);
                });
            })
            ->orderBy('id', 'DESC')
            ->paginate(20);
            
        return view('livewire.master.employee-management-list',[
            'employees' => $employees,
        ]);
    }
}