<?php

namespace App\Exports;

use App\Models\AsignedVehicle;
use App\Models\User;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class RiderExport implements FromCollection, WithHeadings
{
    protected $verification;
    protected $type;

    public function __construct($verification, $type)
    {
        $this->verification = $verification;
        $this->type = $type;
    }

    public function collection()
    {
        $users = User::query()
            ->when($this->type != 'all', function ($q) {
                $q->where('user_type', $this->type);
            })
            ->when($this->verification != 'all', function ($q) {
                if ($this->verification == 'verified') {
                    $q->where('is_verified', 'verified');
                } else {
                    $q->where('is_verified', 'unverified');
                }
            })
            ->with(['assignedVehicle.stock'])
            ->get();

        $data = collect();

        foreach ($users as $user) {

            $assignment = $user->assignedVehicle()
                ->whereIn('status', ['assigned', 'overdue'])
                ->latest()
                ->first();

            $data->push([
                'rider_name' => $user->name,
                'email' => $user->email,
                'mobile' => $user->mobile,
                'user_type' => $user->user_type,
                'status' => $user->is_verified,
                'assigned_vehicle' => optional($assignment?->stock)->vehicle_number,
                'start_date' => $assignment?->start_date,
                'end_date' => $assignment?->end_date,
                'assigned_status' => $assignment?->status,
            ]);
        }

        return $data;
    }

    public function headings(): array
    {
        return [
            'Rider Name',
            'Email',
            'Mobile',
            'User Type',
            'Status',
            'Assigned Vehicle',
            'Start Date',
            'End Date',
            'Assigned Status',
        ];
    }
}