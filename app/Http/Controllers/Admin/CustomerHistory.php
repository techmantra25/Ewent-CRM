<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Season;
use Illuminate\Support\Facades\Session;

class SeasonCrud extends Component
{
    public $seasons, $name, $start_date, $end_date, $season_id;
    public $editMode = false;

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ];
    }

    public function mount()
    {
        $this->loadSeasons();
    }

    public function loadSeasons()
    {
        $this->seasons = Season::orderByDesc('start_date')->get();
    }

    public function store()
    {
        $this->validate();

        Season::create([
            'name' => $this->name,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
        ]);

        session()->flash('success', 'Season created successfully.');
        $this->resetForm();
        $this->loadSeasons();
    }

    public function edit($id)
    {
        $season = Season::findOrFail($id);
        $this->season_id = $season->id;
        $this->name = $season->name;
        $this->start_date = $season->start_date;
        $this->end_date = $season->end_date;
        $this->editMode = true;
    }

    public function update()
    {
        $this->validate();

        Season::findOrFail($this->season_id)->update([
            'name' => $this->name,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
        ]);

        session()->flash('success', 'Season updated successfully.');
        $this->resetForm();
        $this->loadSeasons();
    }

    public function delete($id)
    {
        $this->dispatch('showConfirm', itemId: $id);
    }

    public function DestroyData($id)
    {
        Season::findOrFail($id)->delete();
        session()->flash('success', 'Season deleted successfully.');
        $this->loadSeasons();
    }

    public function resetForm()
    {
        $this->reset(['name', 'start_date', 'end_date', 'season_id', 'editMode']);
    }

    public function render()
    {
        return view('livewire.admin.season-crud');
    }
}
