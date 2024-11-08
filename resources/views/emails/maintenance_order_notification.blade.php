<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maintenance Order Notification</title>
</head>
<body>
    <h2>New Maintenance Order Request</h2>
    <p>The following dies require maintenance. Please review the details below and click the link to proceed with the repair:</p>

    <table border="1" cellpadding="10" cellspacing="0">
        <thead>
            <tr>
                <th>Part Name</th>
                <th>Code - Process</th>
                <th>Problem</th>
                <th>Date</th>
                <th>Image</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($orders as $order)
            <tr>
                <td>{{ $order['part_name'] }}</td>
                <td>{{ $order['code_process'] }}</td>
                <td>{{ $order['problem'] }}</td>
                <td>{{ $order['date'] }}</td>
                <td>
                    @if ($order['img'])
                        <a href="{{ asset($order['img']) }}" target="_blank">View Image</a>
                    @else
                        N/A
                    @endif
                </td>
                <td>
                    <a href="{{ route('dies.repair.req', ['id' => encrypt($order['id_dies']), 'order_id' => encrypt($order['order_id'])]) }}" target="_blank">Request Repair</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
