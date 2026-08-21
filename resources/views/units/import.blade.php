@extends('layouts.admin')

@section('page-title')
    {{ __('Import Units') }}
@endsection

@section('content')
<div class="row">
    <div class="col-md-6 offset-md-3">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('units.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="file" class="form-label">{{ __('Choose Excel File') }}</label>
                        <input type="file" name="file" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary">{{ __('Import Units') }}</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
