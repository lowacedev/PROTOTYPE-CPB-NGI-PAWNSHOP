@extends('reports.pdf.layout')

@section('content')
    <div style="margin-bottom: 20px;">
        <p><strong>Period:</strong> {{ $startDate->format('M d, Y') }} to {{ $endDate->format('M d, Y') }}</p>
    </div>

    <h3 style="margin-top: 20px; margin-bottom: 10px;">Collection Summary</h3>
    <table>
        <thead>
            <tr>
                <th>Category</th>
                <th class="text-right">Amount</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Total Principal Collected</td>
                <td class="text-right">P {{ number_format($summaryData['total_principal'], 2) }}</td>
            </tr>
            <tr>
                <td>Total Interest Collected</td>
                <td class="text-right">P {{ number_format($summaryData['total_interest'], 2) }}</td>
            </tr>
            <tr>
                <td>Service Charges</td>
                <td class="text-right">P {{ number_format($summaryData['total_service_charge'], 2) }}</td>
            </tr>
            <tr>
                <td>Penalties</td>
                <td class="text-right">P {{ number_format($summaryData['total_penalty'], 2) }}</td>
            </tr>
            <tr>
                <td><strong>Net Collection</strong></td>
                <td class="text-right"><strong>P {{ number_format($summaryData['net_collection'], 2) }}</strong></td>
            </tr>
        </tbody>
    </table>

    <h3 style="margin-top: 30px; margin-bottom: 10px;">Transaction Types</h3>
    <table>
        <thead>
            <tr>
                <th>Transaction Type</th>
                <th class="text-right">Count</th>
                <th class="text-right">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transactionTypes as $type => $data)
            <tr>
                <td>{{ $type }}</td>
                <td class="text-right">{{ number_format($data['count']) }}</td>
                <td class="text-right">P {{ number_format($data['amount'], 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <h3 style="margin-top: 30px; margin-bottom: 10px;">Daily Breakdown</h3>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th class="text-right">Principal</th>
                <th class="text-right">Interest</th>
                <th class="text-right">Deductions</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($dateBreakdown as $date => $data)
            <tr>
                <td>{{ $date }}</td>
                <td class="text-right">P {{ number_format($data['principal'], 2) }}</td>
                <td class="text-right">P {{ number_format($data['interest'], 2) }}</td>
                <td class="text-right">P {{ number_format($data['deductions'], 2) }}</td>
                <td class="text-right"><strong>P {{ number_format($data['total'], 2) }}</strong></td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center">No payment data available.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
@endsection
