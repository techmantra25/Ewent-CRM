<?php
namespace App\Livewire\Admin;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ChangePassword extends Component
{
    public $current_password;
    public $new_password;
    public $new_password_confirmation;

    protected function guard()
    {
        if (Auth::guard('admin')->check()) {
            return 'admin';
        }

        if (Auth::guard('organization')->check()) {
            return 'organization';
        }

        return null;
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName, $this->rules());
    }   

    public function rules()
    {
        return [
            'current_password' => ['required'],
            'new_password' => ['required', 'min:6', 'different:current_password', 'same:new_password_confirmation'],
            'new_password_confirmation' => ['required'],
        ];
    }

    public function changePassword()
    {
        $this->validate();

        $guard = $this->guard();

        if (!$guard) {
            abort(403);
        }

        $user = Auth::guard($guard)->user();

        if (!Hash::check($this->current_password, $user->password)) {
            $this->addError('current_password', 'Current password is incorrect.');
            return;
        }

        $user->password = Hash::make($this->new_password);
        $user->save();

        $this->reset([
            'current_password',
            'new_password',
            'new_password_confirmation'
        ]);

        session()->flash('message', 'Password updated successfully!');
    }

    public function render()
    {
        return view('livewire.admin.change-password');
    }

    public function resetForm()
    {
        $this->reset(['current_password', 'new_password', 'new_password_confirmation']);
    }
}

?>