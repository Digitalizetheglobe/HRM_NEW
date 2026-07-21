@extends('layouts.app')

@section('content')
<!-- Page Header -->
<div class="page-header">
    <div>
        <div class="breadcrumb">
            <a href="{{ route('dashboard') }}">Home</a>
            <span class="breadcrumb-sep"><i class="fa-solid fa-chevron-right" style="font-size:9px;"></i></span>
            <span id="breadcrumbCurrent">Employee</span>
        </div>
        <div class="page-title" id="pageTitle">Manage Employee</div>
    </div>
    <div class="page-actions" id="pageActions">
        <div class="emp-view-toggle">
            <a href="javascript:void(0)" class="emp-view-tab active" id="tabListViewBtn" onclick="toggleMainView('list')">
                <i class="fa-solid fa-list" style="font-size:12px;"></i> List View
            </a>
            <a href="javascript:void(0)" class="emp-view-tab" id="tabGridViewBtn" onclick="toggleMainView('grid')">
                <i class="fa-solid fa-grip" style="font-size:12px;"></i> Grid View
            </a>
            <a href="{{ route('employees.create') }}" class="emp-view-tab">
                <i class="fa-solid fa-plus" style="font-size:12px;"></i> Add Employee
            </a>
        </div>
        <button class="btn-outline-sm" onclick="window.print()"><i class="fa-solid fa-download"></i> Export</button>
    </div>
</div>

<!-- Main Content Card -->
<div class="content content-single" style="padding-top: 20px;">
    @if(session('success'))
        <div style="background: #f0fdf4; border: 1.5px solid #86efac; border-radius: 12px; padding: 14px 18px; margin-bottom: 20px; color: var(--accent-green); font-size: 13.5px; font-weight: 600; display: flex; align-items: center; gap: 8px;">
            <i class="fa-solid fa-circle-check"></i>
            {{ session('success') }}
        </div>
    @endif

    <!-- Tabs -->
    <div class="emp-tabs">
        <div class="emp-tab active" onclick="switchTab('active', this)" id="tabActiveBtn">
            <i class="fa-solid fa-circle-check" style="font-size:12px;"></i>
            Active Employees
            <span class="emp-tab-count" id="activeCountBadge">{{ $activeCount }}</span>
        </div>
        <div class="emp-tab" onclick="switchTab('inactive', this)" id="tabInactiveBtn">
            <i class="fa-solid fa-circle-xmark" style="font-size:12px;"></i>
            Inactive Employees
            <span class="emp-tab-count" id="inactiveCountBadge">{{ $inactiveCount }}</span>
        </div>
    </div>

    <!-- Controls -->
    <div class="table-controls" style="background: var(--card-bg); border-left: 1px solid var(--border); border-right: 1px solid var(--border);">
        <div class="per-page-wrap">
            Show
            <select class="per-page-select" id="perPageSelect" onchange="updatePerPage()">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
            </select>
            entries
        </div>
        <div class="search-wrap">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" id="searchInput" placeholder="Search employees..." oninput="handleSearch()"/>
        </div>
    </div>

    <!-- Table Container -->
    <div id="tableCardContainer">
        <div class="table-card" style="border-top-left-radius: 0; border-top-right-radius: 0;">
            <div class="table-scroll">
                <table class="emp-table" id="employeesTable">
                    <thead>
                        <tr>
                            <th class="sortable" onclick="sortTable(0)">Employee ID <i class="fa-solid fa-sort"></i></th>
                            <th class="sortable" onclick="sortTable(1)">Name <i class="fa-solid fa-sort"></i></th>
                            <th class="sortable" onclick="sortTable(2)">Email <i class="fa-solid fa-sort"></i></th>
                            <th class="sortable" onclick="sortTable(3)">Branch <i class="fa-solid fa-sort"></i></th>
                            <th class="sortable" onclick="sortTable(4)">Department <i class="fa-solid fa-sort"></i></th>
                            <th class="sortable" onclick="sortTable(5)">Designation <i class="fa-solid fa-sort"></i></th>
                            <th class="sortable" onclick="sortTable(6)">Date of Joining <i class="fa-solid fa-sort"></i></th>
                            <th style="text-align: right;">Action</th>
                        </tr>
                    </thead>
                    <tbody id="employeesTbody">
                        @forelse($employees as $user)
                            @php
                                $emp = $user->employee;
                                $branchName = $emp && $emp->branch ? $emp->branch->name : '—';
                                $deptName = $emp && $emp->department ? $emp->department->name : '—';
                                $desigName = $emp && $emp->designation ? $emp->designation->name : '—';
                                $joiningDate = $emp && $emp->joining_date ? $emp->joining_date->format('M d, Y') : '—';
                                $uid = $emp ? $emp->employee_uid : '#DTG' . str_pad($user->id, 3, '0', STR_PAD_LEFT);
                            @endphp
                            <tr class="emp-row" data-status="{{ $user->is_active ? 'active' : 'inactive' }}" data-search="{{ strtolower($user->name . ' ' . $user->email . ' ' . $branchName . ' ' . $deptName . ' ' . $desigName . ' ' . $uid) }}">
                                <td><span class="emp-uid">{{ $uid }}</span></td>
                                <td>
                                    <div class="emp-name-cell">
                                        <div class="emp-avatar-sm" style="background: linear-gradient(135deg, var(--primary), var(--accent-purple));">
                                            @if($user->avatar)
                                                <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}">
                                            @else
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            @endif
                                        </div>
                                        <div>
                                            <div class="emp-full-name">{{ $user->name }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td style="color: var(--text-muted);">{{ $user->email }}</td>
                                <td>{{ $branchName }}</td>
                                <td><span class="dept-badge">{{ $deptName }}</span></td>
                                <td>{{ $desigName }}</td>
                                <td style="font-family: 'DM Mono', monospace; font-size: 12px;">{{ $joiningDate }}</td>
                                <td>
                                    <div class="act-btns" style="justify-content: flex-end;">
                                        <a href="{{ route('employees.show', $user->id) }}" class="act-btn" style="background: var(--primary-bg); color: var(--primary); border: 1.5px solid var(--primary-border);" title="View Details">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>
                                        <a href="{{ route('employees.edit', $user->id) }}" class="act-btn act-edit" title="Edit">
                                            <i class="fa-solid fa-pencil"></i>
                                        </a>
                                        <button type="button" class="act-btn act-delete" title="Delete" onclick="openDeleteModal('{{ $user->id }}', '{{ addslashes($user->name) }}')">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr id="noEmployeesRow">
                                <td colspan="8" style="text-align: center; padding: 40px; color: var(--text-muted);">
                                    <i class="fa-solid fa-users-slash" style="font-size: 28px; display: block; margin-bottom: 10px;"></i>
                                    No employees found.
                                </td>
                            </tr>
                        @endforelse
                        
                        <!-- Dynamic Empty row -->
                        <tr id="dynamicEmptyRow" style="display: none;">
                            <td colspan="8" style="text-align: center; padding: 40px; color: var(--text-muted);">
                                <i class="fa-solid fa-users-slash" style="font-size: 28px; display: block; margin-bottom: 10px;"></i>
                                No matching employees found.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="emp-pagination">
                <span id="paginationInfo">Showing 1 to 10 of 10 entries</span>
                <div class="page-btns" id="paginationBtns">
                    <!-- Javascript managed -->
                </div>
            </div>
        </div>
    </div>

    <!-- Grid View Card Panel -->
    <div class="grid-card-wrap" id="gridCardWrap" style="display: none; margin-top: 20px;">
        <div class="emp-grid">
            @foreach($employees as $user)
                @php
                    $emp = $user->employee;
                    $branchName = $emp && $emp->branch ? $emp->branch->name : '—';
                    $deptName = $emp && $emp->department ? $emp->department->name : '—';
                    $desigName = $emp && $emp->designation ? $emp->designation->name : '—';
                    $joiningDate = $emp && $emp->joining_date ? $emp->joining_date->format('M d, Y') : '—';
                    $uid = $emp ? $emp->employee_uid : '#DTG' . str_pad($user->id, 3, '0', STR_PAD_LEFT);
                    
                    $gender = $emp ? $emp->gender : 'male';
                    $avatarBg = $gender === 'female' 
                        ? 'linear-gradient(135deg, #ec4899, #f43f5e)' 
                        : 'linear-gradient(135deg, #6366f1, #8b5cf6)';
                @endphp
                <div class="emp-card" data-status="{{ $user->is_active ? 'active' : 'inactive' }}" data-search="{{ strtolower($user->name . ' ' . $user->email . ' ' . $branchName . ' ' . $deptName . ' ' . $desigName . ' ' . $uid) }}">
                    <div class="emp-card-inner">
                        <div class="emp-card-badge {{ $user->is_active ? 'active' : 'inactive' }}">
                            {{ $user->is_active ? 'Active' : 'Inactive' }}
                        </div>
                        
                        <div class="emp-card-avatar" style="background: {{ $avatarBg }};">
                            @if($user->avatar)
                                <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">
                            @else
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            @endif
                        </div>
                        
                        <div class="emp-card-name">{{ $user->name }}</div>
                        <div class="emp-card-uid">{{ $uid }}</div>
                        
                        <div class="emp-card-divider"></div>
                        <div class="emp-card-info-row">
                            <span class="info-label"><i class="fa-solid fa-building"></i> Branch</span>
                            <span class="info-value">{{ $branchName }}</span>
                        </div>
                        <div class="emp-card-info-row">
                            <span class="info-label"><i class="fa-solid fa-briefcase"></i> Designation</span>
                            <span class="info-value">{{ $desigName }}</span>
                        </div>
                        <div class="emp-card-info-row">
                            <span class="info-label"><i class="fa-solid fa-sitemap"></i> Department</span>
                            <span class="info-value">{{ $deptName }}</span>
                        </div>
                        <div class="emp-card-info-row">
                            <span class="info-label"><i class="fa-regular fa-calendar-check"></i> Joined</span>
                            <span class="info-value">{{ $joiningDate }}</span>
                        </div>
                        
                        <div class="emp-card-actions">
                            <a href="{{ route('employees.show', $user->id) }}" class="card-act-btn card-act-view" title="View Profile">
                                <i class="fa-solid fa-eye"></i> View
                            </a>
                            <a href="{{ route('employees.edit', $user->id) }}" class="card-act-btn card-act-edit" title="Edit">
                                <i class="fa-solid fa-pencil"></i> Edit
                            </a>
                            <button type="button" class="card-act-btn card-act-delete" title="Delete" onclick="openDeleteModal('{{ $user->id }}', '{{ addslashes($user->name) }}')">
                                <i class="fa-solid fa-trash"></i> Delete
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        <!-- Empty Grid State -->
        <div id="emptyGridState" class="empty-grid-state" style="display: none; margin-top: 20px;">
            <i class="fa-solid fa-users-slash" style="font-size: 32px; color: var(--text-muted); display: block; margin-bottom: 12px;"></i>
            <span style="color: var(--text-muted); font-size: 14px; font-weight: 600;">No employees found matching the filters.</span>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="emp-modal-overlay" id="deleteConfirmModal">
    <div class="emp-modal">
        <div class="emp-modal-icon"><i class="fa-solid fa-triangle-exclamation"></i></div>
        <div class="emp-modal-title">Delete Employee?</div>
        <div class="emp-modal-body">
            Are you sure you want to delete <strong id="deleteEmpName">this employee</strong>? This action cannot be undone and all associated data will be permanently removed.
        </div>
        <div class="emp-modal-actions">
            <button type="button" class="wbtn wbtn-outline" onclick="closeDeleteModal()">
                <i class="fa-solid fa-xmark"></i> Cancel
            </button>
            <form id="deleteForm" method="POST" action="">
                @csrf
                @method('DELETE')
                <button type="submit" class="wbtn" style="background: #ef4444; color: #fff;">
                    <i class="fa-solid fa-trash"></i> Yes, Delete
                </button>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let currentTab = 'active';
    let searchQuery = '';
    let perPage = 10;
    let currentPage = 1;
    let currentView = 'list'; // 'list' or 'grid'

    document.addEventListener('DOMContentLoaded', () => {
        // Initial render
        renderList();
    });

    function toggleMainView(view) {
        currentView = view;
        
        // Toggle tab highlights
        document.getElementById('tabListViewBtn').classList.toggle('active', view === 'list');
        document.getElementById('tabGridViewBtn').classList.toggle('active', view === 'grid');

        // Toggle layout displays
        document.getElementById('tableCardContainer').style.display = view === 'list' ? '' : 'none';
        document.getElementById('gridCardWrap').style.display = view === 'grid' ? '' : 'none';
        
        renderList();
    }

    function switchTab(status, element) {
        currentTab = status;
        
        // Update tab styling
        document.getElementById('tabActiveBtn').classList.toggle('active', status === 'active');
        document.getElementById('tabInactiveBtn').classList.toggle('active', status === 'inactive');

        currentPage = 1;
        renderList();
    }

    function handleSearch() {
        searchQuery = document.getElementById('searchInput').value.trim().toLowerCase();
        currentPage = 1;
        renderList();
    }

    function updatePerPage() {
        perPage = parseInt(document.getElementById('perPageSelect').value) || 10;
        currentPage = 1;
        renderList();
    }

    function renderList() {
        const rows = Array.from(document.querySelectorAll('.emp-row'));
        const cards = Array.from(document.querySelectorAll('.emp-card'));

        // Hide all rows & cards initially
        rows.forEach(r => r.style.display = 'none');
        cards.forEach(c => c.style.display = 'none');

        // Filter rows by tab & search query
        let filteredRows = rows.filter(row => {
            const matchesTab = row.getAttribute('data-status') === currentTab;
            const matchesSearch = searchQuery === '' || row.getAttribute('data-search').includes(searchQuery);
            return matchesTab && matchesSearch;
        });

        // Filter cards by tab & search query
        let filteredCards = cards.filter(card => {
            const matchesTab = card.getAttribute('data-status') === currentTab;
            const matchesSearch = searchQuery === '' || card.getAttribute('data-search').includes(searchQuery);
            return matchesTab && matchesSearch;
        });

        const totalItems = filteredRows.length;
        const totalPages = Math.ceil(totalItems / perPage) || 1;
        if (currentPage > totalPages) currentPage = totalPages;

        const startIndex = (currentPage - 1) * perPage;
        const endIndex = Math.min(startIndex + perPage, totalItems);

        // Show page items based on active view mode
        if (currentView === 'list') {
            const pageRows = filteredRows.slice(startIndex, endIndex);
            pageRows.forEach(r => r.style.display = '');
        } else {
            const pageCards = filteredCards.slice(startIndex, endIndex);
            pageCards.forEach(c => c.style.display = '');
        }

        // Show/hide empty states
        const emptyRow = document.getElementById('dynamicEmptyRow');
        const defaultEmpty = document.getElementById('noEmployeesRow');
        const emptyGridState = document.getElementById('emptyGridState');
        
        if (defaultEmpty) defaultEmpty.style.display = 'none';

        if (totalItems === 0) {
            emptyRow.style.display = currentView === 'list' ? '' : 'none';
            if (emptyGridState) emptyGridState.style.display = currentView === 'grid' ? '' : 'none';
        } else {
            emptyRow.style.display = 'none';
            if (emptyGridState) emptyGridState.style.display = 'none';
        }

        // Update info text
        const infoSpan = document.getElementById('paginationInfo');
        if (totalItems === 0) {
            infoSpan.textContent = 'Showing 0 to 0 of 0 entries';
        } else {
            infoSpan.textContent = `Showing ${startIndex + 1} to ${endIndex} of ${totalItems} entries`;
        }

        // Render pagination buttons
        renderPaginationButtons(totalPages);
    }

    function renderPaginationButtons(totalPages) {
        const container = document.getElementById('paginationBtns');
        container.innerHTML = '';

        if (totalPages <= 1) return;

        // Prev Button
        const prev = document.createElement('div');
        prev.className = 'page-btn';
        prev.innerHTML = '<i class="fa-solid fa-chevron-left" style="font-size:10px;"></i>';
        if (currentPage > 1) {
            prev.onclick = () => { currentPage--; renderList(); };
        } else {
            prev.style.opacity = '0.5';
            prev.style.cursor = 'not-allowed';
        }
        container.appendChild(prev);

        // Page Numbers
        for (let i = 1; i <= totalPages; i++) {
            const btn = document.createElement('div');
            btn.className = `page-btn ${currentPage === i ? 'active' : ''}`;
            btn.textContent = i;
            btn.onclick = () => { currentPage = i; renderList(); };
            container.appendChild(btn);
        }

        // Next Button
        const next = document.createElement('div');
        next.className = 'page-btn';
        next.innerHTML = '<i class="fa-solid fa-chevron-right" style="font-size:10px;"></i>';
        if (currentPage < totalPages) {
            next.onclick = () => { currentPage++; renderList(); };
        } else {
            next.style.opacity = '0.5';
            next.style.cursor = 'not-allowed';
        }
        container.appendChild(next);
    }

    // Sort table rows (only affects List View)
    let sortDirections = {};
    function sortTable(colIndex) {
        const tbody = document.getElementById('employeesTbody');
        const rows = Array.from(tbody.querySelectorAll('.emp-row'));
        
        const isAsc = !sortDirections[colIndex];
        sortDirections[colIndex] = isAsc;

        rows.sort((a, b) => {
            let aVal = a.cells[colIndex].innerText.trim().toLowerCase();
            let bVal = b.cells[colIndex].innerText.trim().toLowerCase();
            return isAsc ? aVal.localeCompare(bVal) : bVal.localeCompare(aVal);
        });

        rows.forEach(r => tbody.appendChild(r));
        renderList();
    }

    // Modal helpers
    function openDeleteModal(userId, userName) {
        const form = document.getElementById('deleteForm');
        form.action = `/employees/${userId}`;
        document.getElementById('deleteEmpName').textContent = userName;
        document.getElementById('deleteConfirmModal').classList.add('show');
    }

    function closeDeleteModal() {
        document.getElementById('deleteConfirmModal').classList.remove('show');
    }
</script>
@endpush
@endsection
