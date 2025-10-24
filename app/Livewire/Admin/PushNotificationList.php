<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\WithPagination;
use App\Services\FCMService;
use App\Models\PushNotification;
use App\Jobs\SendPushNotificationJob;

class PushNotificationList extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $page = 1;
    public $tab = 'all';
    public $user_type = "B2C";
    public $search = '';
    public $message = '';
    public $selectedUsers = [];
    public $sendNotifications = [];
    public function gotoPage($value, $pageName = 'page')
    {
        $this->setPage($value, $pageName);
        $this->page = $value;
    }
    public function mount(){
        $this->changeTab('all');
    }
    public function changeUserType($type){
        $this->user_type = $type;
        $this->changeTab($this->tab);
    }
    public function changeTab($tab){
        $this->selectedUsers = [];
        if($tab=='all'){
            $this->search = '';
            $this->selectedUsers = User::where('user_type', $this->user_type)->orderBy('name', 'ASC')->pluck('mobile')->toArray();
        }elseif($tab=='unassigned'){
            $this->selectedUsers = User::where('user_type', $this->user_type)
            ->whereDoesntHave('assigned_vehicle')->orderBy('name', 'ASC')->pluck('mobile')->toArray();
        }elseif($tab=='assigned'){
            $this->selectedUsers = User::where('user_type', $this->user_type)
            ->whereHas('assigned_vehicle')->orderBy('name', 'ASC')->pluck('mobile')->toArray();
        }elseif($tab=='overdue'){
            $this->selectedUsers = User::where('user_type', $this->user_type)
            ->whereHas('overdue_vehicle')->orderBy('name', 'ASC')->pluck('mobile')->toArray();
        }else{
            $this->selectedUsers = [];
        }
        $this->tab = $tab;
        $this->gotoPage(1);
    }
    public function toggleUserSelection($mobile)
    {
        if (in_array($mobile, $this->selectedUsers)) {
            $this->selectedUsers = array_values(array_diff($this->selectedUsers, [$mobile]));
        } else {
            $this->selectedUsers[] = $mobile;
        }
    }
    public function toggleSelectAll(){
        if (count($this->selectedUsers) === User::where('user_type', $this->user_type)->count()) {
            $this->selectedUsers = [];
        } else {
            $this->selectedUsers = User::where('user_type', $this->user_type)
            ->when($this->search, function($query) {
                $query->where(function($subQuery) {
                    $subQuery->where('name', 'like', '%' . $this->search . '%')
                            ->orWhere('email', 'like', '%' . $this->search . '%')
                            ->orWhere('mobile', 'like', '%' . $this->search . '%')
                            ->orWhereHas('organization_details', function ($q3) {
                                $q3->where('name', 'like', '%' . $this->search . '%')
                                    ->orWhere('organization_id', 'like', '%' . $this->search . '%')
                                    ->orWhere('email', 'like', '%' . $this->search . '%')
                                    ->orWhere('mobile', 'like', '%' . $this->search . '%');
                            });
                });
            })->pluck('mobile')->toArray();
        }
    }
    public function searchUsers($value){
        $this->search = $value;
        $this->gotoPage(1);
    }
    public function clearSearch(){
        $this->search = '';
        $this->gotoPage(1);
        $this->dispatch('clear-search-input');
    }
    public function messageText($value){
        $this->message = $value;
    }
   public function sendPushNotificationForm()
    {
        if (count($this->selectedUsers) === 0 || !$this->message) {
            $this->dispatch('notification', [
                'type' => 'error',
                'message' => 'Please select at least one user and enter a message.'
            ]);
            return;
        }

        // Dispatch the job to the queue
        SendPushNotificationJob::dispatch($this->selectedUsers, $this->message);

        PushNotification::create([
            'message' => $this->message,
            'recipient_count' => count($this->selectedUsers),
            'rider_type' => $this->user_type,
            'status' => ucwords($this->tab),
            'recipients' => $this->selectedUsers,
            ]);
        $this->message = '';
        
        session()->flash('success', 'Push notifications queued for sending.');
        $this->dispatch('notification-send');
    }

    public function render()
    {
        // Latest 10 Push Notifications
        $this->sendNotifications = PushNotification::orderBy('created_at', 'DESC')->take(10)->get();

        $all_users = User::where('user_type', $this->user_type)
        ->when($this->search, function($query) {
            $query->where(function($subQuery) {
                $subQuery->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%')
                        ->orWhere('mobile', 'like', '%' . $this->search . '%')
                        ->orWhereHas('organization_details', function ($q3) {
                            $q3->where('name', 'like', '%' . $this->search . '%')
                                ->orWhere('organization_id', 'like', '%' . $this->search . '%')
                                ->orWhere('email', 'like', '%' . $this->search . '%')
                                ->orWhere('mobile', 'like', '%' . $this->search . '%');
                        });
            });
        })
        ->orderBy('name', 'ASC')
        ->paginate(20);

        $unassigned_users = User::where('user_type', $this->user_type)
        ->whereDoesntHave('assigned_vehicle')->whereDoesntHave('overdue_vehicle')->orderBy('name', 'ASC')->paginate(20);

        $assigned_users = User::where('user_type', $this->user_type)->whereHas('assigned_vehicle')->orderBy('name', 'ASC')->paginate(20);
        $overdue_users = User::where('user_type', $this->user_type)->whereHas('overdue_vehicle')->orderBy('name', 'ASC')->paginate(20);

        return view('livewire.admin.push-notification-list',[
            'all_users' => $all_users,
            'unassigned_users' => $unassigned_users,
            'assigned_users' => $assigned_users,
            'overdue_users' => $overdue_users
        ]);
    }
}
