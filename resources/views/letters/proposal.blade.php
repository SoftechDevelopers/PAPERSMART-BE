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

        <table width="100%">
            <tr>
                <td style="text-align: left; font-size: 12px;"><strong>Ref No:</strong> {{ $ref_no }}</td>
                <td style="text-align: right; font-size: 12px;"><strong>Date:</strong> {{ $date }}</td>
            </tr>
        </table>

        <div style="text-align: center;">
            <h3 style="text-decoration: underline;">Proposal for {{ $proposal_type }}</h3>
        </div>

        <div>
            <h5><strong>Party:</strong> <span style="font-weight: normal;">{{ $client_name . ', ' . $client_address }}</span></h5>
        </div>

        <table width="100%" border="1" cellspacing="0" cellpadding="8"
            style="font-size: 12px; border-collapse: collapse; width: 100%; border: 1px solid #999;">
            <thead>
                <tr style="background-color: #f4f4f4; border: 1px solid #999;">
                    <th style="text-align: left; padding: 4px; border: 1px solid #999;">#</th>
                    <th style="text-align: left; padding: 4px; border: 1px solid #999;">Item</th>
                    <th style="text-align: left; padding: 4px; border: 1px solid #999;">Manufacturer / Model</th>
                    <th style="text-align: left; padding: 4px; border: 1px solid #999;">Unit</th>
                    <th style="text-align: right; padding: 4px; border: 1px solid #999;">Base&nbsp;Price</th>
                    <th style="text-align: right; padding: 4px; border: 1px solid #999;">GST</th>
                    <th style="text-align: right; padding: 4px; border: 1px solid #999;">Rate</th>
                    <th style="text-align: right; padding: 4px; border: 1px solid #999;">Qty</th>
                    <th style="text-align: right; padding: 4px; border: 1px solid #999;">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($proposal_items as $index => $item)
                    @php
                        $formatter = new NumberFormatter('en_IN', NumberFormatter::CURRENCY);
                    @endphp
                    <tr style="border: 1px solid #999;">
                        <td style="padding: 4px; border: 1px solid #999;">{{ $index + 1 }}</td>
                        <td style="padding: 4px; border: 1px solid #999;"><strong>{{ $item['item']['name'] }}</strong></td>
                        <td style="padding: 4px; border: 1px solid #999;">
                            <span>{{ $item['item']['manufacturer'] }}</span><br>
                            <span style="font-size: 11px; ">{{ $item['item']['model'] }}</span>
                        </td>
                        <td style="padding: 4px; border: 1px solid #999;">{{ $item['item']['unit'] }}</td>
                        <td style="padding: 4px; text-align: right; border: 1px solid #999;">
                            {{ str_replace('₹', '', $formatter->formatCurrency($item['base_price'], 'INR')) }}
                        </td>
                        <td style="padding: 4px; text-align: right; border: 1px solid #999;">{{ $item['gst_percentage'] }}%</td>
                        <td style="padding: 4px; text-align: right; border: 1px solid #999;">
                            {{ str_replace('₹', '', $formatter->formatCurrency($item['rate'], 'INR')) }}
                        </td>
                        <td style="padding: 4px; text-align: right; border: 1px solid #999;">{{ $item['quantity'] }}</td>
                        <td style="padding: 4px; text-align: right; border: 1px solid #999;">
                            {{ str_replace('₹', '', $formatter->formatCurrency($item['amount'], 'INR')) }}
                        </td>
                    </tr>
                @endforeach

                <tr style="background-color: #f4f4f4; font-weight: bold; border: 1px solid #999;">
                    <td colspan="7" style="padding: 10px; border: 1px solid #999;"></td>
                    <td style="padding: 10px; text-align: right; border: 1px solid #999;">Total:</td>
                    <td style="padding: 10px; text-align: right; border: 1px solid #999;">
                        {{ str_replace('₹', '', $formatter->formatCurrency($total, 'INR')) }}
                    </td>
                </tr>
            </tbody>
        </table>

        <table width="100%" border="0" cellspacing="0" cellpadding="10" style="margin-top: 20px;">
            <tr>
                <td style="width: 50%; border: 1px solid #ddd; padding: 12px; background-color: #f9f9f9; border-radius: 5px; vertical-align: top;">
                    <h5 style="margin-bottom: 8px; font-size: 14px; text-decoration: underline;">
                        <strong>Terms & Conditions:</strong>
                    </h5>
                    <div style="font-weight: normal; font-size: 12px; line-height: 1.5;">
                        {!! nl2br(e($notes)) !!}
                    </div>
                    <br>
                    @if ($valid_for)
                        <div style="font-weight: normal; font-size: 12px; line-height: 1.5; margin-top: 8px;">
                            <strong>NB:</strong> This proposal is valid for {{ $valid_for }} days
                        </div>
                    @elseif ($valid_upto)
                        <div style="font-weight: normal; font-size: 12px; line-height: 1.5; margin-top: 8px;">
                            <strong>NB:</strong> This proposal is valid up to {{ $valid_upto }}
                        </div>
                    @endif
                </td>
                <td style="width: 50%; border: 1px solid #ddd; padding: 12px; border-radius: 5px; vertical-align: top;">
                    <h5 style="margin-bottom: 8px; font-size: 14px; text-decoration: underline;">
                        <strong>Account Details:</strong>
                    </h5>
                    <div style="font-weight: normal; font-size: 12px; line-height: 1.5;">
                        <strong>Beneficiary:</strong> {{ $bank_account['beneficiary'] }} <br>
                        <strong>Account No:</strong> {{ $bank_account['account_no'] }} <br>
                        <strong>Bank:</strong> {{ $bank_account['bank']. ', '. $bank_account['branch'] }} <br>
                        <strong>IFSC:</strong> {{ $bank_account['ifsc'] }}
                    </div>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
