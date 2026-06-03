<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\State;
use Illuminate\Validation\Rule;

class StateIndex extends Component
{
    public $states = [];
    public $stateId;
    public $name;
    public $country = 'India';
    public $status = 1;
    public $search = '';

    public function mount()
    {
        $this->refresh();
    }

    public function refresh()
    {
        $this->resetForm();

        $this->states = State::where('name', 'like', "%{$this->search}%")
            ->orderBy('name')
            ->get();
    }

    public function searchButtonClicked()
    {
        $this->states = State::where('name', 'like', '%' . $this->search . '%')
            ->orderBy('name')
            ->get();
    }

    public function updatedSearch()
    {
        $this->states = State::where('name', 'like', "%{$this->search}%")
            ->orderBy('name')
            ->get();
    }

    public function resetForm()
    {
        $this->reset([
            'stateId',
            'name',
            'status'
        ]);

        $this->country = 'India';
        $this->status = 1;
    }

    public function save()
    {
        $this->validate([
            'name' => [
                'required',
                Rule::unique('states', 'name')->ignore($this->stateId)
            ],
            'country' => 'required',
        ]);

        State::updateOrCreate(
            ['id' => $this->stateId],
            [
                'name' => $this->name,
                'country' => $this->country,
                'status' => $this->status,
            ]
        );

        session()->flash(
            'message',
            $this->stateId
                ? 'State updated successfully.'
                : 'State created successfully.'
        );

        $this->refresh();
    }

    public function edit($id)
    {
        $state = State::findOrFail($id);

        $this->stateId = $state->id;
        $this->name = $state->name;
        $this->country = $state->country;
        $this->status = $state->status;
    }

    public function destroy($id)
    {
        State::findOrFail($id)->delete();

        session()->flash('message', 'State deleted successfully.');

        $this->refresh();
    }

    public function toggleStatus($id)
    {
        $state = State::findOrFail($id);

        $state->update([
            'status' => !$state->status
        ]);

        session()->flash('message', 'Status updated.');

        $this->refresh();
    }

    public function render()
    {
        return view('livewire.admin.state-index');
    }
}
