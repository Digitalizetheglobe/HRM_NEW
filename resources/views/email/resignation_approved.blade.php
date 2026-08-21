<!DOCTYPE html>
<html>
<head>
    <title>Resignation Approved</title>
</head>
<body>
    <h2>Your Resignation Has Been Approved</h2>
    
    <p>Dear {{ $resignation->employee->full_name }},</p>
    
    <p>Your resignation has been approved with the following details:</p>
    
    <ul>
        <li><strong>Resignation Date:</strong> {{ \Auth::user()->dateFormat($resignation->notice_date) }}</li>
        <li><strong>Last Working Day:</strong> {{ \Auth::user()->dateFormat($resignation->resignation_date) }}</li>
    </ul>
    
    <p>Reason: {{ $resignation->description }}</p>
    
    <p>Please complete any pending work and hand over your responsibilities before your last working day.</p>
    
    <p>Best regards,<br>
    {{ env('APP_NAME') }} HR Team</p>
</body>
</html>