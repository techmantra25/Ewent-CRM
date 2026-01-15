<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Stock;
use App\Models\Product;
use App\Models\PaymentItem;
use App\Models\AsignedVehicle;
use App\Models\User;
use App\Models\ExchangeVehicle;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\VehicleSummaryExport;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;


class PaymentVehicleSummary extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $models = [];
    public $page = 1;
    protected string $pageName = 'page';

    public $model,$vehicle,$model_id,$vehicle_id,$vehicle_number;
    public $start_date = null;
    public $end_date = null;
    public function mount($model_id = null,$vehicle_id = null){
        
        if($model_id){
            $this->model =Product::find($model_id);
            if(!$this->model){
                $this->model_id = $model_id;
                abort(404);
            } 
        }
        if($vehicle_id){
            $this->vehicle =Stock::find($vehicle_id);
            if(!$this->vehicle){
                $this->vehicle_id = $vehicle_id;
                abort(404);
            } 
        }
        
        $this->start_date = date('Y-m-01'); // start of current month
        $this->end_date   = date('Y-m-d');  // today
        $this->models = Product::where('status', 1)->orderBy('title', 'ASC')->get();
    }

    public function gotoPage($value, $pageName =null){
        $pageName = $pageName ?: $this->pageName; // default to historyPage
        $this->setPage($value, $pageName);
        $this->page = $value;
    }
    public function FilterModel($value){
        $this->resetPageField();
        $this->resetPage();
        $this->page = 1;
        $this->model =Product::find($value);
        $this->model_id =$value;
    }

   public function keyVehicle($value)
    {
        $this->resetPage();
        $this->page = 1;
        $this->vehicle = Stock::where('vehicle_number', 'like', '%' . $value . '%')->first();
        if($this->vehicle){
            $this->vehicle_id = $this->vehicle->id;
        }
        
    }
    public function resetPageField(){
        $this->reset(['vehicle_id','model_id','model','vehicle', 'vehicle_number']);
    }
    public function updateStartDate($value){
        $this->resetPage();
        $this->page = 1;
        $this->start_date = $value;
    }
    public function updateEndDate($value){
        $this->resetPage();
        $this->page = 1;
        $this->end_date = $value;
    }
    public function exportAll()
    {
        return Excel::download(
            new VehicleSummaryExport(
                $this->vehicle_id,
                $this->model_id,
                $this->start_date,
                $this->end_date
            ),
            "vehicle_summary_between_{$this->start_date}_{$this->end_date}.xlsx"
        );
    }
    public function render()
    {
        // --- 1. Fetch assigned vehicle (only one, tied to vehicle_id if provided) ---
        $assignedVehicles = AsignedVehicle::whereIn('status', ['assigned', 'overdue'])
        ->with(['stock.product', 'order', 'user.organization_details'])
        ->when($this->vehicle_id, fn($query) => $query->where('vehicle_id', $this->vehicle_id))
        ->when($this->model_id, fn($query) => $query->whereHas('order', fn($q) => $q->where('product_id', $this->model_id)))
        ->whereBetween('start_date', [
            Carbon::parse($this->start_date)->startOfDay(), // 00:00:00
            Carbon::parse($this->end_date)->endOfDay(),     // 23:59:59
        ])
        ->get();
        // --- 2. Fetch exchange vehicles ---
        $exchangeVehicles = ExchangeVehicle::with(['stock'])
            ->when($this->vehicle_id, fn($query) => $query->where('vehicle_id', $this->vehicle_id))
            ->when($this->model_id, fn($query) => $query->whereHas('order', fn($q) => $q->where('product_id', $this->model_id)))
            ->whereIn('status', ['returned', 'renewal','exchanged'])
            ->where(function ($q){
                $q->whereIn('status',['returned','renewal'])
                ->orWhere(function ($q2){
                    $q2->where('status','exchanged')
                    ->whereRaw("TIMESTAMPDIFF(HOUR, start_date, end_date) > 24");
                });
            })
           ->whereBetween('start_date', [
                Carbon::parse($this->start_date)->startOfDay(), // 00:00:00
                Carbon::parse($this->end_date)->endOfDay(),     // 23:59:59
            ])
            ->orderBy('id', 'DESC')
            ->get(); // using get() because we regroup manually
            // dd($exchangeVehicles);
        // --- 3. Group exchangeVehicles by vehicle_id ---
        $grouped = $exchangeVehicles->groupBy('vehicle_id');

        $finalCollection = collect();
        foreach ($grouped as $vehicleId => $vehicles) {
            // If any assignedVehicle(s) exist for this vehicle_id, push them first
            $matchedAssigned = $assignedVehicles->where('vehicle_id', $vehicleId);
            foreach ($matchedAssigned as $aVehicle) {
                $aVehicle->exchanged_by = $aVehicle->assigned_by;
                $finalCollection->push($aVehicle);
            }

            // Push all exchangeVehicles for this vehicle_id
            foreach ($vehicles as $v) {
                $finalCollection->push($v);
            }
        }

        // Add any remaining assignedVehicles that didn't match any vehicle_id in grouped exchangeVehicles
        $remainingAssigned = $assignedVehicles->filter(fn($a) => !$finalCollection->contains(fn($item) => $item->vehicle_id == $a->vehicle_id));
        foreach ($remainingAssigned as $aVehicle) {
            $aVehicle->exchanged_by = $aVehicle->assigned_by;
            $finalCollection->prepend($aVehicle); // prepend to bring first
        }
        // dd($finalCollection);

        // --- 4. Manual pagination (Livewire-compatible) ---
        $perPage = 20;
        // dd($this->page);
        $page = $this->page ?? 1; // use Livewire's current page state

        $paginated = new \Illuminate\Pagination\LengthAwarePaginator(
            $finalCollection->forPage($page, $perPage)->values(),
            $finalCollection->count(),
            $perPage,
            $page,
            [
                'path'     => request()->url(),
                'pageName' => $this->pageName, // defaults to "page"
                'query'    => request()->query(),
            ]
        );

        // --- 5. Return view ---
        return view('livewire.admin.payment-vehicle-summary', [
            'history' => $paginated,
        ]);
    }


}
