@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Editar Venda #{{ $sale->id }}</h5>
                </div>

                <div class="card-body">
                    <form action="{{ route('sales.update', $sale->id) }}" method="POST" id="saleForm">
                        @csrf
                        @method('PUT')
                        
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Cliente (Opcional)</label>
                                <select name="client_id" class="form-select select2" data-placeholder="Selecione um cliente...">
                                    <option value="">Selecione um cliente...</option>
                                    @foreach($clients as $client)
                                        <option value="{{ $client->id }}" {{ $sale->client_id == $client->id ? 'selected' : '' }}>{{ $client->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Forma de Pagamento</label>
                                <select name="payment_method" class="form-select" required>
                                    <option value="Dinheiro" {{ $sale->payment_method == 'Dinheiro' ? 'selected' : '' }}>Dinheiro</option>
                                    <option value="Cartão de Crédito" {{ $sale->payment_method == 'Cartão de Crédito' ? 'selected' : '' }}>Cartão de Crédito</option>
                                    <option value="Cartão de Débito" {{ $sale->payment_method == 'Cartão de Débito' ? 'selected' : '' }}>Cartão de Débito</option>
                                    <option value="Pix" {{ $sale->payment_method == 'Pix' ? 'selected' : '' }}>Pix</option>
                                </select>
                            </div>
                        </div>

                        <hr>
                        <h6 class="fw-bold mb-3">Itens da Venda</h6>
                        <div class="table-responsive">
                            <table class="table table-bordered" id="itemsTable">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 45%">Produto</th>
                                        <th style="width: 15%">Preço Unit.</th>
                                        <th style="width: 15%">Qtd.</th>
                                        <th style="width: 20%">Subtotal</th>
                                        <th style="width: 5%"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($sale->items as $key => $item)
                                    <tr class="item-row">
                                        <td>
                                            <select name="items[{{ $key }}][product_id]" class="form-select product-select select2" required data-placeholder="Selecione um produto...">
                                                <option value="">Selecione...</option>
                                                @foreach($products as $product)
                                                    <option value="{{ $product->id }}" data-price="{{ $product->price }}" {{ $item->product_id == $product->id ? 'selected' : '' }}>{{ $product->name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control unit-price" readonly value="{{ number_format($item->unit_price, 2, ',', '.') }}">
                                        </td>
                                        <td>
                                            <input type="number" name="items[{{ $key }}][quantity]" class="form-control quantity" value="{{ $item->quantity }}" min="1" required>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control subtotal" readonly value="{{ number_format($item->subtotal, 2, ',', '.') }}">
                                        </td>
                                        <td>
                                            @if($key > 0)
                                                <button type="button" class="btn btn-danger btn-sm removeItem">X</button>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="3" class="text-end fw-bold">Total Geral:</td>
                                        <td>
                                            <input type="text" id="total_display" class="form-control fw-bold border-0 bg-transparent" readonly value="R$ {{ number_format($sale->total_amount, 2, ',', '.') }}">
                                            <input type="hidden" name="total_amount" id="total_amount" value="{{ $sale->total_amount }}">
                                        </td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                            <button type="button" class="btn btn-outline-primary btn-sm mb-3" id="addItem">+ Adicionar Item</button>
                        </div>

                        <hr>
                        <div class="row align-items-center mb-3">
                            <div class="col-md-6">
                                <h6 class="fw-bold mb-0">Parcelamento</h6>
                            </div>
                            <div class="col-md-3">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">Nº Parcelas</span>
                                    <input type="number" id="num_installments" class="form-control" value="{{ $sale->installments->count() }}" min="1" max="12">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <button type="button" class="btn btn-secondary btn-sm w-100" id="generateInstallments">Gerar Parcelas</button>
                            </div>
                        </div>

                        <div id="installmentsContainer" class="row g-2 mb-4">
                            @foreach($sale->installments as $key => $inst)
                            <div class="col-md-4 installment-item">
                                <div class="p-2 border rounded bg-light">
                                    <label class="small fw-bold">Parcela {{ $key + 1 }}</label>
                                    <input type="date" name="installments[{{ $key }}][due_date]" class="form-control form-control-sm mb-1" value="{{ $inst->due_date }}">
                                    <input type="number" name="installments[{{ $key }}][amount]" class="form-control form-control-sm" value="{{ $inst->amount }}" step="0.01">
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">Salvar Alterações</button>
                            <a href="{{ route('sales.index') }}" class="btn btn-link text-muted">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        let itemCount = {{ $sale->items->count() }};

        // Inicializar Select2
        function initSelect2() {
            $('.select2').each(function() {
                if (!$(this).hasClass("select2-hidden-accessible")) {
                    $(this).select2({
                        theme: 'bootstrap-5',
                        placeholder: $(this).data('placeholder'),
                        allowClear: true,
                        width: '100%'
                    });
                }
            });
        }

        initSelect2();

        $('#addItem').click(function() {
            let newRow = $('.item-row:first').clone();
            
            // Limpar valores e remover vestígios do Select2 no clone
            newRow.find('select').val('');
            newRow.find('.quantity').val(1);
            newRow.find('.unit-price, .subtotal').val('0,00');
            newRow.find('.select2-container').remove();
            newRow.find('.select2').removeClass('select2-hidden-accessible').removeAttr('data-select2-id').find('option').removeAttr('data-select2-id');
            
            // Atualizar nomes com o novo índice
            newRow.find('select').attr('name', `items[${itemCount}][product_id]`);
            newRow.find('.quantity').attr('name', `items[${itemCount}][quantity]`);
            
            // Botão de remover
            newRow.find('td:last').html('<button type="button" class="btn btn-danger btn-sm removeItem">X</button>');
            
            $('#itemsTable tbody').append(newRow);
            
            // Inicializar Select2 apenas no novo campo clonado
            newRow.find('.select2').select2({
                theme: 'bootstrap-5',
                placeholder: newRow.find('.select2').data('placeholder'),
                allowClear: true,
                width: '100%'
            });
            
            itemCount++;
        });

        $(document).on('click', '.removeItem', function() {
            $(this).closest('tr').remove();
            calculateTotal();
        });

        // Atualizar preço e subtotal ao selecionar produto
        $(document).on('change', '.product-select', function() {
            let price = $(this).find(':selected').data('price') || 0;
            $(this).closest('tr').find('.unit-price').val(formatMoney(price));
            updateRowSubtotal($(this).closest('tr'));
        });

        $(document).on('input', '.quantity', function() {
            updateRowSubtotal($(this).closest('tr'));
        });

        function updateRowSubtotal(row) {
            let price = row.find('.product-select :selected').data('price') || 0;
            let qty = row.find('.quantity').val() || 0;
            let subtotal = price * qty;
            row.find('.subtotal').val(formatMoney(subtotal));
            calculateTotal();
        }

        function calculateTotal() {
            let total = 0;
            $('.item-row').each(function() {
                let price = $(this).find('.product-select :selected').data('price') || 0;
                let qty = $(this).find('.quantity').val() || 0;
                total += price * qty;
            });
            $('#total_display').val(formatMoney(total, true));
            $('#total_amount').val(total.toFixed(2));
        }

        $('#generateInstallments').click(function() {
            let total = parseFloat($('#total_amount').val());
            let num = parseInt($('#num_installments').val());
            if (total > 0 && num > 0) {
                generateInstallments(total, num);
            }
        });

        function generateInstallments(total, num) {
            $('#installmentsContainer').empty();
            let instAmount = (total / num).toFixed(2);
            let today = new Date();

            for (let i = 0; i < num; i++) {
                let dueDate = new Date(today);
                dueDate.setMonth(today.getMonth() + i);
                let dateStr = dueDate.toISOString().split('T')[0];

                let html = `
                    <div class="col-md-4 installment-item">
                        <div class="p-2 border rounded bg-light">
                            <label class="small fw-bold">Parcela ${i+1}</label>
                            <input type="date" name="installments[${i}][due_date]" class="form-control form-control-sm mb-1" value="${dateStr}">
                            <input type="number" name="installments[${i}][amount]" class="form-control form-control-sm" value="${instAmount}" step="0.01">
                        </div>
                    </div>
                `;
                $('#installmentsContainer').append(html);
            }
        }

        function formatMoney(value, prefix = false) {
            let formatted = value.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            return prefix ? 'R$ ' + formatted : formatted;
        }

        // Recálculo automático das parcelas
        $(document).on('input', 'input[name^="installments"][name$="[amount]"]', function() {
            let total = parseFloat($('#total_amount').val());
            let installments = $('input[name^="installments"][name$="[amount]"]');
            let index = installments.index(this);
            
            let sumPrevious = 0;
            for (let i = 0; i <= index; i++) {
                sumPrevious += parseFloat($(installments[i]).val()) || 0;
            }
            
            let remaining = total - sumPrevious;
            let countRemaining = installments.length - (index + 1);
            
            if (countRemaining > 0) {
                let amountPerRemaining = Math.max(0, (remaining / countRemaining)).toFixed(2);
                for (let i = index + 1; i < installments.length; i++) {
                    $(installments[i]).val(amountPerRemaining);
                }
            }
        });

        // Validação antes de enviar
        $('#saleForm').submit(function(e) {
            let total = parseFloat($('#total_amount').val());
            let sumInstallments = 0;
            
            $('input[name^="installments"][name$="[amount]"]').each(function() {
                sumInstallments += parseFloat($(this).val()) || 0;
            });

            if (Math.abs(total - sumInstallments) > 0.01) {
                e.preventDefault();
                alert('A soma das parcelas (R$ ' + sumInstallments.toFixed(2) + ') deve ser igual ao total da venda (R$ ' + total.toFixed(2) + ').');
            }
        });
    });
</script>
@endpush
@endsection
