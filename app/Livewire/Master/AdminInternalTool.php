<?php

namespace App\Livewire\Master;

use Livewire\Component;

class AdminInternalTool extends Component
{
    public $tools = [];

    public function mount()
    {
        $this->tools = [
            [
                'title' => 'Failed Payment Captured',
                'route' => 'admin.admin_internal_tools.failed_payment_captured',
            ],
            [
                'title' => 'Rider Type Change(B2B/B2C)',
                'route' => 'admin.admin_internal_tools.rider_type_change',
            ],
        ];
    }

    public function render()
    {
        return view('livewire.master.admin-internal-tool');
    }
}