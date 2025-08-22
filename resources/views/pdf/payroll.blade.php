<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payslip</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }

        .payslip-container {
            /* width: 800px; */
            margin: 0 auto;
            /* padding: 20px; */
        }

        .company-info,
        .employee-info,
        .summary-section {
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .net-pay {
            font-weight: bold;
            font-size: 1.2em;
            text-align: right;
        }
    </style>
</head>

<body>
    <div class="payslip-container">
        <div class="company-info">
            <h2>{{ $data['employee']['company']['name'] }}</h2>
            <div>{{ $data['employee']['branch']['name'] }}</div>
            <div>{{ $data['employee']['branch']['address_1'] }}</div>
            <div>{{ $data['employee']['branch']['city'] }},
                {{ $data['employee']['branch']['state'] }}
            </div>
            <div>{{ $data['employee']['branch']['zip_code'] }},
                {{ $data['employee']['branch']['country'] }}
            </div>
            <p>Phone : {{ $data['employee']['branch']['phone'] }}</p>
        </div>

        <div class="employee-info">
            <h3>Employee Details</h3>
            <table>
                <tr>
                    <th>Name</th>
                    <td>{{ $data['employee']['first_name'] . ' ' . $data['employee']['last_name'] }}</td>
                </tr>
                <tr>
                    <th>Employee ID</th>
                    <td>{{ $data['employee']['nric_number'] }}</td>
                </tr>
                <tr>
                    <th>Position</th>
                    <td>{{ $data['employee']['designation']['name'] }}</td>
                </tr>
                <tr>
                    <th>Pay Period</th>
                    <td>{{ $data['date'] }}</td>
                </tr>
            </table>
        </div>

        <h3>Earnings</h3>
        <table>
            <thead>
                <tr>
                    <th>Description</th>
                    <th style="text-align: right">Amount (RM)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Basic Salary</td>
                    <td style="text-align: right">{{ number_format($data['basic_salary'], 2) }}</td>
                </tr>
                @foreach ($data['allowances']['items'] as $allowance)
                    <tr>
                        <td>{{ $allowance['salary_type']['name'] }}</td>
                        <td style="text-align: right">{{ number_format($allowance['amount'], 2) }}</td>
                    </tr>
                @endforeach
                <tr>
                    <td><strong>Total Allowances</strong></td>
                    <td style="text-align: right"><strong>{{ number_format($data['allowances']['total'] + $data['basic_salary'], 2) }}</strong></td>
                </tr>
            </tbody>
        </table>

        <h3>Deductions</h3>
        <table>
            <thead>
                <tr>
                    <th>Description</th>
                    <th style="text-align: right">Amount (RM)</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data['deductions']['items'] as $deduction)
                    <tr>
                        <td>{{ $deduction['salary_type']['name'] }}</td>
                        <td style="text-align: right">{{ number_format($deduction['amount'], 2) }}</td>
                    </tr>
                @endforeach
                <tr>
                    <td><strong>Total Deductions</strong></td>
                    <td style="text-align: right"><strong>{{ number_format($data['deductions']['total'], 2) }}</strong></td>
                </tr>
            </tbody>
        </table>

        <div class="summary-section">
            <p class="net-pay"><strong>Net Pay:</strong> {{ number_format($data['total_salary'], 2) }}</p>
        </div>
    </div>
</body>

</html>