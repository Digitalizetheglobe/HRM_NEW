@extends('layouts.app')

@section('content')
<!-- PAGE TITLE -->
<div style="padding:20px 28px 0; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:8px;">
    <div>
        <div style="font-size:20px; font-weight:800; color:var(--text-primary);">System Settings</div>
        <div style="font-size:12.5px; color:var(--text-muted); margin-top:2px;">Manage branches, departments, and job designations.</div>
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

        <!-- MODULE CARDS GRID -->
        <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap:24px; margin-bottom:30px;">
            
            <!-- BRANCHES CARD -->
            <div class="card" style="padding:28px 24px; display:flex; flex-direction:column; gap:20px; transition:var(--transition); position:relative; overflow:hidden;">
                <!-- Glowing corner effect -->
                <div style="position:absolute; top:-30px; right:-30px; width:100px; height:100px; background:radial-gradient(circle, rgba(99,102,241,0.15) 0%, transparent 70%); border-radius:50%;"></div>
                
                <div style="display:flex; justify-content:space-between; align-items:start;">
                    <div style="width:48px; height:48px; border-radius:14px; background:rgba(99,102,241,0.1); border:1px solid rgba(99,102,241,0.2); display:flex; align-items:center; justify-content:center; font-size:20px; color:var(--primary);">
                        <i class="fa-solid fa-city"></i>
                    </div>
                    <div style="text-align:right;">
                        <div style="font-size:11.5px; font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.05em;">Total Active</div>
                        <div style="font-size:32px; font-weight:800; color:var(--text-primary); margin-top:2px;">{{ $branchesCount }}</div>
                    </div>
                </div>

                <div>
                    <h3 style="font-size:17px; font-weight:700; color:var(--text-primary); margin-bottom:6px;">Branches</h3>
                    <p style="font-size:13px; color:var(--text-muted); line-height:1.5;">Configure physical locations, regional hubs, head offices, and remote working environments.</p>
                </div>

                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:12px; margin-top:auto;">
                    <a href="{{ route('branches.index') }}" style="display:block; text-align:center; padding:10px 14px; background:rgba(255,255,255,0.03); border:1px solid var(--border); border-radius:10px; font-size:12.5px; font-weight:700; color:var(--text-primary); text-decoration:none; transition:var(--transition);">
                        <i class="fa-solid fa-list" style="margin-right:6px;"></i> View List
                    </a>
                    <a href="{{ route('branches.create') }}" style="display:block; text-align:center; padding:10px 14px; background:var(--primary); border:none; border-radius:10px; font-size:12.5px; font-weight:700; color:#fff; text-decoration:none; transition:var(--transition); box-shadow:0 4px 12px rgba(99,102,241,0.25);">
                        <i class="fa-solid fa-plus" style="margin-right:6px;"></i> Add New
                    </a>
                </div>
            </div>

            <!-- DEPARTMENTS CARD -->
            <div class="card" style="padding:28px 24px; display:flex; flex-direction:column; gap:20px; transition:var(--transition); position:relative; overflow:hidden;">
                <!-- Glowing corner effect -->
                <div style="position:absolute; top:-30px; right:-30px; width:100px; height:100px; background:radial-gradient(circle, rgba(6,182,212,0.15) 0%, transparent 70%); border-radius:50%;"></div>
                
                <div style="display:flex; justify-content:space-between; align-items:start;">
                    <div style="width:48px; height:48px; border-radius:14px; background:rgba(6,182,212,0.1); border:1px solid rgba(6,182,212,0.2); display:flex; align-items:center; justify-content:center; font-size:20px; color:var(--accent-teal);">
                        <i class="fa-solid fa-sitemap"></i>
                    </div>
                    <div style="text-align:right;">
                        <div style="font-size:11.5px; font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.05em;">Total Active</div>
                        <div style="font-size:32px; font-weight:800; color:var(--text-primary); margin-top:2px;">{{ $departmentsCount }}</div>
                    </div>
                </div>

                <div>
                    <h3 style="font-size:17px; font-weight:700; color:var(--text-primary); margin-bottom:6px;">Departments</h3>
                    <p style="font-size:13px; color:var(--text-muted); line-height:1.5;">Organize company structural units, teams, functional departments, and regional cost centers.</p>
                </div>

                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:12px; margin-top:auto;">
                    <a href="{{ route('departments.index') }}" style="display:block; text-align:center; padding:10px 14px; background:rgba(255,255,255,0.03); border:1px solid var(--border); border-radius:10px; font-size:12.5px; font-weight:700; color:var(--text-primary); text-decoration:none; transition:var(--transition);">
                        <i class="fa-solid fa-list" style="margin-right:6px;"></i> View List
                    </a>
                    <a href="{{ route('departments.create') }}" style="display:block; text-align:center; padding:10px 14px; background:var(--primary); border:none; border-radius:10px; font-size:12.5px; font-weight:700; color:#fff; text-decoration:none; transition:var(--transition); box-shadow:0 4px 12px rgba(99,102,241,0.25);">
                        <i class="fa-solid fa-plus" style="margin-right:6px;"></i> Add New
                    </a>
                </div>
            </div>

            <!-- DESIGNATIONS CARD -->
            <div class="card" style="padding:28px 24px; display:flex; flex-direction:column; gap:20px; transition:var(--transition); position:relative; overflow:hidden;">
                <!-- Glowing corner effect -->
                <div style="position:absolute; top:-30px; right:-30px; width:100px; height:100px; background:radial-gradient(circle, rgba(168,85,247,0.15) 0%, transparent 70%); border-radius:50%;"></div>
                
                <div style="display:flex; justify-content:space-between; align-items:start;">
                    <div style="width:48px; height:48px; border-radius:14px; background:rgba(168,85,247,0.1); border:1px solid rgba(168,85,247,0.2); display:flex; align-items:center; justify-content:center; font-size:20px; color:var(--accent-purple);">
                        <i class="fa-solid fa-user-tag"></i>
                    </div>
                    <div style="text-align:right;">
                        <div style="font-size:11.5px; font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.05em;">Total Active</div>
                        <div style="font-size:32px; font-weight:800; color:var(--text-primary); margin-top:2px;">{{ $designationsCount }}</div>
                    </div>
                </div>

                <div>
                    <h3 style="font-size:17px; font-weight:700; color:var(--text-primary); margin-bottom:6px;">Designations</h3>
                    <p style="font-size:13px; color:var(--text-muted); line-height:1.5;">Define operational hierarchy roles, official job titles, engineering rankings, and staff seniority levels.</p>
                </div>

                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:12px; margin-top:auto;">
                    <a href="{{ route('designations.index') }}" style="display:block; text-align:center; padding:10px 14px; background:rgba(255,255,255,0.03); border:1px solid var(--border); border-radius:10px; font-size:12.5px; font-weight:700; color:var(--text-primary); text-decoration:none; transition:var(--transition);">
                        <i class="fa-solid fa-list" style="margin-right:6px;"></i> View List
                    </a>
                    <a href="{{ route('designations.create') }}" style="display:block; text-align:center; padding:10px 14px; background:var(--primary); border:none; border-radius:10px; font-size:12.5px; font-weight:700; color:#fff; text-decoration:none; transition:var(--transition); box-shadow:0 4px 12px rgba(99,102,241,0.25);">
                        <i class="fa-solid fa-plus" style="margin-right:6px;"></i> Add New
                    </a>
                </div>
            </div>

        </div>

    </div>
</div>
@endsection
