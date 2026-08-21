{{ Form::model($notice, ['route' => ['notices.update', $notice->id], 'method' => 'PUT']) }}
<div class="modal-body">
    <div class="row g-3">  <!-- Added 'g-3' for better spacing -->

        <!-- Notice Title -->
        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('title', __('Notice Title'), ['class' => 'form-label']) }}
                {{ Form::text('title', null, ['class' => 'form-control', 'required' => true, 'placeholder' => __('Enter Notice Title')]) }}
            </div>
        </div>

        <!-- Notice Description -->
        <div class="col-md-12">
            <div class="form-group">
                {{ Form::label('description', __('Notice Description'), ['class' => 'form-label']) }}
                {{ Form::textarea('description', null, ['class' => 'form-control summernote-simple', 'required' => true, 'placeholder' => __('Enter Notice Description')]) }}
            </div>
        </div>

        <!-- Notice Start Date -->
        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('notice_startdate', __('Start Date'), ['class' => 'form-label']) }}
                {{ Form::date('notice_startdate', $notice->notice_startdate ? \Carbon\Carbon::parse($notice->notice_startdate)->format('Y-m-d') : null, ['class' => 'form-control', 'required' => true]) }}
            </div>
        </div>

        <!-- Notice End Date -->
        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('notice_enddate', __('End Date'), ['class' => 'form-label']) }}
                {{ Form::date('notice_enddate', $notice->notice_enddate ? \Carbon\Carbon::parse($notice->notice_enddate)->format('Y-m-d') : null, ['class' => 'form-control', 'required' => true]) }}
            </div>
        </div>

       

    </div>
</div>

<!-- Modal Footer -->
<div class="modal-footer">
    <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
    <button type="submit" class="btn btn-primary">{{ __('Update Notice') }}</button>
</div>

{{ Form::close() }}
