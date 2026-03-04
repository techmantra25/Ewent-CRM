<?php

namespace App\Livewire\Product;

use Livewire\Component;
use App\Models\Product;
use App\Models\Stock;
use App\Models\Branch;
use App\Models\BranchLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class VehicleUpdate extends Component
{
    public $models = [];
    public $branchs = [];
    public $id, $branch, $model,$vehicle_number,$vehicle_track_id,$imei_number,$chassis_number,$friendly_name;
    public function mount($id){
        $this->id = $id;
        $stock = Stock::find($id);
        if (!$stock) {
            abort(404); //This will throw a 404 error page
        }
        if (!in_array($stock->branch_id, get_branches() ?? [])) {
            abort(403);
        }
         // Pre-fill form fields for update
         $this->model = $stock->product_id;
         $this->branch = $stock->branch_id;
         $this->vehicle_number = $stock->vehicle_number;
         $this->vehicle_track_id = $stock->vehicle_track_id;
         $this->imei_number = $stock->imei_number;
         $this->chassis_number = $stock->chassis_number;
         $this->friendly_name = $stock->friendly_name;
        $this->models = Product::where('status', 1)->orderBy('title', 'ASC')->get();

        $this->branchs = Branch::whereIn('id', get_branches())
                        ->where('status', 1)
                        ->get();
    }

    public function rules()
    {
        return [
            'branch' => ['required', 'exists:branches,id'],
            'model' => ['required', 'exists:products,id'],
            'vehicle_track_id' => ['required', 'string', Rule::unique('stocks', 'vehicle_track_id')->ignore($this->id)],
            'friendly_name' => ['nullable', 'string', 'max:255'],
            'vehicle_number' => ['required', 'string', Rule::unique('stocks', 'vehicle_number')->ignore($this->id)],
            // 'imei_number' => ['required', 'string', Rule::unique('stocks', 'imei_number')->ignore($this->id)],
            'chassis_number' => ['required', 'string', Rule::unique('stocks', 'chassis_number')->ignore($this->id)],
        ];
    }
    public function updateVehicle()
    {
        $validatedData = $this->validate();

         $stock = Stock::findOrFail($this->id);

        // Take old data BEFORE update
        $oldData = $stock->toArray();

        // Update stock
        $stock->update([
            'branch_id'        => $validatedData['branch'],
            'product_id'       => $validatedData['model'],
            'vehicle_number'   => $validatedData['vehicle_number'],
            'vehicle_track_id' => $validatedData['vehicle_track_id'],
            'imei_number'      => $this->imei_number,
            'chassis_number'   => $validatedData['chassis_number'],
            'friendly_name'    => $validatedData['friendly_name'],
        ]);

        // Refresh model to get updated data
        $stock->refresh();

        // Create Branch Log
        BranchLog::create([
            'branch_id'    => $validatedData['branch'],
            'admin_id'     => auth('admin')->id(),
            'action'       => 'update',
            'module'       => 'Stock',
            'reference_id' => $stock->id,
            'old_data'     => json_encode($oldData),
            'new_data'     => json_encode($stock->toArray()),
            'ip_address'   => request()->ip(),
            'user_agent'   => request()->userAgent(),
        ]);

        session()->flash('message', 'Vehicle updated successfully!');
        return redirect()->route('admin.vehicle.list');
    }
    public function render()
    {
        return view('livewire.product.vehicle-update');
    }
}
