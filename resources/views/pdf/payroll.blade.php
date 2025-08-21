<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payslip</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        @page {
            size: A4;
            margin: 15mm 10mm;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #000;
            background: #fff;
            padding: 10px;
        }

        .container {
            width: 100%;
            padding: 0;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 20px;
            border-bottom: 1px solid #000;
            padding-bottom: 10px;
        }

        .company-info {
            flex: 1;
        }

        .company-name {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 3px;
        }

        .company-address {
            font-size: 9px;
            line-height: 1.2;
            color: #333;
        }

        .payslip-info {
            text-align: right;
        }

        .payslip-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .pay-date {
            font-size: 11px;
        }

        .employee-section {
            margin-bottom: 20px;
        }

        .section-title {
            font-weight: bold;
            font-size: 11px;
            margin-bottom: 8px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 2px;
        }

        .employee-details {
            font-size: 10px;
            line-height: 1.3;
        }

        .earnings-section {
            margin-bottom: 15px;
        }

        .earnings-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
        }

        .earnings-table th {
            background-color: #f0f0f0;
            border: 1px solid #000;
            padding: 5px 8px;
            text-align: left;
            font-weight: bold;
        }

        .earnings-table td {
            border: 1px solid #000;
            padding: 4px 8px;
        }

        .earnings-table .amount-col {
            text-align: right;
            width: 80px;
        }

        .gross-pay-row {
            background-color: #f0f0f0;
            font-weight: bold;
        }

        .deduction-section {
            margin-bottom: 15px;
        }

        .deduction-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
        }

        .deduction-table th {
            background-color: #f0f0f0;
            border: 1px solid #000;
            padding: 5px 8px;
            text-align: left;
            font-weight: bold;
        }

        .deduction-table td {
            border: 1px solid #000;
            padding: 4px 8px;
        }

        .deduction-table .amount-col {
            text-align: right;
            width: 80px;
        }

        .total-deduction-row {
            background-color: #f0f0f0;
            font-weight: bold;
        }

        .net-pay-section {
            margin-top: 10px;
            text-align: center;
        }

        .net-pay-table {
            width: 100%;
            border-collapse: collapse;
        }

        .net-pay-table td {
            border: 2px solid #000;
            padding: 8px;
            font-weight: bold;
            font-size: 12px;
            text-align: center;
        }

        .net-pay-amount {
            font-size: 14px;
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="company-info">
                <div class="company-name">{{ $data['employee']['company']['name'] ?? 'Company Name' }}</div>
                <div class="company-address">
                    {{ $data['employee']['branch']['name'] }}<br>
                    {{ $data['employee']['branch']['address_1'] }}<br>
                    @if(isset($data['employee']['branch']['address_2']))
                        {{ $data['employee']['branch']['address_2'] }}<br>
                    @endif
                    {{ $data['employee']['branch']['zip_code'] }},{{ $data['employee']['branch']['city'] }}, {{ $data['employee']['branch']['state'] }}
                </div>
            </div>
            <div class="payslip-info">
                <div class="payslip-title">Payslip</div>
                <div class="pay-date">{{ date('d-m-Y', strtotime($data['date'])) }}</div>
            </div>
        </div>

        <!-- Employee Information -->
        <div class="employee-section">
            <div class="section-title">Employee Info</div>
            <div class="employee-details">
                {{ $data['employee']['first_name'] }} {{ $data['employee']['last_name'] }}<br>
                @if(isset($data['employee']['designation']['name']))
                {{ $data['employee']['designation']['name'] }}<br>
                @endif
                @if(isset($data['employee']['department']['name']))
                {{ $data['employee']['department']['name'] }}<br>
                @endif
                @if(isset($data['employee']['email']))
                {{ $data['employee']['email'] }}<br>
                @endif
                @if(isset($data['employee']['register_number']))
                {{ $data['employee']['register_number'] }}
                @endif
            </div>
        </div>

        <!-- Earnings -->
        <div class="earnings-section">
            <div class="section-title">Earning</div>
            <table class="earnings-table">
                <thead>
                    <tr>
                        <th style="width: 200px;"></th>
                        <th class="amount-col">Rate</th>
                        <th class="amount-col">Current</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Basic Salary</td>
                        <td class="amount-col">{{ number_format($data['basic_salary'], 2) }}</td>
                        <td class="amount-col">$ {{ number_format($data['basic_salary'], 2) }}</td>
                    </tr>
                    @foreach($data['allowances']['items'] as $allowance)
                    <tr>
                        <td>{{ $allowance['salary_type']['name'] }}</td>
                        <td class="amount-col">{{ number_format($allowance['amount'], 2) }}</td>
                        <td class="amount-col">$ {{ number_format($allowance['amount'], 2) }}</td>
                    </tr>
                    @endforeach
                    <tr class="gross-pay-row">
                        <td><strong>Gross Pay</strong></td>
                        <td class="amount-col"></td>
                        <td class="amount-col"><strong>$ {{ number_format($data['basic_salary'] + $data['allowances']['total'], 2) }}</strong></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Deductions -->
        @if($data['deductions']['items']->count() > 0)
        <div class="deduction-section">
            <div class="section-title">Deduction</div>
            <table class="deduction-table">
                <thead>
                    <tr>
                        <th style="width: 280px;"></th>
                        <th class="amount-col">Current</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data['deductions']['items'] as $deduction)
                    <tr>
                        <td>{{ $deduction['salary_type']['name'] }}</td>
                        <td class="amount-col">$ {{ number_format($deduction['amount'], 2) }}</td>
                    </tr>
                    @endforeach
                    <tr class="total-deduction-row">
                        <td><strong>Total Deduction</strong></td>
                        <td class="amount-col"><strong>$ {{ number_format($data['deductions']['total'], 2) }}</strong></td>
                    </tr>
                </tbody>
            </table>
        </div>
        @endif

        <!-- Net Pay -->
        <div class="net-pay-section">
            <table class="net-pay-table">
                <tr>
                    <td>Net Pay</td>
                    <td class="net-pay-amount">$ {{ number_format($data['total_salary'], 2) }}</td>
                </tr>
            </table>
        </div>
    </div>
</body>

</html>