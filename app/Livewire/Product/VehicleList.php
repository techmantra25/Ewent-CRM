<?php

namespace App\Livewire\Product;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Stock;
use App\Models\City;
use App\Models\Branch;
use App\Models\Product;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\OverdueVehicleExport;

class VehicleList extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';
    public $model,$branch,$overdue_days;
    public $search = '';
    public $active_tab = 1;
    public $models = [];
    public $city_id;
    public $cities = [];
    public $branches = [];
    public $branch_list = [];
    public $isModalOpen = false; // Track modal visibility
    public $customer_type;

    /**
     * Search button click handler to reset pagination.
     */
    public function mount(){
        $this->branches = get_branches() ?? [];

        if (count($this->branches) === 1) {
            $this->branch = $this->branches[0];
        }
        $this->models = Product::where('status', 1)->orderBy('title', 'ASC')->get();
        $this->cities = City::with('state')
            ->where('status', 1)
            ->orderBy('name')
            ->get();
        $this->branch_list = [];
    }
    public function btn_search()
    {
        $this->resetPage();
    }

    public function FilterCity($value)
    {
        $this->city_id = $value;

        $this->branch = null;

        $this->branch_list = Branch::where('city_id', $value)
            ->where('status', 1)
            ->get();

        $this->resetPage();
    }

    public function FilterCustomerType($value)
    {
        $this->customer_type = $value;
        $this->resetPage();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
    }
    public function FilterModel($value){
        $this->model =$value;
    }
    public function FilterOverdue($value){
        $this->overdue_days =$value;
    }
    public function FilterBranch($value){
        $this->branch =$value;
    }

    /**
     * Refresh button click handler to reset the search input and reload data.
     */
    public function reset_search(){
        $this->reset(['search','model','overdue_days', 'city_id', 'branch', 'customer_type']);
        $this->branch_list = []; 
         $this->dispatch('reset-chosen-filters');
    }

    public function tab_change($value){
        $this->active_tab = $value;
        $this->reset_search();
        $this->resetPage('all_vehicles');
        $this->resetPage('assigned_vehicles');
        $this->resetPage('unassigned_vehicles');
        $this->resetPage('overdue_vehicles');
    }

    public function exportOverdue()
    {
        return Excel::download(
            new OverdueVehicleExport($this->branch,$this->model,$this->search,$this->overdue_days),
            'overdue_vehicles.xlsx'
        );
    }

    public function render()
    {
        // Fetch all vehicles (with or without assigned vehicles)
        $all_vehicles = Stock::with([
                'product',
                'assignedVehicle.user',
                'overdueVehicle.user'
            ])
        ->when($this->branch, function ($query) {
            // If specific branch selected
            $query->where('branch_id', $this->branch);
        }, function ($query) {
            // If no branch selected, filter by allowed branches
            $query->whereIn('branch_id', $this->branches);
        })
        ->when($this->model, function ($query) {
            $query->where('product_id', $this->model); // Assuming `model_id` is the column for filtering
        })
        ->when($this->customer_type, function ($query) {

            $query->where(function ($q) {

                $q->whereHas('assignedVehicle.user', function ($uq) {
                    $uq->where('user_type', $this->customer_type);
                })

                ->orWhereHas('overdueVehicle.user', function ($uq) {
                    $uq->where('user_type', $this->customer_type);
                });

            });

        })
        ->when($this->search, function ($query) {

            $searchTerm = '%' . $this->search . '%';

            $query->where(function ($q) use ($searchTerm) {

                $q->where('vehicle_number', 'like', $searchTerm)
                ->orWhere('imei_number', 'like', $searchTerm)
                ->orWhere('chassis_number', 'like', $searchTerm)
                ->orWhere('friendly_name', 'like', $searchTerm)

                ->orWhereHas('assignedVehicle.user', function ($uq) use ($searchTerm) {
                    $uq->where('name', 'like', $searchTerm)
                    ->orWhere('mobile', 'like', $searchTerm)
                    ->orWhere('email', 'like', $searchTerm);
                })

                ->orWhereHas('overdueVehicle.user', function ($uq) use ($searchTerm) {
                    $uq->where('name', 'like', $searchTerm)
                    ->orWhere('mobile', 'like', $searchTerm)
                    ->orWhere('email', 'like', $searchTerm);
                });

            });

        })
        ->orderBy('id', 'DESC')
        ->orderBy('product_id', 'DESC')
        ->paginate(20,['*'],'all_vehicles');

        // Fetch only assigned vehicles (having an entry in the assigned_vehicles table)
        $assigned_vehicles = Stock::with([
                'assignedVehicle.user'
            ])
        ->when($this->branch, function ($query) {
            $query->where('branch_id', $this->branch);
        }, function ($query) {
            $query->whereIn('branch_id', $this->branches);
        })
        // ->whereIn('branch_id',get_branches())
        ->whereHas('assignedVehicle') // Ensures only assigned vehicles are fetched
        ->when($this->model, function ($query) {
            $query->where('product_id', $this->model); // Assuming `model_id` is the column for filtering
        })
        ->when($this->customer_type, function ($query) {
            $query->whereHas('assignedVehicle.user', function ($uq) {
                $uq->where('user_type', $this->customer_type);
            });

        })
        ->when($this->search, function ($query) {

            $searchTerm = '%' . $this->search . '%';

            $query->where(function ($q) use ($searchTerm) {

                $q->where('vehicle_number', 'like', $searchTerm)
                ->orWhere('imei_number', 'like', $searchTerm)
                ->orWhere('chassis_number', 'like', $searchTerm)
                ->orWhere('friendly_name', 'like', $searchTerm)

                ->orWhereHas('assignedVehicle.user', function ($uq) use ($searchTerm) {
                    $uq->where('name', 'like', $searchTerm)
                    ->orWhere('mobile', 'like', $searchTerm)
                    ->orWhere('email', 'like', $searchTerm);
                });

            });

        })
        ->orderBy('id', 'DESC')
        ->orderBy('product_id', 'DESC')
        ->paginate(20, ['*'], 'assigned_vehicles');


        $unassigned_vehicles = Stock::
        when($this->branch, function ($query) {
            // If specific branch selected
            $query->where('branch_id', $this->branch);
        }, function ($query) {
            // If no branch selected, filter by allowed branches
            $query->whereIn('branch_id', $this->branches);
        })
        ->whereDoesntHave('assignedVehicle', function ($query) {
            $query->whereIn('status', ['assigned','sold']); // Ensure it's truly unassigned
        })->whereDoesntHave('overdueVehicle', function ($query) {
            $query->whereIn('status', ['overdue']); // Ensure it's truly unassigned
        })
        ->when($this->model, function ($query) {
            $query->where('product_id', $this->model); // Assuming `model_id` is the column for filtering
        })
        ->when($this->customer_type, function ($query) {
            $query->whereRaw('1 = 0');
        })
        ->when($this->search, function ($query) {
            $searchTerm = '%' . $this->search . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('vehicle_number', 'like', $searchTerm)
                    ->orWhere('imei_number', 'like', $searchTerm)
                    ->orWhere('chassis_number', 'like', $searchTerm)
                    ->orWhere('friendly_name', 'like', $searchTerm);
            });
        })
        ->orderBy('id', 'DESC')
        ->paginate(20,['*'], 'unassigned_vehicles');

        $today = Carbon::today();

$overdue_vehicles = Stock::with([
        'overdueVehicle.user'
    ])
    ->when($this->branch, function ($query) {
        $query->where('branch_id', $this->branch);
    }, function ($query) {
        $query->whereIn('branch_id', $this->branches);
    })
    ->whereHas('overdueVehicle')

    ->when($this->model, function ($query) {
        $query->where('product_id', $this->model);
    })
    ->when($this->customer_type, function ($query) {

        $query->whereHas('overdueVehicle.user', function ($uq) {
            $uq->where('user_type', $this->customer_type);
        });

    })

    // OVERDUE DROPDOWN FILTER (ADDED)
    ->when($this->overdue_days !== null && $this->overdue_days !== '', function ($query) use ($today) {

        if ($this->overdue_days === '20+') {

            // More than 20 days
            $query->whereHas('overdueVehicle', function ($q) use ($today) {
                $q->whereRaw(
                    'DATEDIFF(?, end_date) > 20',
                    [$today]
                );
            });

        } else {

            $days = (int) $this->overdue_days;

            $query->whereHas('overdueVehicle', function ($q) use ($today, $days) {
                $q->whereRaw(
                    'ABS(DATEDIFF(?, end_date)) = ?',
                    [$today, $days]
                );
            });
        }
    })

    // EXISTING SEARCH (NO CHANGE, just small condition add)
    ->when($this->search, function ($query) use ($today) {

        if (is_numeric($this->search) && empty($this->overdue_days)) {

            $days = (int) $this->search;

            $query->whereHas('overdueVehicle', function ($q) use ($today, $days) {
                $q->whereRaw(
                    'ABS(DATEDIFF(?, end_date)) = ?',
                    [$today, $days]
                );
            });

        } else {

            $searchTerm = '%' . $this->search . '%';

            $query->where(function ($q) use ($searchTerm) {

                $q->where('vehicle_number', 'like', $searchTerm)
                  ->orWhere('imei_number', 'like', $searchTerm)
                  ->orWhere('chassis_number', 'like', $searchTerm)
                  ->orWhere('friendly_name', 'like', $searchTerm)

                  ->orWhereHas('overdueVehicle.user', function ($uq) use ($searchTerm) {
                      $uq->where('name', 'like', $searchTerm)
                         ->orWhere('mobile', 'like', $searchTerm)
                         ->orWhere('email', 'like', $searchTerm);
                  });

            });
        }
    })

    ->orderBy('id', 'DESC')
    ->orderBy('product_id', 'DESC')
    ->paginate(20, ['*'], 'overdue_vehicles');

        return view('livewire.product.vehicle-list', [
            'all_vehicles' => $all_vehicles,
            'unassigned_vehicles' => $unassigned_vehicles,
            'assigned_vehicles' => $assigned_vehicles,
            'overdue_vehicles' => $overdue_vehicles,
        ]);
    }

}
