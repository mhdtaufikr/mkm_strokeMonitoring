<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Preventive Maintenance Reminder</title>
</head>
<body>
    <h2>PM Reminder for Asset: {{ $asset->asset_no }}</h2>
    <p>Dear Team,</p>
    <p>The current quantity for the asset "<strong>{{ $asset->part_name }}</strong>" (Asset No: {{ $asset->asset_no }})
    is approaching the standard stroke limit.</p>

    <p><strong>Standard Stroke:</strong> {{ $asset->std_stroke }}<br>
       <strong>Current Quantity:</strong> {{ $asset->current_qty }}</p>

    <p>Please schedule preventive maintenance soon to avoid any disruptions.</p>

    <p><a href="{{ route('pm', ['id' => $asset->id]) }}">Click here to view and perform PM</a></p>

    <p>Best regards,<br>Maintenance Team</p>
</body>
</html>
