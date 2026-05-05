<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt - {{ $sale->receipt_number }}</title>
    <style>
        body { font-family: 'Courier New', Courier, monospace; padding: 20px; max-width: 400px; margin: 0 auto; color: #000; background: #fff; }
        .receipt-header { text-align: center; margin-bottom: 20px; border-bottom: 1px dashed #000; padding-bottom: 10px; }
        .receipt-header h2 { margin: 0 0 5px 0; font-size: 20px; text-transform: uppercase; }
        .receipt-header p { margin: 0; font-size: 14px; }
        
        .receipt-details { margin-bottom: 15px; font-size: 14px; border-bottom: 1px dashed #000; padding-bottom: 10px; }
        .receipt-details p { margin: 3px 0; display: flex; justify-content: space-between; }
        
        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; font-size: 14px; }
        .items-table th { text-align: left; border-bottom: 1px dashed #000; padding-bottom: 5px; font-weight: normal; }
        .items-table th.right, .items-table td.right { text-align: right; }
        .items-table td { padding: 5px 0; vertical-align: top; }
        
        .item-name { max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .item-code { font-size: 11px; color: #555; }
        
        .totals-table { width: 100%; border-collapse: collapse; font-size: 14px; margin-bottom: 20px; }
        .totals-table td { padding: 3px 0; }
        .totals-table td.right { text-align: right; }
        .totals-table tr.grand-total td { font-weight: bold; font-size: 16px; border-top: 1px dashed #000; padding-top: 10px; }
        .totals-table tr.tendered td { padding-top: 10px; }
        
        .receipt-footer { text-align: center; margin-top: 20px; font-size: 14px; border-top: 1px dashed #000; padding-top: 15px; }
        
        .actions { margin-top: 30px; display: flex; gap: 10px; }
        .btn { flex: 1; padding: 12px; border: none; font-size: 16px; cursor: pointer; border-radius: 5px; text-align: center; text-decoration: none; font-weight: bold; font-family: sans-serif; }
        .btn-print { background: #3b82f6; color: white; }
        .btn-back { background: #6b7280; color: white; }
        
        @media print {
            .actions { display: none; }
            body { max-width: 100%; padding: 0; margin: 0; }
        }
    </style>
</head>
<body>
    <div class="receipt-header">
        <h2>CPB-NGI Pawnshop</h2>
        <p>San Pedro St, Poblacion District, Davao City</p>
        <p>Tel: 0920-426-2032</p>
    </div>

    <div class="receipt-details">
        <p><span>Receipt No:</span> <span>{{ $sale->receipt_number }}</span></p>
        <p><span>Date:</span> <span>{{ $sale->sold_at->format('Y-m-d H:i') }}</span></p>
        <p><span>Cashier:</span> <span>{{ $sale->user->name ?? 'N/A' }}</span></p>
    </div>

    <table class="items-table">
        <thead>
            <tr>
                <th>Item</th>
                <th class="right">Price</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sale->saleItems as $saleItem)
            <tr>
                <td>
                    <div class="item-name">{{ $saleItem->item->name }}</div>
                    <div class="item-code">{{ $saleItem->item->item_code }}</div>
                </td>
                <td class="right">{{ number_format($saleItem->price, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totals-table">
        <tr class="grand-total">
            <td>TOTAL DUE</td>
            <td class="right">{{ number_format($sale->total, 2) }}</td>
        </tr>
        <tr class="tendered">
            <td>CASH TENDERED</td>
            <td class="right">{{ number_format($sale->amount_tendered, 2) }}</td>
        </tr>
        <tr>
            <td>CHANGE</td>
            <td class="right">{{ number_format($sale->change, 2) }}</td>
        </tr>
    </table>

    <div class="receipt-footer">
        <p>Thank you for shopping with us!</p>
        <p>Please come again.</p>
    </div>

    <div class="actions">
        <button class="btn btn-print" onclick="window.print()">Print</button>
        <a href="{{ route('pos.index') }}" class="btn btn-back">Back to POS</a>
    </div>
</body>
</html>
