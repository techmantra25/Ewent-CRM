<?php

namespace App\Exports;

use App\Models\AsignedVehicle;
use App\Models\ExchangeVehicle;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Carbon\Carbon;

class VehicleSummaryExport implements FromArray, WithHeadings
{
    protected $vehicle_id;
    protected $model_id;
    protected $start_date;
    protected $end_date;

    public function __construct($vehicle_id = null, $model_id = null, $start_date = null, $end_date = null)
    {
        $this->vehicle_id = $vehicle_id;
        $this->model_id   = $model_id;
        $this->start_date = $start_date;
        $this->end_date   = $end_date;
    }

    public function array(): array
    {
        // --- 1. Fetch assigned vehicle (only one, tied to vehicle_id if provided) ---
        $assignedVehicles = AsignedVehicle::whereIn('status', ['assigned', 'overdue'])
        ->with(['stock.product', 'order', 'user.organization_details'])
        ->when($this->vehicle_id, fn($query) => $query->where('vehicle_id', $this->vehicle_id))
        ->when($this->model_id, fn($query) => $query->whereHas('order', fn($q) => $q->where('product_id', $this->model_id)))
        ->where(function ($query) {
            $query->where('start_date', '<=', Carbon::parse($this->end_date)->endOfDay())
                ->where('end_date', '>=', Carbon::parse($this->start_date)->startOfDay());
        })
        ->get();

        // --- 2. Fetch exchange vehicles ---
        $exchangeVehicles = ExchangeVehicle::with(['stock.product', 'order', 'user.organization_details'])
            ->when($this->vehicle_id, fn($query) => $query->where('vehicle_id', $this->vehicle_id))
            ->when($this->model_id, fn($query) => $query->whereHas('order', fn($q) => $q->where('product_id', $this->model_id)))
            ->whereIn('status', ['returned', 'renewal','exchanged'])
            ->where(function ($q){
                $q->whereIn('status',['returned','renewal'])
                ->orWhere(function ($q2){
                    $q2->where('status','exchanged')
                    ->whereRaw("TIMESTAMPDIFF(HOUR, start_date, end_date) > 24");
                });
            })
            ->where(function ($query) {
                $query->where('start_date', '<=', Carbon::parse($this->end_date)->endOfDay())
                    ->where('end_date', '>=', Carbon::parse($this->start_date)->startOfDay());
            })
            ->orderBy('id', 'DESC')
            ->get();

        // --- 3. Group exchange vehicles by vehicle_id ---
        $grouped = $exchangeVehicles->groupBy('vehicle_id');
        $finalCollection = collect();

        foreach ($grouped as $vehicleId => $vehicles) {
            // If any assignedVehicle(s) exist for this vehicle_id, push them first
            $matchedAssigned = $assignedVehicles->where('vehicle_id', $vehicleId);
            foreach ($matchedAssigned as $aVehicle) {
                $aVehicle->exchanged_by = $aVehicle->assigned_by;
                $finalCollection->push($aVehicle);
            }

            // Push all exchangeVehicles for this vehicle_id
            foreach ($vehicles as $v) {
                $finalCollection->push($v);
            }
        }

        //  Add any remaining assignedVehicles that didn't match any vehicle_id in grouped exchangeVehicles
        $remainingAssigned = $assignedVehicles->filter(fn($a) => !$finalCollection->contains(fn($item) => $item->vehicle_id == $a->vehicle_id));
        foreach ($remainingAssigned as $aVehicle) {
            $aVehicle->exchanged_by = $aVehicle->assigned_by;
            $finalCollection->prepend($aVehicle); // prepend to bring first
        }

        // --- 4. Build rows for export ---
        $rows = [];
        foreach ($finalCollection as $item) {
            if($item->order->user_type === 'B2C'){

                // Safe handling: check if order exists and duration > 0
                if ($item->order && $item->order->rent_duration > 0) {

                    // ================= OVERLAP LOGIC =================
                    $actualStart = \Carbon\Carbon::parse($item->start_date);
                    $actualEnd   = \Carbon\Carbon::parse($item->end_date);

                    // if running (assigned), consider till filter end date
                    if ($item->status == "assigned") {
                        $actualEnd = \Carbon\Carbon::parse($this->end_date);
                    }

                    $filterStart = \Carbon\Carbon::parse($this->start_date)->startOfDay();
                    $filterEnd   = \Carbon\Carbon::parse($this->end_date)->endOfDay();

                    $overlapStart = $actualStart->greaterThan($filterStart) ? $actualStart : $filterStart;
                    $overlapEnd   = $actualEnd->lessThan($filterEnd) ? $actualEnd : $filterEnd;

                    // ================= DURATION (FIXED) =================
                    $item_duration = 0;

                    if ($overlapStart <= $overlapEnd) {
                        $item_duration = max(
                            1,
                            $overlapStart->copy()->startOfDay()
                                ->diffInDays($overlapEnd->copy()->startOfDay())
                        );
                    }

                    // ================= PER DAY PRICE =================
                    $item_per_day_price = $item->rental_amount / $item->order->rent_duration;

                    // ================= CAP BY PLAN =================
                    if ($item_duration > $item->order->rent_duration) {
                        $item_duration = $item->order->rent_duration;
                    }

                    // ================= FINAL PRICE =================
                    $item_price = $item_per_day_price * $item_duration;

                } else {
                    $item_per_day_price = 0.0;
                    $item_duration      = 0.0;
                    $item_price         = 0.0;
                }

                /* force 2 decimal WITHOUT converting to string */
                $item_per_day_price = round($item_per_day_price, 2);
                $item_duration      = round($item_duration, 2);
                $item_price         = round($item_price, 2);

                $ExchangeVehicleData = ExchangeVehicle::where('order_id', $item->order_id)
                    ->where('vehicle_id', $item->vehicle_id)
                    ->orderBy('id', 'ASC')
                    ->first();

                if($ExchangeVehicleData){
                    $assignedValue = $ExchangeVehicleData->start_date;
                }else{
                    $assignedValue = $item->start_date;
                }

                // Unassigned Value
                if($item->status ==='returned'){
                    $unassignedValue = $this->formatUnassigned($item->end_date, $item->exchanged_at, $item->order->user_type);
                }else{
                    $unassignedValue ='';
                }

                $endDate = $item->end_date
                    ? (
                        Carbon::parse($this->end_date)->lessThan(Carbon::parse($item->end_date))
                            ? Carbon::parse($this->end_date)
                            : Carbon::parse($item->end_date)
                    )
                    : null;

                $rows[] = [
                    $item->stock?->vehicle_number ?? 'N/A',
                    $item->stock?->chassis_number ?? 'N/A',
                    $item->stock?->created_at ? \Carbon\Carbon::parse($item->stock?->created_at)->format('d M y h:i A') : '----',
                    '....',
                    $item->stock?->product?->title ?? 'N/A',
                    $item->start_date ? \Carbon\Carbon::parse($item->start_date)->format('d M y h:i A') : '----',
                    $endDate
                        ? $endDate->format('d M y h:i A') . ($item->status === "assigned" ? ' (Running)' : '')
                        : '----',
                    $item_duration,
                    $item->order?->user_type ?? 'N/A',
                    ($item->order && $item->order->user_type === 'B2C')
                        ? (float) $item_price
                        : '',
                    $assignedValue ? \Carbon\Carbon::parse($assignedValue)->format('d M y h:i A') : '',
                    $unassignedValue,
                    $item->user?->name ?? 'N/A',
                    $item->user ? ($item->user->country_code . ' ' . $item->user->mobile) : 'N/A',
                    $item->user?->email ?? 'N/A',
                    ($item->order && $item->order->user_type === 'B2B')
                        ? 'ORG: ' . optional($item->user->organization_details)->name
                        : '----',
                ];
            }
        }
        return $rows;
    }
    // protected function ($startDate, $endDate)
    // {
        
    // }

    public function headings(): array
    {
        return [
            'Vehicle No',
            'Chassis No',
            'Creation Date',
            'Last Retreived Location',
            'Model',
            'Start Date',
            'End Date',
            'Duration(Days)',
            'Rent Type',
            'Rent Amount',
            'Assigned  At',
            'Unassigned At',
            'Rider Name',
            'Mobile No',
            'Email',
            'Organization',
        ];
    }

    protected function formatUnassigned($endDate, $returnedDate, $userType)
    {
        $endDate      = \Carbon\Carbon::parse($endDate);
        $returnedDate = \Carbon\Carbon::parse($returnedDate);
        $days         = $returnedDate->diffInDays($endDate);

        $label = $returnedDate->format('d M y h:i A');

        if ($userType === 'B2C') {
            if ($returnedDate->lt($endDate)) {
                $label .= " (" . abs(round($days)) . " days before)";
            } elseif ($returnedDate->gt($endDate)) {
                $label .= " (" . abs(round($days)) . " days after)";
            } else {
                $label .= " (On time)";
            }
        }

        return $label;
    }
}
