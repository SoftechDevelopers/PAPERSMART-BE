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
            <h3 style="text-decoration: underline;">PURCHASE ORDER</h3>
        </div>  

         <table width="100%" border="0" cellspacing="0" cellpadding="10" style="margin-top: 20px;">
            <tr>
                <td style="width: 50%; border: 1px solid #ddd; padding: 12px; border-radius: 5px; vertical-align: top;">
                    <h5 style="margin-bottom: 8px; font-size: 14px; text-decoration: underline;">
                        <strong>Vendor:</strong>
                    </h5>
                    <div style="font-weight: normal; font-size: 12px; line-height: 1.5; padding-left: 6px;">
                        {{ $vendor['name'] }}<br>      
                        {{ $vendor['address2'] }}<br>   
                        {{ $vendor['district'] }}<br>     
                        {{ $vendor['state'] }}-{{ $vendor['pincode'] }}          
                    </div>
                </td>
                <td style="width: 50%; border: 1px solid #ddd; padding: 12px; border-radius: 5px; vertical-align: top;">
                    <h5 style="margin-bottom: 8px; font-size: 14px; text-decoration: underline;">
                        <strong>Billing Address:</strong>
                    </h5>
                    <div style="font-weight: normal; font-size: 12px; line-height: 1.5; padding-left: 6px;">
                        {{ $organization['name'] }}<br>
                        {{ $organization['address1'] }}, {{ $organization['address2'] }}<br>         
                        {{ $organization['district'] }}, {{ $organization['state'] }}-{{ $vendor['pincode'] }}<br>
                        <strong>GSTIN:</strong> 03ADLFS5708D1Z8        
                    </div>
                </td>
                 <td style="width: 50%; border: 1px solid #ddd; padding: 12px; border-radius: 5px; vertical-align: top;">
                    <h5 style="margin-bottom: 8px; font-size: 14px; text-decoration: underline;">
                        <strong>Shipping Address:</strong>
                    </h5>
                    <div style="font-weight: normal; font-size: 12px; line-height: 1.5; padding-left: 6px;">
                        {{ $ship_to }}<br>                              
                    </div>
                </td>
            </tr>
        </table>
        <div style="margin-top: 8px; margin-bottom: 8px">
            <h5><span style="font-weight: normal;">We are pleased to place an order for the following materials/services. 
                Kindly deliver the materials/services on or before {{ $deadline }}. 
                The order will be deemed cancelled, if the delivery is delayed.
            </span></h5>
        </div>
        <table width="100%" border="1" cellspacing="0" cellpadding="8"
            style="font-size: 12px; border-collapse: collapse; width: 100%; border: 1px solid #999;">
            <thead>
                <tr style="background-color: #f4f4f4; border: 1px solid #999;">
                    <th style="text-align: left; padding: 4px; border: 1px solid #999;">#</th>
                    <th style="text-align: left; padding: 4px; border: 1px solid #999;">Name</th>
                    <th style="text-align: left; padding: 4px; border: 1px solid #999;">Model</th>
                    <th style="text-align: left; padding: 4px; border: 1px solid #999;">Manufacturer</th>
                    <th style="text-align: left; padding: 4px; border: 1px solid #999;">Qty</th>
                    <th style="text-align: left; padding: 4px; border: 1px solid #999;">Unit</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $index => $item)
                    <tr style="border: 1px solid #999;">
                        <td style="padding: 4px; border: 1px solid #999;">{{ $index + 1 }}</td>    
                        <td style="padding: 4px; border: 1px solid #999;"><strong>{{ $item['item']['name'] }}</strong></td>  
                        <td style="padding: 4px; text-align: left; border: 1px solid #999;">{{ $item['item']['model'] }}</td> 
                        <td style="padding: 4px; text-align: left; border: 1px solid #999;">{{ $item['item']['manufacturer'] }}</td>               
                        <td style="padding: 4px; text-align: left; border: 1px solid #999;">{{ $item['quantity'] }}</td> 
                        <td style="padding: 4px; text-align: left; border: 1px solid #999;">{{ $item['item']['unit'] }}</td>                       
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>
