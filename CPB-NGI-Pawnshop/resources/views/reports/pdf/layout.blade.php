<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
            text-transform: uppercase;
        }
        .header p {
            margin: 5px 0 0 0;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        table th {
            background-color: #f4f4f4;
            font-weight: bold;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .summary {
            margin-top: 20px;
            text-align: right;
            font-size: 14px;
        }
        .summary strong {
            font-size: 16px;
        }
        .footer {
            margin-top: 30px;
            font-size: 10px;
            color: #999;
            text-align: center;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>CPB-NGI Pawnshop</h1>
        <p>{{ $title }}</p>
        <p>Run Date: {{ now()->format('M d, Y h:i A') }}</p>
        <p>Run By: {{ auth()->user()->name ?? 'System User' }}</p>
    </div>

    @yield('content')

    <div class="footer">
        CPB-NGI Pawnshop System &copy; {{ date('Y') }}
    </div>

</body>
</html>
