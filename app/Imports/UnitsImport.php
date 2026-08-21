<?php
namespace App\Imports;

use App\Models\Unit;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;


class UnitsImport implements ToModel, WithHeadingRow, WithCustomCsvSettings
{
    protected $projectId;
    protected $rowCount = 0;

    public function __construct($projectId)
    {
        $this->projectId = $projectId;
    }

    public function rules(): array
    {
        return [
            'unit_name' => 'nullable',  // No strict validation
            'unit_size' => 'nullable',  // Accept anything (string, number, etc.)
        ];
    }
    

public function prepareForValidation(array $row): array
{
    // Trim all values and convert empty strings to null
    return array_map(function ($value) {
        $value = is_string($value) ? trim($value) : $value;
        return $value === '' ? null : $value;
    }, $row);
}

public function model(array $row)
{
    // Normalize all keys to lowercase with underscores
    $normalizedRow = [];
    foreach ($row as $key => $value) {
        $normalizedKey = strtolower(str_replace([' ', '-'], '_', $key));
        $normalizedRow[$normalizedKey] = $value;
    }

    $unitName = $normalizedRow['unit_name'] ?? 
                $normalizedRow['name'] ?? 
                $normalizedRow['unit'] ?? 
                null;

    $unitSize = $normalizedRow['unit_size'] ?? 
                $normalizedRow['size'] ?? 
                $normalizedRow['square_feet'] ?? 
                null;

    if (!$unitName || !$unitSize) {
        logger()->error('Missing required fields in row:', $normalizedRow);
        return null; // Skip this row instead of throwing exception
    }

    return new Unit([
        'unit_name' => $unitName,
        'unit_size' => (float)$unitSize,
        'project_id' => $this->projectId
    ]);
}


public function getCsvSettings(): array
{
    return [
        'input_encoding' => 'UTF-8',
        'delimiter' => ',', // Try comma first
        'enclosure' => '"',
        'escape_character' => '\\'
    ];
}

    public function headingRow(): int
    {
        return 1; // Force first row as headers
    }

    public function getRowCount()
    {
        return $this->rowCount;
    }
    
}