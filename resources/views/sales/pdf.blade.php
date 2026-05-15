<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Resumo da Venda #{{ $sale->id }}</title>
    <style>
        body { font-family: sans-serif; font-size: 14px; }
        .header { text-align: center; margin-bottom: 30px; }
        .section { margin-bottom: 20px; }
        .section-title { font-weight: bold; border-bottom: 1px solid #ddd; padding-bottom: 5px; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        th, td { padding: 8px; border: 1px solid #ddd; text-align: left; }
        th { background-color: #f8f9fa; }
        .total { text-align: right; font-size: 18px; font-weight: bold; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Resumo da Venda</h1>
        <p>Venda #{{ $sale->id }} | Data: {{ $sale->created_at->format('d/m/Y H:i') }}</p>
    </div>

    <div class="section">
        <div class="section-title">Informações Gerais</div>
        <p><strong>Vendedor:</strong> {{ $sale->seller->name }}</p>
        <p><strong>Cliente:</strong> {{ $sale->client->name ?? 'Não informado' }}</p>
        <p><strong>Forma de Pagamento:</strong> {{ $sale->payment_method }}</p>
    </div>

    <div class="section">
        <div class="section-title">Itens da Venda</div>
        <table>
            <thead>
                <tr>
                    <th>Produto</th>
                    <th>Qtd.</th>
                    <th>Vlr Unit.</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sale->items as $item)
                <tr>
                    <td>{{ $item->product->name }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>R$ {{ number_format($item->unit_price, 2, ',', '.') }}</td>
                    <td>R$ {{ number_format($item->subtotal, 2, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Cronograma de Parcelas</div>
        <table>
            <thead>
                <tr>
                    <th>Parcela</th>
                    <th>Vencimento</th>
                    <th>Valor</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sale->installments as $key => $inst)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ \Carbon\Carbon::parse($inst->due_date)->format('d/m/Y') }}</td>
                    <td>R$ {{ number_format($inst->amount, 2, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="total">
        TOTAL: R$ {{ number_format($sale->total_amount, 2, ',', '.') }}
    </div>
</body>
</html>
