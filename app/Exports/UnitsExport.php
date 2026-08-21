<?php

namespace App\Exports;

use App\Models\Unit;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UnitsExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Unit::with('project') // Include related project data
        ->get()
        ->map(function ($unit, $index) {
            return [
                'project_name' => $unit->project ? $unit->project->project_name : 'N/A',
                'unit_name' => $unit->unit_name,
            ];
        });
    }
    public function headings(): array
    {
        return [
            'Project Name',
            'Unit Name',
        ];
    }
}
