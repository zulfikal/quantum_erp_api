<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quotation</title>
    <style>
        @page {
            margin: 10mm;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            margin: 0;
            padding: 0;
            color: #333;
            font-size: 12px;
            line-height: 1.5;
        }

        .container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
            padding: 0;
            box-sizing: border-box;
        }

        .header {
            text-align: right;
            margin-bottom: 20px;
        }

        .header h1 {
            color: #1F293B;
            font-size: 28px;
            margin: 0;
            padding: 0;
            text-transform: uppercase;
        }

        .divider {
            border-bottom: 1px solid #ddd;
            margin: 10px 0;
        }

        .info-grid {
            display: flex;
            margin-bottom: 20px;
        }

        .info-from {
            flex: 1;
            background-color: #eff0ff;
            padding: 10px;
        }

        .info-to {
            flex: 1;
            background-color: #eff0ff;
            padding: 10px;
        }

        .info-number {
            padding-left: 10px;
        }

        .quotation-number {
            font-size: 18px;
            font-weight: bold;
            background-color: #1F293B;
            color: white;
            padding: 10px;
            text-align: center;
            margin-bottom: 10px;
        }

        .info-details {
            margin-bottom: 5px;
        }

        .info-label {
            font-weight: bold;
            width: 80px;
            display: inline-block;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .info-table th {
            background-color: #1F293B;
            color: white;
            text-align: left;
            padding: 8px;
        }

        .info-table td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }

        .info-table .amount {
            text-align: right;
        }

        .info-table .item-number {
            width: 30px;
            text-align: center;
        }

        .summary-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .summary-table td {
            padding: 5px;
        }

        .summary-table .label {
            text-align: right;
            font-weight: normal;
        }

        .summary-table .amount {
            text-align: right;
            width: 100px;
        }

        .total-row {
            background-color: #1F293B;
            color: white;
            font-weight: bold;
        }

        .notes {
            margin-top: 30px;
            font-style: italic;
            color: #666;
        }

        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>QUOTATION</h1>
        </div>

        <div class="divider"></div>

        <table style="width: 100%;">
            <td>
                <div class="info-grid">
                    <div class="info-from">
                        <table>
                            <tr style="text-align: left; vertical-align: top;">
                                <td><strong>From:</strong></td>
                                <td>
                                    <div><strong>{{ $data['company']['name'] }}</strong></div>
                                    <div>{{ $data['branch']['address_1'] }}</div>
                                    <div>{{ $data['branch']['zip_code'] }}, {{ $data['branch']['city'] }}</div>
                                    <div>{{ $data['branch']['state'] }}, {{ $data['branch']['country'] }}</div>
                                    <div>{{ $data['branch']['email'] }} | {{ $data['branch']['phone'] }}</div>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="info-grid">
                    <div class="info-to">
                        <table>
                            <tr style="text-align: left; vertical-align: top;">
                                <td><strong>To:</strong></td>
                                <td>
                                    <div><strong>{{ $data['customer']['name'] }}</strong></div>
                                    <div>{{ $data['customer']['address_1'] }}</div>
                                    @if ($data['customer']['address_2'])
                                    <div>{{ $data['customer']['address_2'] }}</div>
                                    @endif
                                    <div>{{ $data['customer']['zip_code'] }}, {{ $data['customer']['city'] }}</div>
                                    <div>{{ $data['customer']['state'] }}, {{ $data['customer']['country'] }}</div>
                                    <div>{{ $data['customer']['email'] }} | {{ $data['customer']['phone'] }}</div>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </td>
            <td>
                <div class="info-number">
                    <div class="quotation-number">
                        #{{ $data['quotation_number'] }}
                    </div>
                    <table style="width: 100%">
                        <tr>
                            <td style="text-align: left;">Date:</td>
                            <td style="text-align: right;">{{ \Carbon\Carbon::parse($data['quotation_date'])->format('d-m-Y') }}</td>
                        </tr>
                        <tr>
                            <td style="text-align: left;">Status:</td>
                            <td style="text-align: right;">{{ $data['sale_status']['name'] }}</td>
                        </tr>
                    </table>
                </div>
            </td>
        </table>

        <div style="margin-top: 20px; font-weight: bold; text-transform: uppercase;">
            QUOTATION
        </div>

        <table class="info-table">
            <thead>
                <tr>
                    <th class="item-number">#</th>
                    <th>Description</th>
                    <th class="amount" style="white-space: nowrap;">Price</th>
                    <th class="amount" style="white-space: nowrap;">Qty</th>
                    <th class="amount" style="white-space: nowrap;">Discount (RM)</th>
                    <th class="amount" style="white-space: nowrap;">Tax (% / RM)</th>
                    <th class="amount" style="white-space: nowrap;">
                        <div>Total (RM)</div>
                        <div>(price * qty)</div>
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data['items'] as $item)
                <tr>
                    <td class="item-number">{{ $loop->iteration }}</td>
                    <td>
                        <div><strong>{{ $item['name'] }}</strong></div>
                        <div>{{ $item['description'] }}</div>
                    </td>
                    <td class="amount">{{ number_format($item['price'], 2) }}</td>
                    <td class="amount">{{ $item['quantity'] }}</td>
                    <td class="amount">{{ number_format($item['discount'], 2) }}</td>
                    <td class="amount">{{ number_format($item['tax_percentage'], 2) }} ({{ number_format($item['tax_amount'], 2) }})</td>
                    <td class="amount">{{ number_format($item['price'] * $item['quantity'], 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div style="margin-top: 10px;">
            <div style="font-weight: bold;">Notes:</div>
            <div>This is a computer-generated document. No signature is required.</div>
        </div>

        <table class="summary-table">
            <tr>
                <td class="label">Subtotal (RM):</td>
                <td class="amount">{{ number_format($data['total_amount'], 2) }}</td>
            </tr>
            <tr>
                <td class="label">Tax (RM):</td>
                <td class="amount">{{ number_format($data['tax_amount'], 2) }}</td>
            </tr>
            <tr>
                <td class="label">Discount (RM):</td>
                <td class="amount">{{ number_format($data['discount_amount'], 2) }}</td>
            </tr>
            <tr>
                <td class="label">Shipping (RM):</td>
                <td class="amount">{{ number_format($data['shipping_amount'], 2) }}</td>
            </tr>
            <tr class="total-row">
                <td class="label">Grand Total (RM):</td>
                <td class="amount">{{ number_format($data['grand_total'], 2) }}</td>
            </tr>
        </table>

        <div class="footer">
            <div>
                Quantum Solutions Inc. powered by Quantum ERP
            </div>
        </div>
    </div>
</body>

</html>