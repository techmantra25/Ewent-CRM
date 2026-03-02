<?php

namespace App\Livewire\Product;

use Livewire\Component;
use App\Models\Product;
use App\Models\Stock;
use App\Models\Branch;
use App\Models\BranchLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class VehicleCreate extends Component
{
    public $existing_stock = [];
    public $models = [];
    public $vehicles = [];
    public $branchs = [];
    public $vehicle_mapping = [];
    public $branch,$model,$vehicle_number,$vehicle_track_id,$imei_number,$chassis_number,$friendly_name;
    public function mount(){

        $vehiclesUrl = 'https://app.loconav.sensorise.net/integration/api/v1/vehicles?perPage=1000';

        $ch = curl_init($vehiclesUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "User-Authentication: " . env('LOCONAV_TOKEN'),
            "Accept: application/json"
        ]);

        $vehiclesResponse = curl_exec($ch);
        curl_close($ch);

        $vehiclesData = json_decode($vehiclesResponse, true);
        if($vehiclesData){
            if($vehiclesData['success']==true){
                $this->vehicles = $vehiclesData['data']['vehicles'];
                foreach ($this->vehicles as $vehicle) {
                    $this->vehicle_mapping[$vehicle['number']] = $vehicle['vehicleUuid'];
                }
            }elseif($vehiclesData['success']==false){
                session()->flash('error', $vehiclesData['data']['errors'][0]['message']);
            }
        }
        
        $this->models = Product::where('status', 1)->orderBy('title', 'ASC')->get();
        $this->existing_stock = Stock::orderBy('vehicle_number', 'ASC')->get()->pluck('vehicle_number')->toArray();
        $this->branchs = Branch::whereIn('id', get_branches())
                        ->where('status', 1)
                        ->get();
       
    }
    public function selectVehicle($number)
    {
        $this->vehicle_number = $number;
        $vehicle_track_id = $this->vehicle_mapping[$number] ?? null;
        if ($vehicle_track_id) {
            $this->vehicle_track_id = $vehicle_track_id;
        }

        $vehiclesUrl = 'https://app.loconav.sensorise.net/integration/api/v1/vehicles/'.$vehicle_track_id;

        $ch = curl_init($vehiclesUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "User-Authentication: " . env('LOCONAV_TOKEN'),
            "Accept: application/json"
        ]);

        $vehiclesResponse = curl_exec($ch);
        curl_close($ch);

        $vehiclesData = json_decode($vehiclesResponse, true);

        if($vehiclesData['success']==true){
            $this->chassis_number = $vehiclesData['data']['chassisNumber'];
        }elseif($vehiclesData['success']==false){
            session()->flash('error', $vehiclesData['data']['errors'][0]['message']);
        }
    }

    protected $rules = [
        'branch' => 'required|exists:branches,id',
        'model' => 'required|exists:products,id',
        'vehicle_track_id' => 'required|string|unique:stocks,vehicle_track_id',
        'friendly_name' => 'nullable|string|max:255',
        'vehicle_number' => 'required|string|unique:stocks,vehicle_number',
        'imei_number' => 'nullable|string|unique:stocks,imei_number',
        'chassis_number' => 'required|string|unique:stocks,chassis_number',
    ];
    public function saveVehicle()
    {
        $validatedData = $this->validate();
        Stock::create([
            'branch_id' => $validatedData['branch'],
            'product_id' => $validatedData['model'],
            'vehicle_number' => $validatedData['vehicle_number'],
            'vehicle_track_id' => $validatedData['vehicle_track_id'],
            'imei_number' => $validatedData['imei_number'],
            'chassis_number' => $validatedData['chassis_number'],
            'friendly_name' => $validatedData['friendly_name'],
        ]);

        // Create Branch Log
        BranchLog::create([
            'branch_id'    => $validatedData['branch'],
            'admin_id'     => Auth::guard('admin')->user()->id,
            'action'       => 'create',
            'module'       => 'Stock',
            'reference_id' => $stock->id,
            'new_data'     => json_encode($stock->toArray()),
            'ip_address'   => request()->ip(),
            'user_agent'   => request()->userAgent(),
        ]);
        session()->flash('message', 'Vehicle created successfully!');
        return redirect()->route('admin.vehicle.list');
    }
    public function render()
    {
        return view('livewire.product.vehicle-create');
    }
}
