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
        // Eager load state definitions for advanced filtering queries
        $this->cities = City::with('state')->where('status', 1)->orderBy('name', 'ASC')->get();
        $this->branches = collect();
    }

    // Triggered when Javascript Chosen sets city_id values
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
        $this->branch_id = null; // Clear branch choice when parent city shifts
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
    
    public function render()
    {
        $employees = branchFilter(
                Admin::with(['designationData','branchData'])
            )
            // Filter by branch if selected; otherwise fallback filter by selected city
            ->when($this->branch_id, function ($query) {
                $query->where('branch_id', $this->branch_id);
            })
            ->when(!$this->branch_id && $this->city_id, function ($query) {
                $query->whereHas('branchData', function($q) {
                    $q->where('city_id', $this->city_id);
                });
            })
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