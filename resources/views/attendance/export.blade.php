<table>
    <tr>
    <tr>
        <td colspan="{{ count($dates) + 1 }}"><strong>{{ \Carbon\Carbon::parse($start_date)->format('M d Y') }} To {{ \Carbon\Carbon::parse($end_date)->format('M d Y') }}</strong></td>
        <td></td>
        <td colspan="12"><strong>Summary</strong></td>
    </tr>
    <tr>
        <td colspan="{{ count($dates) + 1 }}"></td>
        <td></td>
        <td><strong>Monthly Days</strong></td>
        <td><strong>Total Present Days</strong></td>
        <td><strong>Early Clock-Out</strong></td>
        <td><strong>Half Day</strong></td>
        <td><strong>Total LWP</strong></td>
        <td><strong>Week Off</strong></td>
        <td><strong>Total Leave</strong></td>
        <td><strong>Total Payable Days</strong></td>
        
    </tr>
    
    @foreach($employees as $employee)
        <!-- Employee Header -->
        <tr>
            <td colspan="{{ count($dates) + 1 }}"><strong>Employee Code:</strong> {{ $employee->employee_id }} </td>
            <td></td>
            <td>
                <strong>{{ \Carbon\Carbon::parse($end_date)->daysInMonth }}</strong>
            </td>
            <td>
                @if(isset($payableDaysTotals[$employee->id]))
                    <strong>{{ number_format($payableDaysTotals[$employee->id]['present'], 1) }}</strong>
                @else
                    <strong>0</strong>
                @endif
            </td>
            <td>
                @if(isset($payableDaysTotals[$employee->id]))
                    <strong>{{ number_format($payableDaysTotals[$employee->id]['eco'], 1) }}</strong>
                @else
                    <strong>0</strong>
                @endif
            </td>
            <td>
                @if(isset($payableDaysTotals[$employee->id]))
                    <strong>{{ number_format($payableDaysTotals[$employee->id]['hd'], 1) }}</strong>
                @else
                    <strong>0</strong>
                @endif
            </td>
            <td>
                @if(isset($payableDaysTotals[$employee->id]))
                    <strong>{{ number_format($payableDaysTotals[$employee->id]['lwp'], 1) }}</strong>
                @else
                    <strong>0</strong>
                @endif
            </td>
            <td>
                @if(isset($payableDaysTotals[$employee->id]))
                    <strong>{{ number_format($payableDaysTotals[$employee->id]['wo'], 1) }}</strong>
                @else
                    <strong>0</strong>
                @endif
            </td>
            <td>
                @if(isset($payableDaysTotals[$employee->id]))
                    <strong>{{ number_format($payableDaysTotals[$employee->id]['total_leaves'], 1) }}</strong>
                @else
                    <strong>0</strong>
                @endif
            </td>
            <td>
                @if(isset($payableDaysTotals[$employee->id]))
                    <strong>{{ number_format($payableDaysTotals[$employee->id]['total'], 1) }}</strong>
                @else
                    <strong>0</strong>
                @endif
            </td>
        </tr>
        <tr>
            <td colspan="{{ count($dates) + 1 }}"><strong>Employee Name:</strong> {{ $employee->full_name }}</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        
        <!-- Status Row -->
        <tr>
            <td><strong>Days</strong></td>
            @foreach($dates as $date)
                <td>{{ \Carbon\Carbon::parse($date)->format('d D') }}</td>
            @endforeach
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td><strong>Status</strong></td>
            @foreach($dates as $date)
                <td>
                    @php
                        $code = $statusCodes[$employee->id][$date] ?? '';
                        $displayCode = '';
                        if (!empty($code) && !in_array($code, ['P', 'SP', 'HD', 'A', 'WO', 'LOP', 'LWP', 'H', 'ECO'])) {
                            // If it's a leave type, just show Leave
                            $displayCode = 'Leave';
                        } elseif ($code === 'ECO') {
                            // Convert ECO back to P (ECO)
                            $displayCode = 'P (ECO)';
                        } else {
                            $displayCode = $code;
                        }

                        // Add indicators if Present or Single Punch
                        if (in_array($code, ['P', 'SP', 'HD'])) {
                            $att = $attendanceData[$employee->id][$date] ?? null;
                            if ($att) {
                                $indicators = [];
                                $isLate = !empty($att['late']) && $att['late'] != '00:00:00';
                                
                                if ($isLate) $indicators[] = 'L';

                                if (!empty($indicators)) {
                                    $displayCode .= ' (' . implode(', ', $indicators) . ')';
                                }
                            }
                        }

                        echo $displayCode;
                    @endphp
                </td>
            @endforeach
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        
        <!-- In Time Row -->
        <tr>
            <td><strong>InTime</strong></td>
            @foreach($dates as $date)
                <td>
                    @isset($attendanceData[$employee->id][$date]['clock_in'])
                        {{ substr($attendanceData[$employee->id][$date]['clock_in'], 0, 5) }}
                    @endisset
                </td>
            @endforeach
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        
        <!-- Out Time Row -->
        <tr>
            <td><strong>OutTime</strong></td>
            @foreach($dates as $date)
                <td>
                    @isset($attendanceData[$employee->id][$date]['clock_out'])
                        {{ substr($attendanceData[$employee->id][$date]['clock_out'], 0, 5) }}
                    @endisset
                </td>
            @endforeach
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        
        <!-- Total Time Row -->
        <tr>
            <td><strong>Total</strong></td>
            @foreach($dates as $date)
                <td>
                    @isset($attendanceData[$employee->id][$date]['total'])
                        {{ $attendanceData[$employee->id][$date]['total'] }}
                    @else
                        00:00
                    @endisset
                </td>
            @endforeach
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
    @endforeach
</table>
