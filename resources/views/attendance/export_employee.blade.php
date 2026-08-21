<table>
    <tr>
        <td colspan="{{ count($dates) + 1 }}"><strong>ATTENDANCE REPORT</strong></td>
    </tr>
    <tr>
        <td colspan="{{ count($dates) + 1 }}"><strong>Period:</strong> {{ \Carbon\Carbon::parse($start_date)->format('M d, Y') }} To {{ \Carbon\Carbon::parse($end_date)->format('M d, Y') }}</td>
    </tr>
    <tr>
        <td colspan="{{ count($dates) + 1 }}"></td>
    </tr>

    @foreach($employees as $employee)
        <!-- Employee Information -->
        <tr>
            <td colspan="{{ count($dates) + 1 }}"><strong>Employee Code:</strong> {{ \Auth::user()->employeeIdFormat($employee->employee_id) }}</td>
        </tr>
        <tr>
            <td colspan="{{ count($dates) + 1 }}"><strong>Employee Name:</strong> {{ $employee->name }}</td>
        </tr>
        <tr>
            <td colspan="{{ count($dates) + 1 }}"></td>
        </tr>
        
        <!-- Date Header Row -->
        <tr>
            <td><strong>Days</strong></td>
            @foreach($dates as $date)
                <td>{{ \Carbon\Carbon::parse($date)->format('d') }}<br>{{ \Carbon\Carbon::parse($date)->format('D') }}</td>
            @endforeach
        </tr>
        
        <!-- Status Row -->
        <tr>
            <td><strong>Status</strong></td>
            @foreach($dates as $date)
                <td>
                    @isset($attendanceData[$employee->id][$date]['status'])
                        @php
                            $status = $attendanceData[$employee->id][$date]['status'];
                            
                            // Use status from database directly without overriding

                            if ($status == 'Leave') {
                                $display = 'Leave';
                            } elseif ($status == 'LOP') {
                                $display = 'LOP';
                            } elseif ($status == 'Holiday') {
                                $display = 'H-Day';
                            } elseif ($status == 'Absent') {
                                $display = (\Carbon\Carbon::parse($date)->dayOfWeek == \Carbon\Carbon::SUNDAY) ? 'WO' : 'A';
                            } else {
                                $display = substr($status, 0, 1);
                                if ($status == 'Early Clock-Out') {
                                    $display = 'P (ECO)';
                                } elseif ($status == 'Half Day') {
                                    $display = 'H';
                                } else {
                                    $display = 'P'; // Present or Single Punch
                                }
                            }

                            // Add indicators for Present/Half Day
                            if (in_array($display, ['P', 'H', 'P (EL)'])) {
                                $att = $attendanceData[$employee->id][$date] ?? null;
                                if ($att) {
                                    $indicators = [];
                                    $isLate = !empty($att['late']) && $att['late'] != '00:00:00';
                                    $isEarlyLeaving = !empty($att['early_leaving']) && $att['early_leaving'] != '00:00:00';
                                    
                                    if ($isLate) $indicators[] = 'L';

                                    if (!empty($indicators)) {
                                        $display .= ' (' . implode(', ', $indicators) . ')';
                                    }
                                }
                            }
                        @endphp
                        {{ $display }}
                    @else
                        {{ (\Carbon\Carbon::parse($date)->dayOfWeek == \Carbon\Carbon::SUNDAY) ? 'WO' : 'A' }}
                    @endisset
                </td>
            @endforeach
        </tr>
        
        <!-- In Time Row -->
        <tr>
            <td><strong>IN Time</strong></td>
            @foreach($dates as $date)
                <td>
                    @isset($attendanceData[$employee->id][$date]['clock_in'])
                        @if($attendanceData[$employee->id][$date]['clock_in'] != '00:00:00')
                            {{ substr($attendanceData[$employee->id][$date]['clock_in'], 0, 5) }}
                        @else
                            -
                        @endif
                    @else
                        -
                    @endisset
                </td>
            @endforeach
        </tr>
        
        <!-- Out Time Row -->
        <tr>
            <td><strong>OUT Time</strong></td>
            @foreach($dates as $date)
                <td>
                    @isset($attendanceData[$employee->id][$date]['clock_out'])
                        @if($attendanceData[$employee->id][$date]['clock_out'] != '00:00:00')
                            {{ substr($attendanceData[$employee->id][$date]['clock_out'], 0, 5) }}
                        @else
                            -
                        @endif
                    @else
                        -
                    @endisset
                </td>
            @endforeach
        </tr>
        
        <!-- Total Hours Row -->
        <tr>
            <td><strong>Total Hours</strong></td>
            @foreach($dates as $date)
                <td>
                    @isset($attendanceData[$employee->id][$date]['total'])
                        @if($attendanceData[$employee->id][$date]['total'] != '00:00')
                            {{ $attendanceData[$employee->id][$date]['total'] }}
                        @else
                            -
                        @endif
                    @else
                        -
                    @endisset
                </td>
            @endforeach
        </tr>
        
        <!-- Summary Section -->
        <tr>
            <td colspan="{{ count($dates) + 1 }}"></td>
        </tr>
        <tr>
            <td colspan="{{ count($dates) + 1 }}"><strong>SUMMARY</strong></td>
        </tr>
        <tr>
            <td><strong>Total Working Days:</strong></td>
            <td colspan="{{ count($dates) }}">{{ $totalWorkingDays }} days</td>
        </tr>
        <tr>
            <td><strong>Total Month Days:</strong></td>
            <td colspan="{{ count($dates) }}">{{ count($dates) }} days</td>
        </tr>
        <tr>
            <td><strong>Monthly Total Worked Hours:</strong></td>
            <td colspan="{{ count($dates) }}">{{ $totalHoursFormatted }}</td>
        </tr>
        <tr>
            <td><strong>Required Hours:</strong></td>
            <td colspan="{{ count($dates) }}">{{ $requiredHoursFormatted }}</td>
        </tr>
        <tr>
            <td><strong>Extra/Short Hours:</strong></td>
            <td colspan="{{ count($dates) }}">
                @if(strpos($extraShortHours, '+') === 0)
                    <strong style="color: green;">{{ $extraShortHours }} (Extra)</strong>
                @elseif(strpos($extraShortHours, '-') === 0)
                    <strong style="color: red;">{{ $extraShortHours }} (Short)</strong>
                @else
                    {{ $extraShortHours }}
                @endif
            </td>
        </tr>
    @endforeach
</table>
