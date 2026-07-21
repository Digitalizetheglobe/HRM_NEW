@extends('layouts.app')

@section('content')
<!-- PAGE TITLE -->
<div class="module-header">
    <div class="module-title-wrap">
        <div class="module-breadcrumbs">
            <a href="{{ route('settings.index') }}">
                <i class="fa-solid fa-arrow-left"></i> Settings
            </a>
            <span>/</span>
            <span>Designations</span>
        </div>
        <div class="module-title">Manage Designations</div>
    </div>
    <div>
        <a href="{{ route('designations.create') }}" class="btn-module-submit">
            <i class="fa-solid fa-plus"></i> Create Designation
        </a>
    </div>
</div>

<!-- CONTENT -->
<div class="content" style="grid-template-columns: 1fr;">
    <div class="col-main">

        <!-- SUCCESS ALERT -->
        @if(session('success'))
            <div style="padding:14px 20px; background:rgba(22,163,74,0.12); border:1px solid rgba(22,163,74,0.25); border-radius:12px; color:var(--accent-green); font-size:13.5px; font-weight:600; margin-bottom:20px; display:flex; align-items:center; gap:9px;">
                <i class="fa-solid fa-circle-check"></i>
                {{ session('success') }}
            </div>
        @endif

        <!-- DESIGNATIONS TABLE -->
        <div class="card">
            <div class="card-header">
                <i class="fa-solid fa-user-tag" style="color:var(--accent-purple); font-size:15px;"></i>
                <span class="card-title">Registered Designations</span>
            </div>
            
            <div class="table-scroll">
                <table>
                    <thead>
                        <tr>
                            <th>Designation Role</th>
                            <th>Parent Department</th>
                            <th>Branch Location</th>
                            <th>Date Created</th>
                            <th style="text-align:right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($designations as $designation)
                            <tr>
                                <td>
                                    <div style="font-weight:700; color:var(--text-primary);">{{ $designation->name }}</div>
                                </td>
                                <td>
                                    <span style="font-size:13px; color:var(--text-primary); font-weight:600; display:inline-flex; align-items:center; gap:6px;">
                                        <i class="fa-solid fa-sitemap" style="color:var(--text-muted); font-size:12px;"></i>
                                        {{ $designation->department->name }}
                                    </span>
                                </td>
                                <td>
                                    <span style="font-size:13px; color:var(--text-muted); display:inline-flex; align-items:center; gap:6px;">
                                        <i class="fa-solid fa-city" style="color:var(--text-muted); font-size:12px;"></i>
                                        {{ $designation->department->branch->name }}
                                    </span>
                                </td>
                                <td>
                                    <span style="font-size:13px; color:var(--text-muted);">{{ $designation->created_at->format('M j, Y') }}</span>
                                </td>
                                <td style="text-align:right;">
                                    <div style="display:inline-flex; gap:8px; align-items:center;">
                                        <a href="{{ route('designations.edit', $designation->id) }}" style="width:32px; height:32px; border-radius:8px; background:rgba(255,255,255,0.02); border:1px solid var(--border); display:flex; align-items:center; justify-content:center; color:var(--text-primary); text-decoration:none; transition:var(--transition);">
                                            <i class="fa-regular fa-pen-to-square" style="font-size:13px;"></i>
                                        </a>
                                        
                                        <form action="{{ route('designations.destroy', $designation->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this designation?')" style="margin:0;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" style="width:32px; height:32px; border-radius:8px; background:rgba(239,68,68,0.08); border:1px solid rgba(239,68,68,0.2); display:flex; align-items:center; justify-content:center; color:#f87171; cursor:pointer; transition:var(--transition);">
                                                <i class="fa-regular fa-trash-can" style="font-size:13px;"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="text-align:center; padding:40px; color:var(--text-muted); font-size:14px;">
                                    <i class="fa-regular fa-folder-open" style="font-size:28px; display:block; margin-bottom:12px; opacity:0.5;"></i>
                                    No designations created yet. Click "Create Designation" to get started!
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
@endsection
