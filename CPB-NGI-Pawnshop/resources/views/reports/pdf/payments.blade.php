@extends('reports.pdf.layout')

@section('content')
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Receipt #</th>
                <th>Ticket #</th>
                <th>Customer Name</th>
                <th>Type</th>
                <th class="text-right">Amount Paid</th>
            </tr>
        </thead>
        <tbody>
            @forelse($payments as $payment)
                <tr>
                    <td>{{ $payment->payment_date->format('M d, Y') }}</td>
                    <td>{{ $payment->receipt_number }}</td>
                    <td>{{ $payment->transaction->pawn_ticket_number }}</td>
                    <td>{{ $payment->transaction->customer->full_name }}</td>
                    <td>{{ $payment->payment_type_label }}</td>
                    <td class="text-right">{{ number_format($payment->amount_paid, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">No records found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="summary">
        Total Collected: <strong>{{ number_format($totalCollected, 2) }}</strong>
    </div>
@endsection
