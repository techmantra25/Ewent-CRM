<?php

namespace App\Livewire\Product;

use Livewire\Component;
use App\Models\RentalPrice;
use App\Models\Product;
use App\Models\Branch;
use App\Models\City; 
use App\Models\State; 
use Illuminate\Validation\Rule;
use Livewire\WithPagination;

class MasterSubscription extends Component
{
    use WithPagination;

    public $subscriptionId = null;
    public $asset = null;
    public $customerType = 'B2C';
    public $model, $models, $subscription_type, $customer_type, $duration, $deposit_amount, $rental_amount;
    public $active_tab = 1;
    public $city_id = null;
    public $branch_id = null;

    public $cities = [];
    public $branches = [];

    public $filter_city_id = null;
    public $filter_branch_id = null;

    public $filterCities = [];
    public $filterBranches = [];
    
    protected function rules()
    {
        return [
            'model' => 'required|exists:products,id',
            'branch_id' => 'required|exists:branches,id',
            'subscription_type' => [
                'required',
                'string',
                'max:255',
                Rule::unique('rental_prices')->where(function ($query) {
                    return $query->where('product_id', $this->model)
                                 ->where('customer_type', $this->customer_type)
                                 ->where('branch_id', $this->branch_id);
                }),
            ],
            'customer_type' => 'required|in:B2B,B2C',
            'duration' => 'required|integer|min:1',
            'deposit_amount' => 'required|numeric|min:0',
            'rental_amount' => 'required|numeric|min:0',
        ];
    }

    protected $messages = [
        'model.required' => 'The model field is mandatory.',
        'model.exists' => 'The selected model is invalid.',
        'branch_id.required' => 'The branch field is mandatory.',
        'subscription_type.required' => 'The subscription type is required.',
        'customer_type.required' => 'The customer type is required.',
        'duration.required' => 'The duration is required.',
        'deposit_amount.required' => 'The deposit amount is required.',
        'rental_amount.required' => 'The rental amount is required.',
    ];

    public function mount()
    {
        $this->models = Product::where('status', 1)
            ->orderBy('title')
            ->get();

        $this->cities = City::with('state')
            ->where('status', 1)
            ->orderBy('name')
            ->get();

        $this->filterCities = $this->cities;

        $this->branches = collect();
        $this->filterBranches = collect();
    }

    public function updatedFilterCityId($value)
    {
        $this->filter_branch_id = null;

        if ($value) {
            $this->filterBranches = Branch::where('city_id', $value)
                ->where('status', 1)
                ->orderBy('name')
                ->get();
        } else {
            $this->filterBranches = collect();
        }

        // Dispatches specific instruction payload to force sync the chosen DOM elements
        $this->dispatch('subscription-filter-updated');
    }

    public function updatedCityId($value)
    {
        $this->branch_id = null; 

        if (!empty($value)) {
            $this->branches = Branch::where('city_id', $value)
                ->where('status', 1)
                ->orderBy('name', 'ASC')
                ->get();
        } else {
            $this->branches = collect();
        }
        
        $this->dispatch('subscription-edit-loaded');
    }

    public function filter($value){
        $this->asset = $value;
    }

    public function filterType($value){
        $this->customerType = $value;
    }

    public function GetDuration($duration)
    {
        $this->duration = $duration;
    }

    public function GetCustomerType($customer_type)
    {
        $this->customer_type = $customer_type;
        if($customer_type == 'B2B'){
            $this->deposit_amount = 0;
        }else{
            $this->deposit_amount = null;
        }
    }

    public function store()
    {
        $this->validate();

        RentalPrice::create([
            'branch_id' => $this->branch_id,
            'product_id' => $this->model,
            'subscription_type' => $this->subscription_type,
            'customer_type' => $this->customer_type,
            'duration' => $this->duration,
            'deposit_amount' => $this->deposit_amount,
            'rental_amount' => $this->rental_amount,
        ]);

        session()->flash('message', 'Subscription created successfully!');
        $this->refresh();
        $this->active_tab = 1;
    }

    public function edit($id)
    {
        $subscription = RentalPrice::with('branch.city.state')->findOrFail($id);

        $this->subscriptionId = $subscription->id;
        $this->model = $subscription->product_id;
        $this->subscription_type = $subscription->subscription_type;
        $this->customer_type = $subscription->customer_type;
        $this->duration = $subscription->duration;
        $this->deposit_amount = $subscription->deposit_amount;
        $this->rental_amount = $subscription->rental_amount;
        $this->branch_id = $subscription->branch_id;

        if ($subscription->branch && $subscription->branch->city_id) {
            $cityObj = $subscription->branch->city;
            
            if ($cityObj) {
                $this->city_id = $cityObj->id;

                $this->branches = Branch::where('city_id', $this->city_id)
                    ->where('status', 1)
                    ->orderBy('name', 'ASC')
                    ->get();
            }
        }
        $this->dispatch('subscription-edit-loaded');

        $this->active_tab = 3;
    }

    public function update()
    {
        $this->validate([
            'model' => 'required|exists:products,id',
            'branch_id' => 'required|exists:branches,id',
            'subscription_type' => [
                'required',
                'string',
                'max:255',
                Rule::unique('rental_prices')->where(function ($query) {
                    return $query->where('product_id', $this->model)
                                 ->where('customer_type', $this->customer_type)
                                 ->where('branch_id', $this->branch_id);
                })->ignore($this->subscriptionId),
            ],
            'duration' => 'required|integer|min:1',
            'customer_type' => 'required|in:B2B,B2C',
            'deposit_amount' => 'required|numeric|min:0',
            'rental_amount' => 'required|numeric|min:0',
        ], [
            'subscription_type.unique' => 'This subscription type already exists for this combination.',
        ]);
    
        $subscription = RentalPrice::findOrFail($this->subscriptionId);
        $subscription->update([
            'branch_id' => $this->branch_id,
            'product_id' => $this->model,
            'subscription_type' => $this->subscription_type,
            'duration' => $this->duration,
            'deposit_amount' => $this->deposit_amount,
            'rental_amount' => $this->rental_amount,
        ]);
    
        session()->flash('message', 'Subscription updated successfully!');
        $this->refresh();
        $this->active_tab = 1;
    }

    public function toggleStatus($id)
    {
        $subscription = RentalPrice::findOrFail($id);
        $subscription->status = !$subscription->status;
        $subscription->save();

        session()->flash('message', 'Subscription status updated successfully!');
    }

    public function destroy($id)
    {
       $subscription = RentalPrice::findOrFail($id);
       $subscription->delete();
       session()->flash('message', 'Subscription deleted successfully!');
    }

    public function refresh()
    {
        $this->reset([
            'model',
            'subscription_type',
            'customer_type',
            'duration',
            'deposit_amount',
            'rental_amount',
            'subscriptionId',
            'city_id',
            'branch_id'
        ]);

        $this->branches = collect();

        $this->cities = City::with('state')
            ->where('status', 1)
            ->orderBy('name')
            ->get();
    }
    public function ActiveCreateTab($value)
    {
        if($value == 1){
            $this->refresh();
        }
        $this->active_tab = $value;
    }

    public function render()
    {
        $query = RentalPrice::with(['product', 'branch.city.state']);

            if ($this->asset) {
                $query->where('product_id', $this->asset);
            }

            if ($this->customerType) {
                $query->where('customer_type', $this->customerType);
            }

            if ($this->filter_city_id) {
                $query->whereHas('branch', function ($q) {
                    $q->where('city_id', $this->filter_city_id);
                });
            }

            if ($this->filter_branch_id) {
                $query->where('branch_id', $this->filter_branch_id);
            }
    
        $subscriptions = $query->get();
        return view('livewire.product.master-subscription', ['subscriptions' => $subscriptions]);
    }
}