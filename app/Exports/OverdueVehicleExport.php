<?php

namespace App\Exports;

use App\Models\Stock;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class OverdueVehicleExport implements FromCollection, WithHeadings
{
    protected $branch;
    protected $model;
    protected $overdue_days;
    protected $search;

    public function __construct($branch,$model,$search,$overdue_days)
    {
        $this->branch = $branch;
        $this->model = $model;
        $this->overdue_days = $overdue_days;
        $this->search = $search;
    }

    public function collection()
    {
        $today = Carbon::today();

        $query = Stock::with(['product','overdueVehicle.user'])
            ->when($this->branch, function ($q) {
                $q->where('branch_id',$this->branch);
            })
            ->when($this->model, function ($q) {
                $q->where('product_id',$this->model);
            })
            ->whereHas('overdueVehicle')

            // 🔥 ✅ OVERDUE DROPDOWN FILTER (ADDED)
            ->when($this->overdue_days !== null && $this->overdue_days !== '', function ($query) use ($today) {

                if ($this->overdue_days === '20+') {

                    $query->whereHas('overdueVehicle', function ($q) use ($today) {
                        $q->whereRaw(
                            'DATEDIFF(?, end_date) > 20',
                            [$today]
                        );
                    });

                } else {

                    $days = (int) $this->overdue_days;

                    $query->whereHas('overdueVehicle', function ($q) use ($today, $days) {
                        $q->whereRaw(
                            'ABS(DATEDIFF(?, end_date)) = ?',
                            [$today, $days]
                        );
                    });
                }
            });

        // 🔥 EXISTING SEARCH (small condition added, rest same)
        if ($this->search) {

            if (is_numeric($this->search) && empty($this->overdue_days)) {

                $days = (int) $this->search;

                $query->whereHas('overdueVehicle', function ($q) use ($today, $days) {
                    $q->whereRaw(
                        'ABS(DATEDIFF(?, end_date)) = ?',
                        [$today, $days]
                    );
                });

            } else {

                $searchTerm = '%'.$this->search.'%';

                $query->where(function($q) use ($searchTerm){

                    $q->where('vehicle_number','like',$searchTerm)
                    ->orWhere('imei_number','like',$searchTerm)
                    ->orWhere('chassis_number','like',$searchTerm)
                    ->orWhere('friendly_name','like',$searchTerm)

                    ->orWhereHas('overdueVehicle.user', function ($uq) use ($searchTerm) {
                        $uq->where('name','like',$searchTerm)
                        ->orWhere('mobile','like',$searchTerm)
                        ->orWhere('email','like',$searchTerm);
                    });

                })
                ->orderBy('id', 'DESC')
                ->orderBy('product_id', 'DESC');

            }
        }

        return $query->get()->map(function($vehicle) use ($today){

            $endDate = Carbon::parse($vehicle->overdueVehicle->end_date);
            $daysLate = $today->diffInDays($endDate,false);

            return [
                'Vehicle Model' => $vehicle->product->title ?? 'N/A',
                'Vehicle Number' => $vehicle->vehicle_number,
                'Rider Name' => $vehicle->overdueVehicle->user->name ?? 'N/A',
                'Mobile' => $vehicle->overdueVehicle->user->mobile ?? '',
                'Email' => $vehicle->overdueVehicle->user->email ?? '',
                'Start Date' => $vehicle->overdueVehicle->start_date,
                'End Date' => $vehicle->overdueVehicle->end_date,
                'Days Late' => round(abs($daysLate)),
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Vehicle Model',
            'Vehicle Number',
            'Rider Name',
            'Mobile',
            'Email',
            'Start Date',
            'End Date',
            'Days Late'
        ];
    }
}