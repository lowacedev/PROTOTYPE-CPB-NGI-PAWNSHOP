@extends('reports.pdf.layout')

@section('content')
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Receipt #</th>
                <th>Items Sold</th>
                <th>Cashier</th>
                <th class="text-right">Total Amount</th>
            </tr>
        </thead>
        <tbody>
            @forelse($sales as $sale)
                <tr>
                    <td>{{ $sale->sold_at->format('M d, Y h:i A') }}</td>
                    <td>{{ $sale->receipt_number }}</td>
                    <td>
                        @foreach($sale->saleItems as $sItem)
                            {{ $sItem->item->name ?? 'N/A' }} ({{ number_format($sItem->price, 2) }})<br>
                        @endforeach
                    </td>
                    <td>{{ $sale->user->name ?? 'N/A' }}</td>
                    <td class="text-right">{{ number_format($sale->total, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">No records found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="summary">
        Total Sales: <strong>{{ number_format($totalSales, 2) }}</strong>
    </div>
@endsection
