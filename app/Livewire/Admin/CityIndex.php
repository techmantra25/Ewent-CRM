<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\State;
use App\Models\City;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class CityIndex extends Component
{
    use WithFileUploads;

    public $csvFile;
    public $modal_activity_class = 0;
    public $cities, $states;
    public $cityId, $name, $state_id, $status, $search;
    public $filter_state_id = '';

    // Remove the validation rules from the property and move it to the method
    protected $rules = [
        'name' => 'required|string|max:255',
        'state_id' => 'required',
    ];

    // Mount function to initialize data
    public function mount()
    {
        $this->states = State::where('status', 1)->orderBy('name', 'ASC')->get();
        $this->refresh();
    }

    // Fetch city with search
    public function refresh()
    {
        $this->resetForm();

        $this->loadCities();
    }

    public function loadCities()
    {
        $this->cities = City::with('state')
            ->when($this->search, function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%');
            })
            ->when($this->filter_state_id, function ($q) {
                $q->where('state_id', $this->filter_state_id);
            })
            ->orderBy('name', 'ASC')
            ->get();
    }
    // Reset form inputs
    public function resetForm()
    {
        $this->reset(['cityId', 'name', 'state_id', 'status']);
    }

    // Create or Update City
    public function save()
    {
        $this->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('cities')
                    ->where(function ($query) {
                        return $query->where('state_id', $this->state_id);
                    })
                    ->ignore($this->cityId),
            ],
            'state_id' => 'required',
        ]);

        // Create or update logic
        if ($this->cityId) {
            $city = City::findOrFail($this->cityId);
            $city->name = $this->name;
            $city->state_id = $this->state_id;
            
            $city->save();
            session()->flash('message', 'City updated successfully!');
        } else {
            $city = new City([
                'name' => $this->name,
                'state_id'=>$this->state_id,
                'country' => 'India',
                'status' => true,
            ]);
            
            $city->save();
            session()->flash('message', 'City created successfully!');
        }

        $this->resetForm();
        $this->refresh();
    }

    // Edit City
    public function edit($id)
    {
        $city = City::findOrFail($id);

        $this->cityId = $city->id;
        $this->name = $city->name;
        $this->state_id = $city->state_id;
        $this->status = $city->status;
    }

    // Delete city
    public function destroy($id)
    {
        City::findOrFail($id)->delete();

        session()->flash('message', 'City deleted successfully!');
        $this->refresh();
    }

    // Toggle city status
    public function toggleStatus($id)
    {
        $city = City::findOrFail($id);
        $city->status = !$city->status;
        $city->save();

        session()->flash('message', 'City status updated successfully!');
        $this->refresh();
    }

    public function changeState($stateId)
    {
        $this->filter_state_id = $stateId;

        $this->loadCities();
    }

    public function searchButtonClicked()
    {
        $this->loadCities();
    }

    public function resetSearch()
    {
        $this->reset(['search', 'filter_state_id']);

        $this->loadCities();

        $this->dispatch('refreshChosen');
    }

    public function ModalImport($value)
    {
        $this->modal_activity_class = $value;
    }

    public function uploadFile()
    {
        $this->validate([
            'csvFile' => 'required|mimes:csv,txt|max:5120',
        ]);

        DB::beginTransaction();

        try {

            $file = $this->csvFile;

            $fileName = time().'.'.$file->getClientOriginalExtension();

            $filePath = $file->storeAs(
                'public/csv/city',
                $fileName
            );

            $csvData = array_map(
                'str_getcsv',
                file(storage_path('app/'.$filePath))
            );

            // Remove header
            $rows = array_slice($csvData, 1);

            foreach ($rows as $row) {

                if (empty($row[0]) || empty($row[1])) {
                    continue;
                }

                $cityName = trim($row[0]);
                $stateName = trim($row[1]);

                $state = State::where(
                    'name',
                    $stateName
                )->first();

                if (!$state) {

                    DB::rollBack();

                    session()->flash(
                        'csv_error',
                        "State '{$stateName}' not found."
                    );

                    return;
                }

                City::updateOrCreate(
                [
                    'name'     => $cityName,
                    'state_id' => $state->id,
                ],
                [
                    'country' => 'India',
                    'status'  => 1,
                ]
                );
            }

            DB::commit();

            session()->flash(
                'message',
                'Cities imported successfully.'
            );

        } catch (\Exception $e) {

            DB::rollBack();

            session()->flash(
                'csv_error',
                $e->getMessage()
            );
        }

        if (isset($filePath)) {

            $deletePath = public_path(
                'storage/csv/city/'.$fileName
            );

            if (file_exists($deletePath)) {
                unlink($deletePath);
            }
        }

        $this->reset('csvFile');

        $this->modal_activity_class = 0;

        $this->refresh();
    }

    public function render()
    {
        return view('livewire.admin.city-index');
    }
}
