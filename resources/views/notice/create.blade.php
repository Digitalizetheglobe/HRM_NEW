@php
    $setting = App\Models\Utility::settings();
@endphp
     {!! Form::open(['route' => 'notices.store', 'method' => 'POST']) !!}
     <div class="modal-body">              
                    <div class="row">

                        <!-- Title Field -->
                        <div class="col-md-6">
                            <div class="form-group">
                                {{ Form::label('title', __('Title'), ['class' => 'form-label']) }}
                                {{ Form::text('title', null, ['class' => 'form-control', 'required' => true, 'placeholder' => __('Enter Notice Title')]) }}
                            </div>
                        </div>

                        <!-- Description Field -->
                        <div class="col-md-12">
                            <div class="form-group">
                                {{ Form::label('description', __('Description'), ['class' => 'form-label']) }}
                                {{ Form::textarea('description', null, ['class' => 'form-control summernote-simple', 'required' => true, 'placeholder' => __('Enter Notice Description')]) }}
                            </div>
                        </div>

                        <!-- Notice Start Date Field -->
                        <div class="col-md-6">
                            <div class="form-group">
                                {{ Form::label('notice_startdate', __('Start Date'), ['class' => 'form-label']) }}
                                {{ Form::date('notice_startdate', null, ['class' => 'form-control', 'required' => true]) }}
                            </div>
                        </div>

                        <!-- Notice End Date Field -->
                        <div class="col-md-6">
                            <div class="form-group">
                                {{ Form::label('notice_enddate', __('End Date'), ['class' => 'form-label']) }}
                                {{ Form::date('notice_enddate', null, ['class' => 'form-control', 'required' => true]) }}
                            </div>
                        </div>

                    </div>
</div>

                <div class="modal-footer">
                    <a href="{{ route('notices.index') }}" class="btn btn-light">{{ __('Cancel') }}</a>
                    {{ Form::submit(__('Create Notice'), ['class' => 'btn btn-primary']) }}
                </div>
                {!! Form::close() !!}
          