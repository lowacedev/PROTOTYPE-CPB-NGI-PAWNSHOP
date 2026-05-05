@extends('reports.pdf.layout')

@section('content')
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Ticket #</th>
                <th>Customer Name</th>
                <th>Item(s)</th>
                <th>Status</th>
                <th class="text-right">Loan Amount</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $txn)
                <tr>
                    <td>{{ $txn->transaction_date->format('M d, Y') }}</td>
                    <td>{{ $txn->pawn_ticket_number }}</td>
                    <td>{{ $txn->customer->full_name }}</td>
                    <td>
                        @foreach($txn->items as $txnItem)
                            {{ $txnItem->item->name ?? 'N/A' }}<br>
                        @endforeach
                    </td>
                    <td>{{ $txn->status_label }}</td>
                    <td class="text-right">{{ number_format($txn->loan_amount, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">No records found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="summary">
        Total Loans Released: <strong>{{ number_format($totalLoanReleased, 2) }}</strong>
    </div>
@endsection
