<div class="modal-body">

    <div class="form-group">
        <label class="form-label" for="leaveReasonTextarea">{{__('Leave Reason')}}</label>
        <textarea class="form-control" id="leaveReasonTextarea" rows="4" readonly>{{ isset($leave) ? $leave->leave_reason : 'No leave data found' }}</textarea>
    </div>

</div>
