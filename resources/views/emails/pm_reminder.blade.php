<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Preventive Maintenance Reminder</title>
</head>
<body>
    <h2>PM Reminder for Asset: {{ $asset_no }}</h2>
    <p>Dear Team,</p>
    <p>The current quantity for the asset "<strong>{{ $part_name }}</strong>" (Asset No: {{ $asset_no }})
    is approaching the standard stroke limit.</p>

    <p><strong>Standard Stroke:</strong> {{ $std_stroke }}<br>
       <strong>Current Quantity:</strong> {{ $current_qty }}</p>

    <p>Please schedule preventive maintenance soon to avoid any disruptions.</p>

    <p><a href="{{ $pmLink }}">Click here to view and perform PM</a></p>

    <p>Best regards,<br>Maintenance Team</p>
</body>
</html>
