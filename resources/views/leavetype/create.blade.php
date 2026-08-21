{{ Form::open(['url' => 'leavetype', 'method' => 'post']) }}
<div class="modal-body">
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12">
            <div class="form-group">
                {{ Form::label('title', __('Name'), ['class' => 'form-label']) }}
                <div class="form-icon-user">
                    {{ Form::text('title', null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Enter Leave Type Name')]) }}
                </div>
                @error('title')
                    <span class="invalid-name" role="alert">
                        <strong class="text-danger">{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>

        <div class="col-lg-12 col-md-12 col-sm-12">
            <div class="form-group">
                <div class="form-check">
                    <input type="checkbox" name="unlimited" value="1" class="form-check-input" id="unlimited-check">
                    <label class="form-check-label" for="unlimited-check">{{ __('Unlimited Days (e.g. Casual Leave)') }}</label>
                </div>
            </div>
        </div>

        <div class="col-lg-12 col-md-12 col-sm-12" id="days-container">
            <div class="form-group">
                {{ Form::label('days', __('Days Per Month'), ['class' => 'form-label']) }}
                <div class="form-icon-user">
                    <input type="number" name="days" class="form-control" step="0.5" min="0" placeholder="{{ __('Enter Days Per Month (e.g. 1.5)') }}" id="days-input">
                </div>
                @error('days')
                    <span class="invalid-name" role="alert">
                        <strong class="text-danger">{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <input type="button" value="Cancel" class="btn btn-light" data-bs-dismiss="modal">
    <input type="submit" value="{{ __('Create') }}" class="btn btn-primary">
</div>
{{ Form::close() }}

@push('scripts')
<script>
    $(document).ready(function() {
        $('#unlimited-check').change(function() {
            if($(this).is(':checked')) {
                $('#days-container').hide();
                $('input[name="days"]').val('').prop('disabled', true);
            } else {
                $('#days-container').show();
                $('input[name="days"]').prop('disabled', false);
            }
        });
        
        // Initialize based on current state
        if($('#unlimited-check').is(':checked')) {
            $('#days-container').hide();
            $('input[name="days"]').prop('disabled', true);
        }
    });
    </script>
@endpush