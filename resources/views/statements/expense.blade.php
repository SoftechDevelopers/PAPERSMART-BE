<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proposal</title>
    <style>
        body {
            font-family: 'Helvetica', sans-serif;
            margin: 10px;
            padding: 0;
        }

        .letter-container {
            width: 100%;
            max-width: 100%;
            margin: auto;
            position: relative;
            padding-top: 140px;
        }

        .letterhead {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: auto;
            z-index: -1;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;,
        }

        h5 {
            margin: 2 !important;
            padding: 2 !important;
        }

    </style>
</head>
<body>
    <div class="letter-container">
        <img src="{{ public_path('images/letter-head.png') }}" class="letterhead" alt="Letterhead">
    </div>
    <table width="100%">
    <tr>
        <td style="text-align: left; font-size: 12px;">
            <strong>Recipient Name:</strong> {{ $name }}
        </td>
    </tr>
    <tr>
        <td style="text-align: left; font-size: 12px;">
            <strong>Address:</strong> {{ $address }}
        </td>
    </tr>
    <tr>
        <td style="text-align: left; font-size: 12px;">
            <strong>Phone:</strong> {{ $phone }}
        </td>
    </tr>
    <tr>
        <td style="text-align: left; font-size: 12px;">
            <strong>Balance:</strong> {{ $balance }}
        </td>
    </tr>
</table>

<div>
    <h5><strong>Account statement for the period </strong> {{ $start_date . ' to ' . $end_date }}</h5>
</div>

<table width="100%" border="1" cellspacing="0" cellpadding="8"
    style="font-size: 12px; border-collapse: collapse; width: 100%; border: 1px solid #999;">
    <thead>
        <tr style="background-color: #f4f4f4; border: 1px solid #999;">
            <th style="text-align: left; padding: 4px; border: 1px solid #999;">#</th>
            <th style="text-align: left; padding: 4px; border: 1px solid #999;">Date</th>
            <th style="text-align: left; padding: 4px; border: 1px solid #999;">Particulars</th>
            <th style="text-align: left; padding: 4px; border: 1px solid #999;">Credit</th>
            <th style="text-align: left; padding: 4px; border: 1px solid #999;">Debit</th>
            <th style="text-align: left; padding: 4px; border: 1px solid #999;">Balance</th>
        </tr>
    </thead>
    <tbody>
        @foreach($expenses as $index => $item)
            <tr style="border: 1px solid #999;">
                <td style="padding: 4px; border: 1px solid #999;">{{ $index + 1 }}</td>
                <td style="padding: 4px; border: 1px solid #999; white-space: nowrap;">{{ $item['date'] }}</td>
                <td style="padding: 4px; border: 1px solid #999;">{{ $item['particulars'] }}</td>
                <td style="padding: 4px; border: 1px solid #999;">{{ $item['credit'] }}</td>
                <td style="padding: 4px; border: 1px solid #999;">{{ $item['debit'] }}</td>
                <td style="padding: 4px; border: 1px solid #999;">{{ $item['balance'] }}</td>
            </tr>
        @endforeach
        <tr style="background-color: #f4f4f4; font-weight: bold; border: 1px solid #999;">
            <td colspan="3" style="padding: 10px; border: 1px solid #999;">Total</td>
            <td style="padding: 10px; text-align: right; border: 1px solid #999;">{{ $total_credit }}</td>
            <td style="padding: 10px; text-align: right; border: 1px solid #999;">{{ $total_debit }}</td>
            <td style="padding: 10px; text-align: right; border: 1px solid #999;"></td>
        </tr>
    </tbody>
</table>

</body>
</html>
