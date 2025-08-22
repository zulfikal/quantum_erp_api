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
            margin: 10mm;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 10px;
            line-height: 1.5;
            color: #333;
            background: #fff;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        .container {
            width: 100%;
            max-width: 100%;
        }

        /* Modern header with accent color */
        .header {
            display: flex;
            justify-content: space-between;
            padding: 15px 0;
            border-bottom: 2px solid #3498db;
            margin-bottom: 20px;
            position: relative;
        }
        
        .header:after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 100%;
            height: 1px;
            background-color: #e8f4fc;
        }

        .company-info {
            flex: 2;
        }

        .company-name {
            font-size: 18px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 5px;
            letter-spacing: 0.5px;
            text-shadow: 0 1px 0 rgba(255,255,255,0.8);
        }

        .company-address {
            font-size: 9px;
            line-height: 1.3;
            color: #7f8c8d;
        }

        .payslip-info {
            flex: 1;
            text-align: right;
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
            padding: 10px;
            border-radius: 4px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .payslip-title {
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .pay-date {
            font-size: 11px;
            margin-top: 5px;
        }

        /* Two-column layout for employee info */
        .info-grid {
            display: flex;
            margin-bottom: 20px;
            background-color: #f9f9f9;
            border-radius: 4px;
            padding: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }

        .employee-section {
            flex: 1;
            padding-right: 10px;
        }

        .payment-info-section {
            flex: 1;
            padding-left: 10px;
        }

        .section-title {
            font-weight: bold;
            font-size: 11px;
            margin-bottom: 8px;
            color: #3498db;
            border-bottom: 1px solid #ecf0f1;
            padding-bottom: 4px;
            text-transform: uppercase;
            letter-spacing: 0.7px;
            position: relative;
        }
        
        .section-title:after {
            content: '';
            position: absolute;
            bottom: -1px;
            left: 0;
            width: 40px;
            height: 2px;
            background-color: #3498db;
        }

        .employee-details {
            font-size: 10px;
            line-height: 1.5;
        }

        .info-row {
            display: flex;
            margin-bottom: 3px;
        }

        .info-label {
            flex: 1;
            font-weight: bold;
            color: #7f8c8d;
        }

        .info-value {
            flex: 2;
        }

        /* Modern tables with subtle styling */
        .earnings-section, .deduction-section {
            margin-bottom: 12px;
        }

        .modern-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9px;
        }

        .modern-table th {
            background: linear-gradient(to bottom, #f8f9fa, #edf2f7);
            color: #3498db;
            padding: 7px 9px;
            text-align: left;
            font-weight: bold;
            border-bottom: 1px solid #e9ecef;
            text-transform: uppercase;
            font-size: 9.5px;
            letter-spacing: 0.5px;
        }

        .modern-table td {
            padding: 6px 9px;
            border-bottom: 1px solid #f1f1f1;
        }
        
        .modern-table tr:hover td {
            background-color: #f8f9fa;
        }

        .modern-table .amount-col {
            text-align: right;
            width: 80px;
        }

        .summary-row {
            background-color: #f8f9fa;
            font-weight: bold;
        }

        /* Net pay section with emphasis */
        .net-pay-section {
            margin-top: 20px;
            background: linear-gradient(135deg, #2c3e50, #1a2530);
            color: white;
            padding: 12px 15px;
            border-radius: 4px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 3px 6px rgba(0,0,0,0.1);
        }

        .net-pay-label {
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .net-pay-amount {
            font-size: 16px;
            font-weight: bold;
        }

        /* Footer */
        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #ecf0f1;
            font-size: 8px;
            color: #95a5a6;
            text-align: center;
            position: relative;
        }
        
        .footer:before {
            content: '';
            position: absolute;
            top: -2px;
            left: 0;
            width: 100%;
            height: 1px;
            background-color: #f9f9f9;
        }

        /* Subtle zebra striping for tables */
        .modern-table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        /* Watermark styling */
        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 80px;
            color: rgba(200, 200, 200, 0.1);
            pointer-events: none;
            z-index: -1;
            font-weight: bold;
            white-space: nowrap;
        }
        
        /* Responsive adjustments to ensure A4 fit */
        @media print {
            body {
                font-size: 9px;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            
            .company-name {
                font-size: 16px;
            }
            
            .payslip-title {
                font-size: 14px;
            }
            
            .net-pay-amount {
                font-size: 14px;
            }
            
            .container {
                page-break-inside: avoid;
            }
            
            .modern-table {
                page-break-inside: auto;
            }
            
            .modern-table tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }
            
            .info-grid {
                page-break-inside: avoid;
            }
            
            .header, .footer {
                page-break-inside: avoid;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Watermark -->
        <div class="watermark">PAYSLIP</div>
        <!-- Header -->
        <div class="header">
            <div class="company-info">
                <div class="company-name">{{ $data['employee']['company']['name'] ?? 'Company Name' }}</div>
                <div class="company-address">
                    {{ $data['employee']['branch']['name'] }}<br>
                    {{ $data['employee']['branch']['address_1'] }}
                    @if(isset($data['employee']['branch']['address_2']))
                    , {{ $data['employee']['branch']['address_2'] }}
                    @endif
                    <br>
                    {{ $data['employee']['branch']['zip_code'] }}, {{ $data['employee']['branch']['city'] }}, {{ $data['employee']['branch']['state'] }}
                </div>
            </div>
            <div class="payslip-info">
                <div class="payslip-title">Payslip</div>
                <div class="pay-date">{{ date('F Y', strtotime($data['date'])) }}</div>
            </div>
        </div>

        <!-- Two-column layout for employee and payment info -->
        <div class="info-grid">
            <!-- Employee Information -->
            <div class="employee-section">
                <div class="section-title">Employee Information</div>
                <div class="employee-details">
                    <div class="info-row">
                        <div class="info-label">Name:</div>
                        <div class="info-value">{{ $data['employee']['first_name'] }} {{ $data['employee']['last_name'] }}</div>
                    </div>
                    @if(isset($data['employee']['designation']['name']))
                    <div class="info-row">
                        <div class="info-label">Designation:</div>
                        <div class="info-value">{{ $data['employee']['designation']['name'] }}</div>
                    </div>
                    @endif
                    @if(isset($data['employee']['department']['name']))
                    <div class="info-row">
                        <div class="info-label">Department:</div>
                        <div class="info-value">{{ $data['employee']['department']['name'] }}</div>
                    </div>
                    @endif
                    @if(isset($data['employee']['email']))
                    <div class="info-row">
                        <div class="info-label">Email:</div>
                        <div class="info-value">{{ $data['employee']['email'] }}</div>
                    </div>
                    @endif
                    @if(isset($data['employee']['register_number']))
                    <div class="info-row">
                        <div class="info-label">Employee ID:</div>
                        <div class="info-value">{{ $data['employee']['register_number'] }}</div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Payment Information -->
            <div class="payment-info-section">
                <div class="section-title">Payment Details</div>
                <div class="employee-details">
                    <div class="info-row">
                        <div class="info-label">Pay Period:</div>
                        <div class="info-value">{{ date('F Y', strtotime($data['date'])) }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Basic Salary:</div>
                        <div class="info-value">$ {{ number_format($data['basic_salary'], 2) }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Total Allowances:</div>
                        <div class="info-value">$ {{ number_format($data['allowances']['total'], 2) }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Total Deductions:</div>
                        <div class="info-value">$ {{ number_format($data['deductions']['total'], 2) }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Earnings -->
        <div class="earnings-section">
            <div class="section-title">Earnings</div>
            <table class="modern-table">
                <thead>
                    <tr>
                        <th style="width: 50%;">Description</th>
                        <th class="amount-col">Rate</th>
                        <th class="amount-col">Amount</th>
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
                    <tr class="summary-row">
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
            <div class="section-title">Deductions</div>
            <table class="modern-table">
                <thead>
                    <tr>
                        <th style="width: 70%;">Description</th>
                        <th class="amount-col">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data['deductions']['items'] as $deduction)
                    <tr>
                        <td>{{ $deduction['salary_type']['name'] }}</td>
                        <td class="amount-col">$ {{ number_format($deduction['amount'], 2) }}</td>
                    </tr>
                    @endforeach
                    <tr class="summary-row">
                        <td><strong>Total Deductions</strong></td>
                        <td class="amount-col"><strong>$ {{ number_format($data['deductions']['total'], 2) }}</strong></td>
                    </tr>
                </tbody>
            </table>
        </div>
        @endif

        <!-- Net Pay -->
        <div class="net-pay-section">
            <div class="net-pay-label">Net Pay</div>
            <div class="net-pay-amount">$ {{ number_format($data['total_salary'], 2) }}</div>
        </div>

        <!-- Footer -->
        <div class="footer">
            This is a computer-generated document. No signature is required.
        </div>
    </div>
</body>

</html>