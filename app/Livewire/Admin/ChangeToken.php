<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Illuminate\Support\Str;
use App\Models\ApiToken;

class ChangeToken extends Component
{
    public $generatedToken = null;

    public function mount()
    {
        // Load existing token if available
        $token = ApiToken::where('name', 'API-TOKEN')->first();
        if ($token) {
            $this->generatedToken = $token->token; // plain token
        }
    }

    public function generateToken()
    {
        $plainToken = Str::random(30);

        ApiToken::updateOrCreate(
            ['name' => 'API-TOKEN'],
            ['token' => $plainToken] // store as plain text
        );

        $this->generatedToken = $plainToken;

        session()->flash('message', 'API token generated successfully.');
    }

    public function render()
    {
        return view('livewire.admin.reset-token');
    }
}
