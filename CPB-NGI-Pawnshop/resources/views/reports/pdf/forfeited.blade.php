@extends('reports.pdf.layout')

@section('content')
    <table>
        <thead>
            <tr>
                <th>Item Code</th>
                <th>Name</th>
                <th>Category</th>
                <th>Original Ticket #</th>
                <th>Maturity Date</th>
                <th>Current Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($items as $item)
                @php 
                    $latestTxn = $item->transactions->sortByDesc('created_at')->first(); 
                @endphp
                <tr>
                    <td>{{ $item->item_code }}</td>
                    <td>{{ $item->name }}</td>
                    <td>{{ $item->category->name ?? 'N/A' }}</td>
                    <td>{{ $latestTxn ? $latestTxn->pawn_ticket_number : 'N/A' }}</td>
                    <td>{{ $latestTxn ? $latestTxn->maturity_date->format('M d, Y') : 'N/A' }}</td>
                    <td>
                        @if($item->item_status === 'for_sale') For Sale
                        @elseif($item->item_status === 'sold') Sold
                        @else {{ ucfirst($item->item_status) }}
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">No records found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection
