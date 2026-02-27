<?php

namespace App\Livewire\Master;

use Livewire\Component;
use App\Models\Admin;
use App\Models\Branch;
use Illuminate\Pagination\Paginator;
use Livewire\WithPagination;

class BranchIndex extends Component
{   
    use WithPagination;
    public $search = "";
     public function boot()
    {
        Paginator::useBootstrap();
    }
    public function searchButtonClicked()
    {
        $this->resetPage();
    }
     public function resetSearch()
    {
        $this->reset('search');  
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
        $branch = Branch::with('city')
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
        ->orderBy('id', 'DESC')
        ->paginate(20);
        return view('livewire.master.branch-index',[
            'branches'=>$branch,
        ]);
    }
}
