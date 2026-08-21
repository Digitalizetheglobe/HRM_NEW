@if(count($todos) > 0)
    <div style="display: flex; flex-direction: column; gap: 12px; max-height: 350px; overflow-y: auto; padding-right: 5px;">
        @foreach($todos as $task)
            <div style="background: #f0fdf4; border: 1px solid #dcfce7; border-radius: 12px; padding: 16px 10px; display: flex; align-items: center; justify-content: space-between; gap: 10px; transition: all 0.2s ease;" onmouseover="this.style.borderColor='#bbf7d0'; this.style.boxShadow='0 4px 6px rgba(0,0,0,0.02)'" onmouseout="this.style.borderColor='#dcfce7'; this.style.boxShadow='none'">
                <div class="d-flex align-items-center" style="flex: 1; min-width: 0;">
                    <div style="width: 42px; height: 42px; background: #fff; border-radius: 10px; display: flex; align-items: center; justify-content: center; margin-right: 12px; flex-shrink: 0; box-shadow: 0 2px 5px rgba(0,0,0,0.02);">
                        <i class="ti ti-user" style="color: #0f766e; font-size: 20px;"></i>
                    </div>
                    <div style="flex: 1; min-width: 0;">
                        <h6 style="margin: 0 0 4px 0; font-size: 15px; font-weight: 700; color: #111827; word-break: break-word; white-space: normal;">{{ explode("\n\n", $task->task)[0] }}</h6>
                        <div style="font-size: 13px; color: #4b5563; display: flex; flex-wrap: wrap; align-items: center; gap: 8px;">
                            <span style="font-weight: 500;">Priority:</span> 
                            <span style="color: {{ strtolower($task->priority) == 'high' ? '#dc2626' : (strtolower($task->priority) == 'medium' ? '#d97706' : '#059669') }}">{{ ucfirst($task->priority) }}</span>
                            <span style="color: #cbd5e1;">|</span>
                            <span style="font-weight: 500;">Status:</span>
                            <span style="color: {{ $task->is_completed ? '#059669' : '#d97706' }}">{{ $task->is_completed ? 'Completed' : 'Pending' }}</span>
                        </div>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-1" style="flex-shrink: 0;">
                    <a href="#" data-bs-toggle="modal" data-bs-target="#viewScheduleModal{{ $task->id }}" style="color: #8b5cf6; width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; border-radius: 6px; transition: background 0.2s;" onmouseover="this.style.background='#ede9fe'" onmouseout="this.style.background='transparent'" title="{{ __('View') }}">
                        <i class="ti ti-eye" style="font-size: 16px;"></i>
                    </a>
                    <a href="#" data-bs-toggle="modal" data-bs-target="#editScheduleModal{{ $task->id }}" style="color: #0ea5e9; width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; border-radius: 6px; transition: background 0.2s;" onmouseover="this.style.background='#e0f2fe'" onmouseout="this.style.background='transparent'" title="{{ __('Edit') }}">
                        <i class="ti ti-pencil" style="font-size: 16px;"></i>
                    </a>
                    <form action="{{ route('todo.destroy', $task->id) }}" method="POST" class="m-0 p-0 d-flex align-items-center" id="delete-form-{{ $task->id }}">
                        @csrf
                        @method('DELETE')
                        <a href="#" style="color: #ef4444; width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; border-radius: 6px; transition: background 0.2s;" onmouseover="this.style.background='#fee2e2'" onmouseout="this.style.background='transparent'" title="{{ __('Delete') }}" onclick="event.preventDefault(); if(confirm('Are you sure?')) document.getElementById('delete-form-{{ $task->id }}').submit();">
                            <i class="ti ti-trash" style="font-size: 16px;"></i>
                        </a>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
    
    @foreach($todos as $task)
        <!-- View Schedule Modal -->
        <div class="modal fade" id="viewScheduleModal{{ $task->id }}" tabindex="-1" aria-labelledby="viewScheduleModalLabel{{ $task->id }}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
                    <div class="modal-header" style="background:linear-gradient(135deg, #0b0b39ff 0%, #393970ff 100%); border-bottom: none; padding: 20px 25px;">
                        <h5 class="modal-title text-white d-flex align-items-center" id="viewScheduleModalLabel{{ $task->id }}">
                            <i class="ti ti-calendar-event me-2" style="font-size: 22px;"></i>{{ __('View ToDo') }}
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4 bg-light text-left">
                        <div class="row g-4">
                            <div class="col-12 text-start">
                                <label class="form-label font-weight-bold" style="color: #495057; font-size: 13px; font-weight: 600;">{{ __('Task Title') }}</label>
                                <div style="background: #fff; border-radius: 8px; border: 1px solid #ced4da; padding: 10px 15px; min-height: 44px; color:#555;">{{ explode("\n\n", $task->task)[0] }}</div>
                            </div>
                            <div class="col-md-6 text-start">
                                <label class="form-label font-weight-bold" style="color: #495057; font-size: 13px; font-weight: 600;">{{ __('Start Date') }}</label>
                                <div style="background: #fff; border-radius: 8px; border: 1px solid #ced4da; padding: 10px 15px; min-height: 44px; color:#555;">{{ $task->start_date ? \Carbon\Carbon::parse($task->start_date)->format('d-m-Y, h:i A') : '-' }}</div>
                            </div>
                            <div class="col-md-6 text-start">
                                <label class="form-label font-weight-bold" style="color: #495057; font-size: 13px; font-weight: 600;">{{ __('End Date') }}</label>
                                <div style="background: #fff; border-radius: 8px; border: 1px solid #ced4da; padding: 10px 15px; min-height: 44px; color:#555;">{{ $task->end_date ? \Carbon\Carbon::parse($task->end_date)->format('d-m-Y, h:i A') : '-' }}</div>
                            </div>
                            <div class="col-md-6 text-start">
                                <label class="form-label font-weight-bold" style="color: #495057; font-size: 13px; font-weight: 600;">{{ __('Priority') }}</label>
                                <div style="background: #fff; border-radius: 8px; border: 1px solid #ced4da; padding: 10px 15px; min-height: 44px; color:#555;">{{ ucfirst($task->priority) }}</div>
                            </div>
                            <div class="col-md-6 text-start">
                                <label class="form-label font-weight-bold" style="color: #495057; font-size: 13px; font-weight: 600;">{{ __('Status') }}</label>
                                <div style="background: #fff; border-radius: 8px; border: 1px solid #ced4da; padding: 10px 15px; min-height: 44px; color:#555;">
                                    @if($task->is_completed)
                                        <span class="badge bg-success" style="padding: 5px 10px; border-radius: 5px;">{{ __('Completed') }}</span>
                                    @else
                                        <span class="badge bg-warning" style="padding: 5px 10px; border-radius: 5px;">{{ __('Pending') }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-12 text-start">
                                <label class="form-label font-weight-bold" style="color: #495057; font-size: 13px; font-weight: 600;">{{ __('Description') }}</label>
                                <div style="background: #fff; border-radius: 8px; border: 1px solid #ced4da; padding: 12px 15px; min-height: 70px; color:#555; white-space: pre-wrap;">{{ $task->description ?: '-' }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-white border-top px-4 py-3">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="border-radius: 8px; font-weight: 600; padding: 8px 20px;">{{ __('Close') }}</button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    @foreach($todos as $task)
        <!-- Edit Schedule Modal -->
        <div class="modal fade" id="editScheduleModal{{ $task->id }}" tabindex="-1" aria-labelledby="editScheduleModalLabel{{ $task->id }}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
                    <div class="modal-header" style="background:linear-gradient(135deg, #0b0b39ff 0%, #393970ff 100%); border-bottom: none; padding: 20px 25px;">
                        <h5 class="modal-title text-white d-flex align-items-center" id="editScheduleModalLabel{{ $task->id }}">
                            <i class="ti ti-pencil me-2" style="font-size: 22px;"></i>{{ __('Edit ToDo') }}
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4 bg-light text-left">
                        <form action="{{ route('todo.update', $task->id) }}" method="POST" id="editScheduleForm{{ $task->id }}">
                            @csrf
                            @method('PUT')
                            @php
                                $parts = explode("\n\n", $task->task);
                                $taskTitle = $parts[0];
                                $taskDesc = $task->description ?: '';
                            @endphp
                            <div class="row g-4">
                                <div class="col-12 text-start">
                                    <label class="form-label font-weight-bold" style="color: #495057; font-size: 13px; font-weight: 600;">{{ __('Task Title') }} <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control shadow-sm" name="task" value="{{ $taskTitle }}" required placeholder="{{ __('Enter task title...') }}" style="border-radius: 8px; border: 1px solid #ced4da; padding: 10px 15px;">
                                </div>
                                <div class="col-md-6 text-start">
                                    <label class="form-label font-weight-bold" style="color: #495057; font-size: 13px; font-weight: 600;">{{ __('Start Date') }} <span class="text-danger">*</span></label>
                                    <input type="datetime-local" class="form-control shadow-sm" name="start_date" value="{{ $task->start_date ? \Carbon\Carbon::parse($task->start_date)->format('Y-m-d\TH:i') : '' }}" required style="border-radius: 8px; border: 1px solid #ced4da; padding: 10px 15px;">
                                </div>
                                <div class="col-md-6 text-start">
                                    <label class="form-label font-weight-bold" style="color: #495057; font-size: 13px; font-weight: 600;">{{ __('End Date') }} <span class="text-danger">*</span></label>
                                    <input type="datetime-local" class="form-control shadow-sm" name="end_date" value="{{ $task->end_date ? \Carbon\Carbon::parse($task->end_date)->format('Y-m-d\TH:i') : '' }}" required style="border-radius: 8px; border: 1px solid #ced4da; padding: 10px 15px;">
                                </div>
                                <div class="col-md-6 text-start">
                                    <label class="form-label font-weight-bold" style="color: #495057; font-size: 13px; font-weight: 600;">{{ __('Priority') }} <span class="text-danger">*</span></label>
                                    <select class="form-select shadow-sm" name="priority" required style="border-radius: 8px; border: 1px solid #ced4da; padding: 10px 15px;">
                                        <option value="high" {{ strtolower($task->priority) == 'high' ? 'selected' : '' }}>{{ __('High') }}</option>
                                        <option value="medium" {{ strtolower($task->priority) == 'medium' ? 'selected' : '' }}>{{ __('Medium') }}</option>
                                        <option value="low" {{ strtolower($task->priority) == 'low' ? 'selected' : '' }}>{{ __('Low') }}</option>
                                    </select>
                                </div>
                                <div class="col-md-6 text-start">
                                    <label class="form-label font-weight-bold" style="color: #495057; font-size: 13px; font-weight: 600;">{{ __('Task Status') }} <span class="text-danger">*</span></label>
                                    <select class="form-select shadow-sm" name="is_completed" required style="border-radius: 8px; border: 1px solid #ced4da; padding: 10px 15px;">
                                        <option value="0" {{ $task->is_completed == 0 ? 'selected' : '' }}>{{ __('Pending') }}</option>
                                        <option value="1" {{ $task->is_completed == 1 ? 'selected' : '' }}>{{ __('Completed') }}</option>
                                    </select>
                                </div>
                                <div class="col-12 text-start">
                                    <label class="form-label font-weight-bold" style="color: #495057; font-size: 13px; font-weight: 600;">{{ __('Description') }} <span class="text-muted fw-normal">({{ __('Optional') }})</span></label>
                                    <textarea class="form-control shadow-sm" name="description" rows="3" placeholder="{{ __('Enter task details here...') }}" style="border-radius: 8px; border: 1px solid #ced4da; padding: 12px 15px; resize: none;">{{ $taskDesc }}</textarea>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer bg-white border-top px-4 py-3">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="border-radius: 8px; font-weight: 600; padding: 8px 20px;">{{ __('Cancel') }}</button>
                        <button type="submit" form="editScheduleForm{{ $task->id }}" class="btn btn-primary" style="border-radius: 8px; background: #0b0b39ff; border-color: #0b0b39ff; font-weight: 600; padding: 8px 20px; box-shadow: 0 4px 10px rgba(11, 11, 57, 0.2);">{{ __('Update Task') }}</button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@else
    <div class="d-flex flex-column align-items-center justify-content-center text-muted" style="height: 250px; background: #f9fafb; border-radius: 12px; border: 1px dashed #e5e7eb;">
        <div style="width: 64px; height: 64px; background: #fff; border: 1px solid #eef2f7; box-shadow: 0 4px 10px rgba(0,0,0,0.03); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-bottom: 15px;">
            <i class="ti ti-calendar-event" style="font-size: 30px; color: #a1aab2;"></i>
        </div>
        <h6 class="mb-1" style="font-weight: 600; color: #495057;">{{ __('No tasks scheduled for the selected date') }}</h6>
        <p style="font-size: 13px; color: #87929d; margin-bottom: 0;">{{ __('Click \'Add\' to schedule a new task.') }}</p>
    </div>
@endif
