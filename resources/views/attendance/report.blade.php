@extends('layouts.admin')
@section('page-title')
    {{ __('Employee Report') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item">{{ __('Employee Report') }}</li>
@endsection

@section('content')
    @if(\Auth::user()->type != 'employee')
    <div class="row">
        <div class="col-sm-12">
            <div class="row mb-3">
                <div class="col-sm-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-body py-3">
                            <div class="row align-items-center">
                                <div class="col-md-4 mb-3 mb-md-0">
                                    <h5 class="mb-0 text-primary"><i class="fas fa-user-clock me-2"></i>{{ __('Attendance Report') }}</h5>
                                </div>
                                <div class="col-md-8">
                                     <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-md-end">
                                         <div class="employee-select-wrapper">
                                             {{ Form::select('employee', $employees, null, ['class' => 'form-control select2', 'id' => 'employee_id']) }}
                                         </div>
                                     </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @else
        {{ Form::hidden('employee', !empty(\Auth::user()->employee) ? \Auth::user()->employee->id : 0, ['id' => 'employee_id']) }}
    @endif

    <div class="row justify-content-center mt-3" id="report_container" style="display:none;">
        <div class="col-md-12">
            <div class="card h-90">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 style="font-size:20px;color:black; margin: 0;">{{ __('Attendance Overview') }}</h5>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" id="attendanceFilterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <span id="selectedFilterText">Today</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="attendanceFilterDropdown">
                            <li><a class="dropdown-item attendance-filter-option active" href="javascript:void(0)" data-filter="today">
                                <i class="fas fa-calendar-day me-2"></i>Today
                            </a></li>
                            <li><a class="dropdown-item attendance-filter-option" href="javascript:void(0)" data-filter="date">
                                <i class="fas fa-calendar me-2"></i>Select Date
                            </a></li>
                            <li><a class="dropdown-item attendance-filter-option" href="javascript:void(0)" data-filter="weekly">
                                <i class="fas fa-calendar-week me-2"></i>Weekly
                            </a></li>
                            <li><a class="dropdown-item attendance-filter-option" href="javascript:void(0)" data-filter="monthly">
                                <i class="fas fa-calendar-alt me-2"></i>Monthly
                            </a></li>
                        </ul>
                        <input type="date" id="attendanceDatePicker" style="position: absolute; opacity: 0; width: 0; height: 0; pointer-events: none;" onchange="handleDateSelect(this.value)" oninput="handleDateSelect(this.value)">
                        <input type="month" id="attendanceMonthPicker" style="position: absolute; opacity: 0; width: 0; height: 0; pointer-events: none;" onchange="handleMonthSelect(this.value)" oninput="handleMonthSelect(this.value)">
                    </div>
                </div>
                <div class="card-body">
                    <!-- Loading state -->
                    <div class="text-center py-4" id="attendanceLoading" style="display: none;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                    <div id="attendanceOverviewContent">
                        <!-- Content will be loaded here -->
                    </div>
                    <div id="gpsMessage" class="alert d-none mt-2" role="alert"></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script-page')
<style>
    .employee-select-wrapper {
        width: 100%;
        max-width: 100%;
    }
    @media (min-width: 768px) {
        .employee-select-wrapper {
            max-width: 300px;
        }
    }
</style>
<script>
/* ---------- Enhanced Attendance Overview JS (paste into your Blade) ---------- */

let attendanceWeekOffset = 0; // 0 = week of selected date or current week

    function initializeAttendanceOverview() {
        $('#employee_id').on('change', function() {
            if($(this).val()) {
                $('#report_container').show();
                // trigger load of active filter
                const activeFilter = document.querySelector('.attendance-filter-option.active');
                if(activeFilter) {
                    activeFilter.click();
                } else {
                    loadAttendanceData('today');
                }
            } else {
                $('#report_container').hide();
            }
        });
        // Add event listeners for filter options
    document.querySelectorAll('.attendance-filter-option').forEach(option => {
        option.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const filterType = this.dataset.filter;

            if (filterType === 'date' || filterType === 'monthly') {
                const dropdown = bootstrap.Dropdown.getInstance(document.getElementById('attendanceFilterDropdown'));
                if (dropdown) dropdown.hide();
                setTimeout(() => openPicker(filterType === 'date' ? 'date' : 'month'), 120);
                return false;
            }

            if (filterType === 'weekly') {
                const dropdown = bootstrap.Dropdown.getInstance(document.getElementById('attendanceFilterDropdown'));
                if (dropdown) dropdown.hide();

                const datePicker = document.getElementById('attendanceDatePicker');
                // if selected date exists, use it; else use today
                const refDate = (datePicker && datePicker.value) ? datePicker.value : (new Date()).toISOString().split('T')[0];
                // reset weekOffset to 0 (start from selected reference)
                attendanceWeekOffset = 0;
                setSelectedFilterActive('weekly', 'Week of ' + formatShortDate(refDate));
                loadAttendanceData('weekly', refDate);
                return false;
            }

            // today
            setSelectedFilterActive(filterType);
            loadAttendanceData(filterType);
            return false;
        });
    });

    // pickers hooks
    const datePicker = document.getElementById('attendanceDatePicker');
    if (datePicker) {
        datePicker.addEventListener('change', function() {
            if (this.value) handleDateSelect(this.value);
        });
        datePicker.addEventListener('input', function() {
            if (this.value) handleDateSelect(this.value);
        });
    }
    const monthPicker = document.getElementById('attendanceMonthPicker');
    if (monthPicker) {
        monthPicker.addEventListener('change', function() {
            if (this.value) handleMonthSelect(this.value);
        });
        monthPicker.addEventListener('input', function() {
            if (this.value) handleMonthSelect(this.value);
        });
    }

    // Add prev/next week button listeners (buttons HTML below)
    const prevWeekBtn = document.getElementById('prevWeekBtn');
    const nextWeekBtn = document.getElementById('nextWeekBtn');
    if (prevWeekBtn) {
        prevWeekBtn.addEventListener('click', function() {
            adjustWeekOffset(-1);
        });
    }
    if (nextWeekBtn) {
        nextWeekBtn.addEventListener('click', function() {
            adjustWeekOffset(1);
        });
    }

    loadAttendanceData('today'); // default
}

function setSelectedFilterActive(filterType, labelText = null) {
    document.querySelectorAll('.attendance-filter-option').forEach(o => o.classList.remove('active'));
    const option = document.querySelector(`[data-filter="${filterType}"]`);
    if (option) option.classList.add('active');
    document.getElementById('selectedFilterText').textContent = labelText || (option ? option.textContent.trim() : filterType);
}

function formatShortDate(isoDate /* YYYY-MM-DD */) {
    try {
        const d = new Date(isoDate + 'T00:00:00');
        return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
    } catch (e) {
        return isoDate;
    }
}

function adjustWeekOffset(delta) {
    attendanceWeekOffset += delta; // negative -> previous weeks
    // compute reference date from datePicker value or today
    const datePicker = document.getElementById('attendanceDatePicker');
    let base = (datePicker && datePicker.value) ? new Date(datePicker.value + 'T00:00:00') : new Date();
    // shift by 7 * offset days
    const ref = new Date(base);
    ref.setDate(base.getDate() + (attendanceWeekOffset * 7));
    const isoRef = ref.toISOString().split('T')[0];
    setSelectedFilterActive('weekly', 'Week of ' + formatShortDate(isoRef));
    loadAttendanceData('weekly', isoRef);
}

function openPicker(type) {
    const datePicker = document.getElementById('attendanceDatePicker');
    const monthPicker = document.getElementById('attendanceMonthPicker');
    if (type === 'date' && datePicker) {
        datePicker.style.position = 'fixed'; datePicker.style.opacity = '0'; datePicker.style.pointerEvents = 'auto'; datePicker.style.zIndex = '9999';
        if (datePicker.showPicker && typeof datePicker.showPicker === 'function') {
            const pickerPromise = datePicker.showPicker();
            if (pickerPromise && typeof pickerPromise.catch === 'function') {
                pickerPromise.catch(() => datePicker.click()).finally(() => {
                    datePicker.style.position = 'absolute';
                    datePicker.style.opacity = '0';
                    datePicker.style.pointerEvents = 'none';
                    datePicker.style.zIndex = 'auto';
                });
            } else {
                datePicker.click();
                setTimeout(() => {
                    datePicker.style.position = 'absolute';
                    datePicker.style.opacity = '0';
                    datePicker.style.pointerEvents = 'none';
                    datePicker.style.zIndex = 'auto';
                }, 200);
            }
        } else {
            datePicker.click();
            setTimeout(() => {
                datePicker.style.position = 'absolute';
                datePicker.style.opacity = '0';
                datePicker.style.pointerEvents = 'none';
                datePicker.style.zIndex = 'auto';
            }, 200);
        }
    } else if (type === 'month' && monthPicker) {
        monthPicker.style.position = 'fixed'; monthPicker.style.opacity = '0'; monthPicker.style.pointerEvents = 'auto'; monthPicker.style.zIndex = '9999';
        if (monthPicker.showPicker && typeof monthPicker.showPicker === 'function') {
            const pickerPromise = monthPicker.showPicker();
            if (pickerPromise && typeof pickerPromise.catch === 'function') {
                pickerPromise.catch(() => monthPicker.click()).finally(() => {
                    monthPicker.style.position = 'absolute';
                    monthPicker.style.opacity = '0';
                    monthPicker.style.pointerEvents = 'none';
                    monthPicker.style.zIndex = 'auto';
                });
            } else {
                monthPicker.click();
                setTimeout(() => {
                    monthPicker.style.position = 'absolute';
                    monthPicker.style.opacity = '0';
                    monthPicker.style.pointerEvents = 'none';
                    monthPicker.style.zIndex = 'auto';
                }, 200);
            }
        } else {
            monthPicker.click();
            setTimeout(() => {
                monthPicker.style.position = 'absolute';
                monthPicker.style.opacity = '0';
                monthPicker.style.pointerEvents = 'none';
                monthPicker.style.zIndex = 'auto';
            }, 200);
        }
    }
}

function handleDateSelect(dateValue) {
    if (!dateValue) return;
    // when user picks a date we treat it as date filter (single day)
    setSelectedFilterActive('date', formatShortDate(dateValue));
    // reset week offset so prev/next operate from new base
    attendanceWeekOffset = 0;
    loadAttendanceData('date', dateValue);
}

function handleMonthSelect(monthValue) {
    if (!monthValue) return;
    setSelectedFilterActive('monthly', new Date(monthValue + '-01').toLocaleDateString('en-US',{ month:'long', year:'numeric' }));
    attendanceWeekOffset = 0;
    loadAttendanceData('monthly', monthValue);
}

function loadAttendanceData(filterType, dateValue = null) {
    // Stop real-time updates when loading new data
    stopRealTimeUpdates();
    
    const contentDiv = document.getElementById('attendanceOverviewContent');
    const loadingDiv = document.getElementById('attendanceLoading');
    const punchInOutSection = document.getElementById('punchInOutSection');
    
    // Check if elements exist before accessing
    if (!contentDiv) {
        console.error('[Attendance] attendanceOverviewContent element not found');
        return;
    }
    
    // Hide/show punch in/out section based on filter type
    // Check if selected date is today for date filter
    const datePicker = document.getElementById('attendanceDatePicker');
    const selectedDate = datePicker ? datePicker.value : null;
    const today = new Date().toISOString().split('T')[0];
    const isSelectedDateToday = selectedDate === today;
    
    if (punchInOutSection) {
        if (filterType === 'today' || (filterType === 'date' && isSelectedDateToday)) {
            punchInOutSection.style.display = 'block';
        } else {
            punchInOutSection.style.display = 'none';
        }
    }
    
    // Show loading indicator if it exists
    if (loadingDiv) {
        loadingDiv.style.display = 'block';
    }
    
    // Clear content
    contentDiv.innerHTML = '';

    const url = '{{ route("attendance.overview") }}';
    const payload = {
        _token: '{{ csrf_token() }}',
        filter_type: filterType,
        employee_id: document.getElementById('employee_id').value
    };

    if (filterType === 'weekly') {
        if (dateValue) {
            payload.date = dateValue;
            currentWeekDate = dateValue;
        } else {
            const datePicker = document.getElementById('attendanceDatePicker');
            payload.date = (datePicker && datePicker.value) ? datePicker.value : (new Date()).toISOString().split('T')[0];
            currentWeekDate = payload.date;
        }
    } else if (filterType === 'date' && dateValue) {
        payload.date = dateValue;
    } else if (filterType === 'monthly' && dateValue) {
        payload.month = dateValue; // YYYY-MM
        currentMonth = dateValue;
    }

    console.log('[Attendance] Request payload:', payload);

    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type':'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify(payload)
    })
    .then(r => {
        // Hide loading indicator if it exists
        if (loadingDiv) {
            loadingDiv.style.display = 'none';
        }
        if (!r.ok) throw new Error('Network error');
        return r.json();
    })
    .then(json => {
        console.log('[Attendance] Response:', json);
        if (!json.success) {
            contentDiv.innerHTML = '<div class="alert alert-warning">' + (json.message || 'No data') + '</div>';
            return;
        }

        // If weekly, update label with week range returned by server if available:
        if (filterType === 'weekly' && json.data && json.data.week_start && json.data.week_end) {
            const selectedFilterText = document.getElementById('selectedFilterText');
            if (selectedFilterText) {
                selectedFilterText.textContent = json.data.week_start + ' - ' + json.data.week_end;
            }
        }
        if (filterType === 'monthly' && json.data && json.data.month_name) {
            const selectedFilterText = document.getElementById('selectedFilterText');
            if (selectedFilterText) {
                selectedFilterText.textContent = json.data.month_name;
            }
        }

        renderAttendanceOverview(json.data, filterType);
        
        // Update attendance status text if late punch-in (for today view)
        if (filterType === 'today' && json.data.clock_in && json.data.is_late) {
            const attendanceStatus = document.getElementById('attendanceStatus');
            if (attendanceStatus) {
                const timeString = json.data.clock_in;
                attendanceStatus.innerHTML = '<span class="text-danger"><i class="fas fa-fingerprint"></i> Punched In at ' + timeString + '</span>';
            }
        }
    })
    .catch(err => {
        // Hide loading indicator if it exists
        if (loadingDiv) {
            loadingDiv.style.display = 'none';
        }
        if (contentDiv) {
            contentDiv.innerHTML = '<div class="alert alert-danger">Error loading attendance data.</div>';
        }
        console.error('[Attendance] fetch error:', err);
    });
}

// Global variables for real-time updates
let attendanceUpdateInterval = null;
let currentAttendanceData = null;
let currentFilterType = null;

// Function to stop real-time updates
function stopRealTimeUpdates() {
    if (attendanceUpdateInterval) {
        clearInterval(attendanceUpdateInterval);
        attendanceUpdateInterval = null;
    }
}

// Function to check if viewing current period
function isCurrentPeriod(filterType, data) {
    const today = new Date();
    const todayStr = today.toISOString().split('T')[0];
    
    if (filterType === 'today') {
        return true;
    }
    
    if (filterType === 'date') {
        const datePicker = document.getElementById('attendanceDatePicker');
        const selectedDate = datePicker ? datePicker.value : null;
        return selectedDate === todayStr;
    }
    
    if (filterType === 'weekly') {
        // Check if current week
        const weekStart = data.week_start ? new Date(data.week_start) : null;
        const weekEnd = data.week_end ? new Date(data.week_end) : null;
        if (weekStart && weekEnd) {
            return today >= weekStart && today <= weekEnd;
        }
    }
    
    if (filterType === 'monthly') {
        // Check if current month
        const monthName = data.month_name || '';
        const currentMonthName = today.toLocaleDateString('en-US', { month: 'long', year: 'numeric' });
        return monthName === currentMonthName;
    }
    
    return false;
}

// Function to calculate current hours for today
function calculateCurrentHours(data) {
    if (!data.clock_in || data.clock_in === 'N/A') return data.hours_completed || 0;
    if (data.clock_out && data.clock_out !== 'N/A' && data.clock_out !== '00:00:00') return data.hours_completed || 0;
    
    const serverHours = data.hours_completed || 0;
    const maxHoursForDay = 24;
    
    if (data.clock_in_raw || (data.clock_in && (data.clock_in.includes('AM') || data.clock_in.includes('PM')))) {
        try {
            const today = new Date();
            const todayStr = today.getFullYear() + '-' + String(today.getMonth() + 1).padStart(2, '0') + '-' + String(today.getDate()).padStart(2, '0');
            
            let clockInDateStr = data.date || todayStr;
            let clockInTime;
            
            if (data.clock_in_raw) {
                const timeStr = data.clock_in_raw.length >= 5 ? data.clock_in_raw.substring(0, 5) : data.clock_in_raw;
                const parts = timeStr.split(':');
                const dateParts = clockInDateStr.split('-');
                if (parts.length >= 2 && dateParts.length === 3) {
                    clockInTime = new Date(parseInt(dateParts[0]), parseInt(dateParts[1])-1, parseInt(dateParts[2]), parseInt(parts[0]), parseInt(parts[1]), 0);
                } else {
                    clockInTime = new Date(clockInDateStr + 'T' + timeStr + ':00');
                }
            } else if (data.clock_in.includes('AM') || data.clock_in.includes('PM')) {
                const timeParts = data.clock_in.match(/(\d+):(\d+)\s*(AM|PM)/i);
                if (timeParts) {
                    let hours = parseInt(timeParts[1]);
                    const minutes = parseInt(timeParts[2]);
                    const ampm = timeParts[3].toUpperCase();
                    
                    if (ampm === 'PM' && hours !== 12) hours += 12;
                    if (ampm === 'AM' && hours === 12) hours = 0;
                    
                    const dateParts = clockInDateStr.split('-');
                    if (dateParts.length === 3) {
                        clockInTime = new Date(parseInt(dateParts[0]), parseInt(dateParts[1])-1, parseInt(dateParts[2]), hours, minutes, 0);
                    } else {
                        clockInTime = new Date(clockInDateStr + 'T' + String(hours).padStart(2, '0') + ':' + String(minutes).padStart(2, '0') + ':00');
                    }
                } else {
                    return Math.min(serverHours, maxHoursForDay);
                }
            } else {
                clockInTime = new Date(clockInDateStr + 'T' + data.clock_in);
            }
            
            const now = new Date();
            const diffMs = now - clockInTime;
            const diffHours = diffMs / (1000 * 60 * 60);
            
            if (!isNaN(diffHours) && diffHours >= 0 && diffHours <= maxHoursForDay) {
                return diffHours;
            }
        } catch (e) {
            console.error('Error calculating current hours:', e);
        }
    }
    
    return Math.min(serverHours, maxHoursForDay);
}

// Store the date/month used for weekly/monthly to refresh data
let currentWeekDate = null;
let currentMonth = null;

// Function to update progress bar in real-time (smooth, no interruptions)
function updateProgressBarRealTime() {
    if (!currentAttendanceData || !currentFilterType) return;
    
    const contentDiv = document.getElementById('attendanceOverviewContent');
    if (!contentDiv) return;
    
    let hoursCompleted = 0;
    let totalHours = 0;
    let percentage = 0;
    
    if (currentFilterType === 'today' || currentFilterType === 'date') {
        // For today/date, calculate real-time from clock_in
        hoursCompleted = calculateCurrentHours(currentAttendanceData);
        totalHours = currentAttendanceData.total_hours || 8; // Usually 8 or 4
        percentage = totalHours > 0 ? (hoursCompleted / totalHours * 100) : 0;
    } else if (currentFilterType === 'weekly') {
        // For weekly, use stored data and add today's real-time hours if applicable
        const baseHours = currentAttendanceData.hours_completed || 0;
        totalHours = currentAttendanceData.total_hours || 0;
        
        // If today is in the week and user is clocked in, add real-time hours
        if (currentAttendanceData.clock_in && 
            (!currentAttendanceData.clock_out || currentAttendanceData.clock_out === 'N/A')) {
            // User is clocked in today, calculate real-time hours
            const todayHours = calculateCurrentHours(currentAttendanceData);
            const storedTodayHours = currentAttendanceData.today_hours || 0;
            // Replace today's hours with real-time calculation
            hoursCompleted = baseHours - storedTodayHours + todayHours;
        } else {
            hoursCompleted = baseHours;
        }
        
        percentage = totalHours > 0 ? (hoursCompleted / totalHours * 100) : 0;
    } else if (currentFilterType === 'monthly') {
        // For monthly, similar to weekly
        const baseHours = currentAttendanceData.hours_completed || 0;
        totalHours = currentAttendanceData.total_hours || 0;
        
        // If user is clocked in today, add real-time hours
        if (currentAttendanceData.clock_in && 
            (!currentAttendanceData.clock_out || currentAttendanceData.clock_out === 'N/A')) {
            // User is clocked in today, calculate real-time hours
            const todayHours = calculateCurrentHours(currentAttendanceData);
            const storedTodayHours = currentAttendanceData.today_hours || 0;
            // Replace today's hours with real-time calculation
            hoursCompleted = baseHours - storedTodayHours + todayHours;
        } else {
            hoursCompleted = baseHours;
        }
        
        percentage = totalHours > 0 ? (hoursCompleted / totalHours * 100) : 0;
    }
    
    // Update the progress bar elements smoothly without any loading indicators
    const hoursText = contentDiv.querySelector('.h5.mb-0');
    const badge = contentDiv.querySelector('.badge');
    const progressBar = contentDiv.querySelector('.progress-bar');
    const hoursCompletedLabel = contentDiv.querySelector('h6.text-muted.mb-2');
    
    // Check if late punch-in (from currentAttendanceData)
    const isLate = currentAttendanceData && currentAttendanceData.is_late;
    
    if (hoursText) {
        const h = Math.floor(hoursCompleted);
        const m = Math.round((hoursCompleted - h) * 60);
        hoursText.textContent = `${h} hours ${m} minutes / ${totalHours} hours`;
    }
    
    if (badge) {
        badge.textContent = `${percentage.toFixed(1)}%`;
        // Update badge color if late
        if (isLate) {
            badge.className = 'badge bg-danger';
        }
    }
    
    if (progressBar) {
        // Smooth transition for progress bar width
        progressBar.style.transition = 'width 0.5s ease';
        progressBar.style.width = `${Math.min(percentage, 100)}%`;
        progressBar.setAttribute('aria-valuenow', hoursCompleted);
        progressBar.textContent = `${percentage.toFixed(1)}%`;
        // Update progress bar color if late
        if (isLate) {
            progressBar.className = 'progress-bar bg-danger';
        } else {
            progressBar.className = `progress-bar ${hoursCompleted >= totalHours ? 'bg-primary' : 'bg-primary'}`;
            progressBar.style.backgroundColor = '';
            badge.className = `badge ${hoursCompleted >= totalHours ? 'bg-primary' : 'bg-primary'}`;
            badge.style.backgroundColor = '';
            if (hoursCompleted >= totalHours) badge.style.color = '#fff';
        }
    }
    
    // Update hours completed label to show "(Late Punch-In)" if late
    if (hoursCompletedLabel && isLate && !hoursCompletedLabel.innerHTML.includes('Late Punch-In')) {
        hoursCompletedLabel.innerHTML = hoursCompletedLabel.innerHTML.replace('Hours Completed', 'Hours Completed <span class="text-danger">(Late Punch-In)</span>');
    }
}

        function renderAttendanceOverview(data, filterType) {
            const contentDiv = document.getElementById('attendanceOverviewContent');
            const punchInOutSection = document.getElementById('punchInOutSection');
            let html = '';

            // Check if selected date is today
            const datePicker = document.getElementById('attendanceDatePicker');
            const selectedDate = datePicker ? datePicker.value : null;
            const today = new Date().toISOString().split('T')[0];
            const isSelectedDateToday = selectedDate === today;

            // Hide punch in/out section for:
            // - date filter when selected date is NOT today
            // - weekly view
            // - monthly view
            if (filterType === 'weekly' || filterType === 'monthly' || 
                (filterType === 'date' && !isSelectedDateToday)) {
                if (punchInOutSection) {
                    punchInOutSection.style.display = 'none';
                }
            } else {
                // Show punch in/out section for today view or when selected date is today
                if (punchInOutSection) {
                    punchInOutSection.style.display = 'block';
                }
            }

            // Build detailed log list
            let detailedHtml = '';
            let runningLateCount = 0;
            if (data.records && data.records.length > 0) {
                let recordsList = '';
                data.records.forEach(r => {
                    let outText = r.is_running ? '<span class="badge bg-warning text-dark">Running</span>' : r.clock_out;
                    
                    let isLate = r.late && r.late !== '00:00:00';
                    if (isLate) {
                        runningLateCount++;
                    }
                    
                    let policyHtml = '';
                    if (r.status === 'Half Day' && runningLateCount >= 4) {
                        policyHtml = `<span class="badge bg-warning text-dark ms-2" style="font-size: 0.7rem; font-weight: 600;"><i class="fas fa-exclamation-triangle me-1"></i>Late Policy Applied (${runningLateCount}th Late Mark)</span>`;
                    }
                    recordsList += `
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2 border-bottom" style="border-color: rgba(0,0,0,0.05) !important; background: transparent;">
                            <div class="d-flex align-items-center flex-wrap gap-1">
                                <span class="badge bg-light text-dark border me-2" style="font-size: 0.85rem; min-width: 90px; text-align: center;">${r.date.split(',')[0]}</span>
                                <span class="text-secondary small font-monospace">${r.clock_in}</span>
                                <span class="mx-2 text-muted">&rarr;</span>
                                <span class="text-secondary small font-monospace">${outText}</span>
                                ${policyHtml}
                            </div>
                            <span class="fw-bold text-dark small font-monospace">${r.duration_formatted}</span>
                        </li>
                    `;
                });
                
                detailedHtml = `
                    <div class="mt-4 border-top pt-3">
                        <h6 class="text-primary mb-3 d-flex align-items-center" style="font-size: 1rem; font-weight: 600;">
                            <i class="fas fa-list-alt me-2"></i>Detailed Work Log
                        </h6>
                        <ul class="list-group list-group-flush mb-3">
                            ${recordsList}
                        </ul>
                        <div class="bg-light p-3 rounded border">
                            <small class="text-muted d-block mb-1 font-semibold text-uppercase" style="letter-spacing: 0.5px; font-size: 0.75rem;">Total Working Time Breakdown</small>
                            <div class="text-dark font-monospace small d-flex align-items-center" style="word-break: break-all;">
                                <i class="fas fa-calculator text-primary me-2"></i>
                                <span>${data.summation_expression}</span>
                                <i class="fas fa-check-circle text-success ms-2" style="font-size: 1.1rem;"></i>
                            </div>
                        </div>

                    </div>
                `;
            }

            // Build Overtime & Remaining time section
            let remainingText = '';
            if (data.remaining_seconds > 0) {
                remainingText = `<span class="text-warning fw-bold"><i class="fas fa-hourglass-half me-1"></i>${data.remaining_formatted}</span>`;
            } else {
                remainingText = `<span class="text-success fw-bold"><i class="fas fa-check-circle me-1"></i>Completed!</span>`;
            }
            
            let overtimeText = '';
            if (data.overtime_seconds > 0) {
                overtimeText = `<span class="text-success fw-bold"><i class="fas fa-arrow-circle-up me-1"></i>${data.overtime_formatted}</span>`;
            } else {
                overtimeText = `<span class="text-muted">${data.overtime_formatted}</span>`;
            }
            
            let summaryGridHtml = `
                <div class="row mt-3 border-top pt-3">
                    <div class="col-6 mb-3">
                        <small class="text-muted d-block mb-1 text-uppercase" style="letter-spacing: 0.5px; font-size: 0.75rem; font-weight: 600;">Time Remaining</small>
                        <div style="font-size: 0.9rem;">${remainingText}</div>
                    </div>
                    <div class="col-6 mb-3">
                        <small class="text-muted d-block mb-1 text-uppercase" style="letter-spacing: 0.5px; font-size: 0.75rem; font-weight: 600;">${data.overtime_label || 'Overtime'}</small>
                        <div style="font-size: 0.9rem;">${overtimeText}</div>
                    </div>
                </div>
            `;



            if (filterType === 'today' || filterType === 'date') {
                // Check if late punch-in
                const isLate = data.is_late || false;
                const totalHours = data.total_hours || 8;
                const hoursCompleted = data.hours_completed || 0;
                const percentage = totalHours > 0 ? (hoursCompleted / totalHours * 100) : 0;
                
                const progressBarClass = isLate ? 'bg-danger' : 'bg-primary';
                const badgeClass = isLate ? 'bg-danger' : 'bg-primary';
                const lateText = isLate ? ' <span class="text-danger">(Late Punch-In)</span>' : '';
                
                if (filterType === 'date' && !isSelectedDateToday) {
                    // For selected date (not today), show punch in and punch out times with progress bar
                    html = `
                        <div class="attendance-detail">
                            <div class="mb-3">
                                <h6 class="text-muted mb-2">Hours Completed${lateText}</h6>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="h5 mb-0">${Math.floor(hoursCompleted)} hours ${Math.round((hoursCompleted - Math.floor(hoursCompleted)) * 60)} minutes / ${totalHours} hours</span>
                                    <span class="badge ${badgeClass}">${percentage.toFixed(1)}%</span>
                                </div>
                                <div class="progress" style="height: 30px;">
                                    <div class="progress-bar ${progressBarClass}" 
                                         role="progressbar" 
                                         style="width: ${Math.min(percentage, 100)}%;" 
                                         aria-valuenow="${hoursCompleted}" 
                                         aria-valuemin="0" 
                                         aria-valuemax="${totalHours}">
                                        ${percentage.toFixed(1)}%
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-6 mb-3">
                                    <small class="text-muted d-block">Punch In Time</small>
                                    <p class="small mb-0 ${isLate ? 'text-danger' : ''}" style="font-size: 0.875rem;">${data.clock_in || 'N/A'}</p>
                                </div>
                                <div class="col-6 mb-3">
                                    <small class="text-muted d-block">Punch Out Time</small>
                                    <p class="small mb-0" style="font-size: 0.875rem;">${data.clock_out || 'N/A'}</p>
                                </div>
                            </div>
                            ${summaryGridHtml}
                            ${detailedHtml}
                        </div>
                    `;
                } else {
                    // Today view - show hours completed with progress (buttons shown above)
                    html = `
                        <div class="attendance-detail">
                            <div class="mb-2">
                                <h6 class="text-muted mb-2">Hours Completed${lateText}</h6>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="h5 mb-0">${Math.floor(hoursCompleted)} hours ${Math.round((hoursCompleted - Math.floor(hoursCompleted)) * 60)} minutes / ${totalHours} hours</span>
                                    <span class="badge ${badgeClass}">${percentage.toFixed(1)}%</span>
                                </div>
                                <div class="progress" style="height: 30px;">
                                    <div class="progress-bar ${progressBarClass}" 
                                         role="progressbar" 
                                         style="width: ${Math.min(percentage, 100)}%;" 
                                         aria-valuenow="${hoursCompleted}" 
                                         aria-valuemin="0" 
                                         aria-valuemax="${totalHours}">
                                        ${percentage.toFixed(1)}%
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-6 mb-3">
                                    <small class="text-muted d-block">Punch In Time</small>
                                    <p class="small mb-0 ${isLate ? 'text-danger' : ''}" style="font-size: 0.875rem;">${data.clock_in || 'N/A'}</p>
                                </div>
                                <div class="col-6 mb-3">
                                    <small class="text-muted d-block">Punch Out Time</small>
                                    <p class="small mb-0" style="font-size: 0.875rem;">${data.clock_out || 'N/A'}</p>
                                </div>
                            </div>
                            ${summaryGridHtml}
                            ${detailedHtml}
                        </div>
                    `;
                }
            } else if (filterType === 'weekly') {
                // Weekly view - show hours completed with progress bar, Days Worked and Week Period
                const hoursCompleted = data.hours_completed || 0;
                const totalHours = data.total_hours || 0;
                const percentage = data.percentage || 0;
                
                const h = Math.floor(hoursCompleted);
                const m = Math.round((hoursCompleted - h) * 60);
                const hoursText = `${h} hours ${m} minutes / ${totalHours} hours`;
                
                html = `
                    <div class="attendance-detail">
                        <div class="mb-3">
                            <h6 class="text-muted mb-2">Hours Completed</h6>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="h5 mb-0">${hoursText}</span>
                                <span class="badge bg-primary">${percentage.toFixed(1)}%</span>
                            </div>
                            <div class="progress" style="height: 30px;">
                                <div class="progress-bar bg-primary" 
                                     role="progressbar" 
                                     style="width: ${Math.min(percentage, 100)}%;" 
                                     aria-valuenow="${hoursCompleted}" 
                                     aria-valuemin="0" 
                                     aria-valuemax="${totalHours}">
                                    ${percentage.toFixed(1)}%
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-6 mb-3">
                                <small class="text-muted d-block">Days Worked</small>
                                <p class="small mb-0" style="font-size: 0.875rem;">${data.days_worked || 0} days</p>
                            </div>
                            <div class="col-6 mb-3">
                                <small class="text-muted d-block">Week Period</small>
                                <p class="small mb-0" style="font-size: 0.875rem;">${data.week_start || ''} - ${data.week_end || ''}</p>
                            </div>
                        </div>
                        ${summaryGridHtml}
                        ${detailedHtml}
                        <!-- Week Navigation -->
                        <div class="d-flex justify-content-center gap-2 mt-3">
                            <button type="button" class="btn btn-sm btn-outline-primary" id="prevWeekBtn">
                                <i class="fas fa-chevron-left"></i> Previous Week
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-primary" id="nextWeekBtn">
                                Next Week <i class="fas fa-chevron-right"></i>
                            </button>
                        </div>
                    </div>
                `;
                
                // Re-attach week navigation listeners after rendering
                setTimeout(() => {
                    const prevWeekBtn = document.getElementById('prevWeekBtn');
                    const nextWeekBtn = document.getElementById('nextWeekBtn');
                    if (prevWeekBtn) {
                        prevWeekBtn.addEventListener('click', function() {
                            adjustWeekOffset(-1);
                        });
                    }
                    if (nextWeekBtn) {
                        nextWeekBtn.addEventListener('click', function() {
                            adjustWeekOffset(1);
                        });
                    }
                }, 100);
            } else if (filterType === 'monthly') {
                // Monthly view - show hours completed with progress bar, Days Worked and Month Name
                const hoursCompleted = data.hours_completed || 0;
                const totalHours = data.total_hours || 0;
                const percentage = data.percentage || 0;
                
                const h = Math.floor(hoursCompleted);
                const m = Math.round((hoursCompleted - h) * 60);
                const hoursText = `${h} hours ${m} minutes / ${totalHours} hours`;
                
                html = `
                    <div class="attendance-detail">
                        <div class="mb-3">
                            <h6 class="text-muted mb-2">Hours Completed</h6>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="h5 mb-0">${hoursText}</span>
                                <span class="badge bg-primary">${percentage.toFixed(1)}%</span>
                            </div>
                            <div class="progress" style="height: 30px;">
                                <div class="progress-bar bg-primary" 
                                     role="progressbar" 
                                     style="width: ${Math.min(percentage, 100)}%;" 
                                     aria-valuenow="${hoursCompleted}" 
                                     aria-valuemin="0" 
                                     aria-valuemax="${totalHours}">
                                    ${percentage.toFixed(1)}%
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-6 mb-3">
                                <small class="text-muted d-block">Days Worked</small>
                                <p class="small mb-0" style="font-size: 0.875rem;">${data.days_worked || 0} days</p>
                            </div>
                            <div class="col-6 mb-3">
                                <small class="text-muted d-block">Month Name</small>
                                <p class="small mb-0" style="font-size: 0.875rem;">${data.month_name || ''}</p>
                            </div>
                        </div>
                        ${summaryGridHtml}
                        ${detailedHtml}
                    </div>
                `;
            }

            contentDiv.innerHTML = html;
            
            // Store current data for real-time updates
            currentAttendanceData = data;
            currentFilterType = filterType;
            
            // Stop any existing interval
            stopRealTimeUpdates();
            
            // Start real-time updates if viewing current period
            if (isCurrentPeriod(filterType, data)) {
                // Update immediately
                updateProgressBarRealTime();
                
                // Update every 5 seconds for smooth continuous progress
                // This ensures the progress bar updates continuously without interruptions
                attendanceUpdateInterval = setInterval(() => {
                    // Always update the progress bar smoothly without reloading
                    // This runs every 5 seconds for smooth continuous progress
                    updateProgressBarRealTime();
                }, 5000); // Update every 5 seconds for smooth continuous progress
                
                // For weekly/monthly, also refresh data from server every 2 minutes in background
                // but don't show loading - just update the stored data silently
                if (filterType === 'weekly' || filterType === 'monthly') {
                    setInterval(() => {
                        const url = '{{ route("attendance.overview") }}';
                        const payload = {
                            _token: '{{ csrf_token() }}',
                            filter_type: filterType,
                            employee_id: document.getElementById('employee_id').value
                        };
                        
                        if (filterType === 'weekly' && currentWeekDate) {
                            payload.date = currentWeekDate;
                        } else if (filterType === 'monthly' && currentMonth) {
                            payload.month = currentMonth;
                        }
                        
                        fetch(url, {
                            method: 'POST',
                            headers: {
                                'Content-Type':'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify(payload)
                        })
                        .then(r => r.json())
                        .then(json => {
                            if (json.success && json.data) {
                                // Update stored data silently without reloading UI
                                currentAttendanceData = json.data;
                                // Progress bar will update on next interval automatically
                            }
                        })
                        .catch(err => {
                            console.error('Background refresh error:', err);
                        });
                    }, 120000); // Refresh every 2 minutes in background
                }
            }
        }
        
        // initialize attendance overview when dom is ready
        $(document).ready(function() {
            initializeAttendanceOverview();
            if ($('#employee_id').val()) {
                $('#employee_id').trigger('change');
            }
        });
    
</script>
<style>
    .attendance-filter-option:hover {
        background-color: var(--bs-primary);
        color: white !important;
    }
    .attendance-filter-option:hover i {
        color: white !important;
    }
    .attendance-filter-option.active {
        background-color: var(--bs-primary) !important;
        color: white !important;
    }
    .attendance-filter-option.active i {
        color: white !important;
    }
</style>
@endpush

