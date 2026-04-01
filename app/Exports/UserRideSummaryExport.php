<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Models\AsignedVehicle;
use App\Models\ExchangeVehicle;

class UserRideSummaryExport implements FromCollection, WithHeadings
{
    protected $rider_id;

    public function __construct($rider_id)
    {
        $this->rider_id = $rider_id;
    }

    public function collection()
    {
        $assignedVehicle = AsignedVehicle::whereIn('status', ['assigned','overdue'])
            ->where('user_id', $this->rider_id)
            ->first();

        $exchangeVehicles = ExchangeVehicle::with('stock', 'admin', 'order', 'user')
            ->where('user_id', $this->rider_id)
            ->orderBy('id', 'DESC')
            ->get();

        if ($assignedVehicle) {
            $assignedVehicle->exchanged_by = $assignedVehicle->assigned_by;
            $exchangeVehicles->prepend($assignedVehicle);
        }

        $lastIndex = $exchangeVehicles->count() - 1;

        $transformedData = $exchangeVehicles->values()->map(function ($data, $index) use ($lastIndex) {

            // ================= DATE LOGIC =================
            $dateText = '';

            if ($data->status == 'exchanged') {
                $dateText = 'Exchanged Date : ' . date('d M y h:i A', strtotime($data->exchanged_at));
            } elseif ($data->status == 'renewal') {
                $dateText = 'Renewal Date : ' . date('d M y h:i A', strtotime($data->end_date));
            } elseif ($data->status == 'returned') {
                $dateText = 'Returned Date : ' . date('d M y h:i A', strtotime($data->exchanged_at));
            } else {
                if (!$data->assigned_at || $data->assigned_at == '1970-01-01 00:00:00') {
                    $dateText = 'Assigned Date : N/A';
                } else {
                    $dateText = 'Assigned Date : ' . date('d M y h:i A', strtotime($data->assigned_at));
                }
            }

            // ===== LAST INDEX EXTRA START DATE (same as blade) =====
            if ($index == $lastIndex) {
                if (in_array($data->status, ['exchanged', 'renewal', 'returned'])) {
                    $dateText .= ' | Start Date : ' . date('d M y h:i A', strtotime($data->start_date));
                }
            }

            // ================= RENT =================
            $rent = '';
            if ($data->status == 'renewal') {
                $rent = $data->order
                    ? $data->rental_amount
                    : 0;
            }

            // ================= RENT STATUS =================
            if ($index == $lastIndex) {
                if (in_array($data->status, ['exchanged', 'returned'])) {
                    $rentStatus = ucwords($data->status);
                } else {
                    $rentStatus = 'Start';
                }
            } else {
                $rentStatus = ucwords($data->status);
            }

            // ================= ACTION BY =================
            if (!empty($data->exchanged_by) || !empty($data->assigned_by)) {
                $actionBy = optional($data->admin)->email ?? '....';
            } else {
                $actionBy = optional($data->user)->email ?? 'N/A';
            }

            return [
                'models' => $data->stock && $data->stock->product ? $data->stock->product->title : "N/A",
                'vehicle' => $data->stock ? $data->stock->vehicle_number : "N/A",
                'date' => $dateText,
                'rent_amount' => $rent,
                'rent_status' => $rentStatus,
                'action_by' => $actionBy,
            ];
        });

        return $transformedData;
    }

    public function headings(): array
    {
        return [
            'Models',
            'Vehicle Number',
            'Date',
            'Rent Amount',
            'Rent Status',
            'Action By',
        ];
    }
}