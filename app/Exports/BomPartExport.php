<?php

namespace App\Exports;

use App\Models\BomPart;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class BomPartExport implements FromCollection, WithHeadings
{
    protected $search;

    public function __construct($search)
    {
        $this->search = $search;
    }

    public function collection()
    {
        return BomPart::with('product')
            ->when($this->search, function ($query) {
                $query->where(function ($query) {
                    $query->where('part_name', 'like', '%' . $this->search . '%')
                        ->orWhere('part_number', 'like', '%' . $this->search . '%')
                        ->orWhere('part_price', 'like', '%' . $this->search . '%')
                        ->orWhere('warranty', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy('id','DESC')
            ->get()
            ->map(function ($part) {
                return [
                    'Model' => optional($part->product)->title ?? 'N/A',
                    'Part Name' => $part->part_name,
                    'Part Number' => $part->part_number,
                    'Warranty' => $part->warranty,
                    'Price' => $part->part_price,
                ];
            });
    }

    public function headings(): array
    {
        return [
            'Model',
            'Part Name',
            'Part Number',
            'Warranty',
            'Price'
        ];
    }
}