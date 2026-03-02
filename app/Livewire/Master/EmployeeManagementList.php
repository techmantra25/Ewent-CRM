<?php

namespace App\Livewire\Master;

use Livewire\Component;
use App\Models\Admin;
use Livewire\WithFileUploads;
use Illuminate\Pagination\Paginator;
use Livewire\WithPagination; // Import WithPagination trait

class EmployeeManagementList extends Component
{   

    use WithFileUploads, WithPagination; // Include WithPagination trait
    public $search = "";
    public $branch_id;

     public function boot()
    {
        Paginator::useBootstrap();
    }
    public function searchButtonClicked()
    {
        $this->resetPage(); // Reset to the first page
    }
     public function resetSearch()
    {
        $this->reset('search');     // Reset pagination
    }
    public function mount()
    {
        $this->branch_id = request()->branch_id;
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
        $employees = Admin::with(['designationData','branchData'])
            ->when($this->branch_id, function ($query) {
                $query->where('branch_id', $this->branch_id);
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
            ->where('id', '!=', 1)
            ->paginate(20);
        return view('livewire.master.employee-management-list',[
            'employees'=>$employees,
        ]);
    }
}
