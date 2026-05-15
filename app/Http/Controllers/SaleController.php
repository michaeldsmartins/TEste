<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\Client;
use App\Models\Product;
use App\Models\SaleItem;
use App\Models\Installment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SaleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = Sale::with(['client', 'seller']);

        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $sales = $query->latest()->paginate(10);
        $clients = Client::all();

        return view('sales.index', compact('sales', 'clients'));
    }

    public function create()
    {
        $clients = Client::all();
        $products = Product::all();
        return view('sales.create', compact('clients', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'payment_method' => 'required',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'installments' => 'required|array|min:1',
        ]);

        return DB::transaction(function () use ($request) {
            $sale = Sale::create([
                'user_id' => Auth::id(),
                'client_id' => $request->client_id,
                'total_amount' => $request->total_amount,
                'payment_method' => $request->payment_method,
            ]);

            foreach ($request->items as $item) {
                $product = Product::find($item['product_id']);
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $product->price,
                    'subtotal' => $item['quantity'] * $product->price,
                ]);
            }

            foreach ($request->installments as $installment) {
                Installment::create([
                    'sale_id' => $sale->id,
                    'due_date' => $installment['due_date'],
                    'amount' => $installment['amount'],
                ]);
            }

            return redirect()->route('sales.index')->with('success', 'Venda realizada com sucesso!');
        });
    }

    public function edit(Sale $sale)
    {
        $sale->load(['items', 'installments']);
        $clients = Client::all();
        $products = Product::all();
        return view('sales.edit', compact('sale', 'clients', 'products'));
    }

    public function update(Request $request, Sale $sale)
    {
        $request->validate([
            'payment_method' => 'required',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'installments' => 'required|array|min:1',
        ]);

        return DB::transaction(function () use ($request, $sale) {
            $sale->update([
                'client_id' => $request->client_id,
                'total_amount' => $request->total_amount,
                'payment_method' => $request->payment_method,
            ]);

            $sale->items()->delete();
            foreach ($request->items as $item) {
                $product = Product::find($item['product_id']);
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $product->price,
                    'subtotal' => $item['quantity'] * $product->price,
                ]);
            }

            $sale->installments()->delete();
            foreach ($request->installments as $installment) {
                Installment::create([
                    'sale_id' => $sale->id,
                    'due_date' => $installment['due_date'],
                    'amount' => $installment['amount'],
                ]);
            }

            return redirect()->route('sales.index')->with('success', 'Venda atualizada com sucesso!');
        });
    }

    public function destroy(Sale $sale)
    {
        $sale->delete();
        return redirect()->route('sales.index')->with('success', 'Venda excluída com sucesso!');
    }
}
