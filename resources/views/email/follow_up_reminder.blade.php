@extends('email.common')

@section('content')
<div style="font-family:Open Sans, Helvetica, Arial, sans-serif;font-size:13px;line-height:22px;text-align:left;color:#797e82;">
    <h2 style="text-align:center; color: #6676EF; line-height:32px; margin-bottom: 20px;">Timesheet Follow-Up Reminder</h2>
    
    <p style="margin: 10px 0; text-align: left;">Dear Employee,</p>
    
    <p style="margin: 10px 0; text-align: left;">This is a reminder that you have a follow-up scheduled for today for your timesheet entry:</p>
    
    <div style="background-color: #f5f5f5; padding: 20px; margin: 20px 0; border-radius: 5px;">
        @if(isset($timeSheet) && $timeSheet->date)
        <p style="margin: 10px 0; text-align: left; color: #333333;"><strong>Timesheet Date:</strong> 
            @php
                try {
                    echo \Carbon\Carbon::parse($timeSheet->date)->format('F d, Y');
                } catch (\Exception $e) {
                    echo $timeSheet->date;
                }
            @endphp
        </p>
        @endif
        
        @if(isset($timeSheet) && $timeSheet->hours)
        <p style="margin: 10px 0; text-align: left; color: #333333;"><strong>Hours Worked:</strong> {{ $timeSheet->hours }}</p>
        @endif
        
        @if(isset($timeSheet) && $timeSheet->follow_up_date)
        <p style="margin: 10px 0; text-align: left; color: #333333;"><strong>Follow-Up Date:</strong> 
            @php
                try {
                    echo \Carbon\Carbon::parse($timeSheet->follow_up_date)->format('F d, Y');
                } catch (\Exception $e) {
                    echo $timeSheet->follow_up_date;
                }
            @endphp
        </p>
        @endif
    </div>
    
    @if(isset($lastRemark) && !empty($lastRemark))
    <div style="margin: 20px 0;">
        <h3 style="color: #6676EF; margin-bottom: 10px; text-align: left; font-size: 16px;">Remark / Description:</h3>
        <div style="background-color: #ffffff; padding: 15px; border-left: 4px solid #6676EF; margin: 10px 0;">
            <p style="margin: 0; color: #555555; text-align: left; line-height: 1.6; white-space: pre-wrap;">
                {{ $lastRemark }}
            </p>
        </div>
    </div>
    @endif
    
    <p style="margin: 20px 0; text-align: left;">
        Please ensure you check and complete your follow-up actions as scheduled.
    </p>
    
    <p style="margin: 20px 0; text-align: left; color: #666666;">
        Best regards,<br>
        {{ config('app.name') }} System
    </p>
</div>
@endsection
