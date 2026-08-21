<?php

namespace App\Exports;

use App\Models\TimeSheet;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class TimeSheetExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected $timeSheets;

    public function __construct($timeSheets)
    {
        $this->timeSheets = $timeSheets;
    }

    public function collection()
    {
        return $this->timeSheets->map(function ($timesheet) {
            return [
                'ID' => $timesheet->id,
                'Employee Name' => $timesheet->employee->name ?? '',
                'Date' => $timesheet->date,
                'Hours' => $timesheet->hours,
                'Remark' => $timesheet->remark,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'ID',
            'Employee Name',
            'Date',
            'Hours',
            'Remark',
        ];
    }
}