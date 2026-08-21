<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Full Month Present - Attendance Update</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background-color: #f4f4f4; padding: 20px; border-radius: 5px;">
        <h2 style="color: #2c3e50; margin-top: 0;">Full Month Present - Attendance Update</h2>
        
        <p>Dear Team,</p>
        
        <p>This is to inform you that <strong>{{ $data['employee_count'] }} employee(s)</strong> have been marked as present for the full month of <strong>{{ $data['month'] }}</strong>.</p>
        
        <div style="background-color: #fff; padding: 15px; border-radius: 5px; margin: 20px 0;">
            <h3 style="color: #27ae60; margin-top: 0;">Employees with Full Month Present:</h3>
            <ul style="list-style-type: none; padding-left: 0;">
                @foreach($data['employees'] as $employee)
                <li style="padding: 5px 0; border-bottom: 1px solid #eee;">✓ {{ $employee }}</li>
                @endforeach
            </ul>
        </div>
        
        <p>All attendance records have been updated accordingly.</p>
        
        <p style="margin-top: 30px;">
            Best regards,<br>
            <strong>HRM System</strong>
        </p>
    </div>
</body>
</html>
