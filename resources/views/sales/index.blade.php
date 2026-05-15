@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Listagem de Vendas</h5>
                    <a href="{{ route('sales.create') }}" class="btn btn-light btn-sm">Nova Venda</a>
                </div>

                <div class="card-body">
                    <form action="{{ route('sales.index') }}" method="GET" class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label">Cliente</label>
                            <select name="client_id" class="form-select select2">
                                <option value="">Todos</option>
                                @foreach($clients as $client)
                                    <option value="{{ $client->id }}" {{ request('client_id') == $client->id ? 'selected' : '' }}>
                                        {{ $client->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Desde</label>
                            <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Até</label>
                            <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">Filtrar</button>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Data</th>
                                    <th>Vendedor</th>
                                    <th>Cliente</th>
                                    <th>Total</th>
                                    <th>Pagamento</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($sales as $sale)
                                <tr>
                                    <td>#{{ $sale->id }}</td>
                                    <td>{{ $sale->created_at->format('d/m/Y H:i') }}</td>
                                    <td>{{ $sale->seller->name }}</td>
                                    <td>{{ $sale->client->name ?? 'N/A' }}</td>
                                    <td>R$ {{ number_format($sale->total_amount, 2, ',', '.') }}</td>
                                    <td>{{ $sale->payment_method }}</td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('sales.pdf', $sale->id) }}" class="btn btn-outline-danger btn-sm" title="Baixar PDF">
                                                <i class="bi bi-file-earmark-pdf"></i>
                                            </a>
                                            <a href="{{ route('sales.edit', $sale->id) }}" class="btn btn-outline-primary btn-sm" title="Editar">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <form action="{{ route('sales.destroy', $sale->id) }}" method="POST" onsubmit="return confirm('Tem certeza?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-secondary btn-sm" title="Excluir">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">Nenhuma venda encontrada.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $sales->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('.select2').select2({
            theme: 'bootstrap-5',
            placeholder: 'Selecione um cliente'
        });
    });
</script>
@endpush
