<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class RiderEngagementExport implements FromCollection, WithHeadings
{
    protected $tab;
    protected $search;
    protected $organization;

    public function __construct($tab, $search, $organization)
    {
        $this->tab = $tab;
        $this->search = $search;
        $this->organization = $organization;
    }

    public function collection()
    {
        $tab = $this->tab ?? 4;
        $searchTerm = '%' . $this->search . '%';

        $query = User::query()
            ->with(['organization_details', 'active_vehicle.stock'])
            ->when($this->organization, function ($q) {
                $q->where('organization_id', $this->organization);
            })
            ->when($this->search, function ($q) use ($searchTerm) {
                $q->where(function ($qq) use ($searchTerm) {
                    $qq->where('name', 'like', $searchTerm)
                        ->orWhere('mobile', 'like', $searchTerm)
                        ->orWhere('email', 'like', $searchTerm)
                        ->orWhere('customer_id', 'like', $searchTerm)
                        ->orWhereHas('organization_details', function ($q3) use ($searchTerm) {
                            $q3->where('name', 'like', $searchTerm)
                            ->orWhere('email', 'like', $searchTerm)
                            ->orWhere('mobile', 'like', $searchTerm);
                        })
                        ->orWhereHas('active_vehicle.stock', function ($q2) use ($searchTerm) {
                            $q2->where('vehicle_number', 'like', $searchTerm)
                                ->orWhere('vehicle_track_id', 'like', $searchTerm)
                                ->orWhere('imei_number', 'like', $searchTerm)
                                ->orWhere('chassis_number', 'like', $searchTerm)
                                ->orWhere('friendly_name', 'like', $searchTerm)
                                ->orWhereHas('product', function ($productQuery) use ($searchTerm) {
                                    $productQuery->where('title', 'like', $searchTerm)
                                        ->orWhere('types', 'like', $searchTerm)
                                        ->orWhere('product_sku', 'like', $searchTerm);
                                });
                        });
                });
            });

        switch ((int) $this->tab) {

            case 1: // All
                $query->where('is_verified', 'verified')
                    ->whereNull('vehicle_assign_status');
                break;

            case 2: // Await
                $query->where('is_verified', 'verified')
                    ->whereHas('accessToken')
                    ->whereNull('vehicle_assign_status')
                    ->doesntHave('await_order');
                break;

            case 3: // Ready to assign
                $query->where('is_verified', 'verified')
                    ->whereHas('ready_to_assign_order');
                break;

            case 4: // Active
                $query->where('is_verified', 'verified')
                    ->whereHas('active_order');
                break;

            case 5: // Inactive
                $query->where('is_verified', 'verified')
                    ->whereDoesntHave('accessToken');
                break;

            case 6: // Suspended
                $query->where('vehicle_assign_status', 'suspended');
                break;

            case 7: // Cancel Requested
                $query->where('is_verified', 'verified')
                    ->whereHas('cancel_requested_order');
                break;

            default:
                $query->where('is_verified', 'verified');
        }

        return $query->orderBy('id', 'DESC')->get()->map(function ($user) {
            return [
                'ID'            => $user->customer_id,
                'Name'          => $user->name,
                'Mobile'        => $user->mobile,
                'Email'         => $user->email,
                // 'Organization'  => optional($user->organization_details)->name,
                // 'Vehicle Status'=> $user->vehicle_assign_status ? ucfirst($user->vehicle_assign_status) : 'Active',
                // 'KYC Status'    => ucfirst($user->is_verified),
                'Address'       => $user->address,
                'Created Date'  => optional($user->created_at)->format('d-m-Y'),
            ];
        });
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Mobile',
            'Email',
            // 'Organization',
            // 'Vehicle Status',
            // 'KYC Status',
            'Address',
            'Created Date',
        ];
    }
}
