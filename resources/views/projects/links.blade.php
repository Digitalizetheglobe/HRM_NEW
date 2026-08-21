@extends('layouts.admin')

@section('page-title')
    {{ __('Project Links') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item">{{ __('Project Links') }}</li>
@endsection

@section('content')
<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-header card-body table-border-style">
                <div class="table-responsive">
                    <table class="table" id="pc-dt-simple">
                        <thead>
                            <tr>
                                <th>{{ __('Project Name') }}</th>
                                <th>{{ __('Website URL') }}</th>
                                <th>{{ __('Dashboard URL') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($projects as $project)
                                <tr>
                                    <td>{{ $project->project_name }}</td>
                                    <td>
                                        @if(!empty($project->website_url))
                                            <a href="{{ $project->website_url }}" target="_blank" class="text-primary">{{ $project->website_url }}</a>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if(!empty($project->dashboard_url))
                                            <a href="{{ $project->dashboard_url }}" target="_blank" class="text-primary">{{ $project->dashboard_url }}</a>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
