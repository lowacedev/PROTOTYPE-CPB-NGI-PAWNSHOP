@extends('reports.pdf.layout')

@section('content')
    <p style="margin-bottom: 20px;"><strong>Period:</strong> {{ $startDate->format('M d, Y') }} to {{ $endDate->format('M d, Y') }}</p>
    
    <table>
        <thead>
            <tr>
                <th>Item Category</th>
                <th class="text-right">Beginning Balance</th>
                <th class="text-right">Added</th>
                <th class="text-right">Minus</th>
                <th class="text-right">Ending Balance</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalBeg = 0;
                $totalAdd = 0;
                $totalMinus = 0;
                $totalEnd = 0;
            @endphp
            @forelse($inventory as $row)
                @php
                    $totalBeg += $row['beg'];
                    $totalAdd += $row['add'];
                    $totalMinus += $row['minus'];
                    $totalEnd += $row['end'];
                @endphp
                <tr>
                    <td>{{ $row['category'] }}</td>
                    <td class="text-right">{{ number_format($row['beg']) }}</td>
                    <td class="text-right">+{{ number_format($row['add']) }}</td>
                    <td class="text-right">-{{ number_format($row['minus']) }}</td>
                    <td class="text-right"><strong>{{ number_format($row['end']) }}</strong></td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">No inventory data available.</td>
                </tr>
            @endforelse
            @if(count($inventory) > 0)
                <tr>
                    <td><strong>Total</strong></td>
                    <td class="text-right"><strong>{{ number_format($totalBeg) }}</strong></td>
                    <td class="text-right"><strong>+{{ number_format($totalAdd) }}</strong></td>
                    <td class="text-right"><strong>-{{ number_format($totalMinus) }}</strong></td>
                    <td class="text-right"><strong>{{ number_format($totalEnd) }}</strong></td>
                </tr>
            @endif
        </tbody>
    </table>
@endsection
