<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    public function index()
    {
        return Sale::query()
            ->with(['customer', 'warehouse', 'items.product'])
            ->latest()
            ->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_id' => ['required', 'exists:customers,id'],
            'warehouse_id' => ['required', 'exists:warehouses,id'],
            'number' => ['required', 'string', 'max:100', 'unique:sales,number'],
            'status' => ['nullable', 'string', 'max:50'],
            'sold_at' => ['nullable', 'date'],
            'tax' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.001'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
        ]);

        return DB::transaction(function () use ($data) {
            $items = $data['items'];
            unset($data['items']);

            $subtotal = 0;
            foreach ($items as $item) {
                $subtotal += $item['quantity'] * $item['unit_price'];
            }

            $tax = $data['tax'] ?? 0;
            $sale = Sale::create([
                ...$data,
                'subtotal' => $subtotal,
                'tax' => $tax,
                'total' => $subtotal + $tax,
            ]);

            foreach ($items as $item) {
                $sale->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total' => $item['quantity'] * $item['unit_price'],
                ]);
            }

            return $sale->load(['customer', 'warehouse', 'items.product']);
        });
    }

    public function show(Sale $sale)
    {
        return $sale->load(['customer', 'warehouse', 'items.product']);
    }

    public function update(Request $request, Sale $sale)
    {
        $data = $request->validate([
            'customer_id' => ['sometimes', 'required', 'exists:customers,id'],
            'warehouse_id' => ['sometimes', 'required', 'exists:warehouses,id'],
            'number' => ['sometimes', 'required', 'string', 'max:100', 'unique:sales,number,' . $sale->id],
            'status' => ['nullable', 'string', 'max:50'],
            'sold_at' => ['nullable', 'date'],
            'tax' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
            'items' => ['nullable', 'array', 'min:1'],
            'items.*.product_id' => ['required_with:items', 'exists:products,id'],
            'items.*.quantity' => ['required_with:items', 'numeric', 'min:0.001'],
            'items.*.unit_price' => ['required_with:items', 'numeric', 'min:0'],
        ]);

        return DB::transaction(function () use ($sale, $data) {
            $items = $data['items'] ?? null;
            unset($data['items']);

            $sale->update($data);

            if ($items !== null) {
                $sale->items()->delete();

                $subtotal = 0;
                foreach ($items as $item) {
                    $lineTotal = $item['quantity'] * $item['unit_price'];
                    $subtotal += $lineTotal;

                    $sale->items()->create([
                        'product_id' => $item['product_id'],
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['unit_price'],
                        'total' => $lineTotal,
                    ]);
                }

                $tax = $data['tax'] ?? $sale->tax;
                $sale->update([
                    'subtotal' => $subtotal,
                    'tax' => $tax,
                    'total' => $subtotal + $tax,
                ]);
            }

            return $sale->refresh()->load(['customer', 'warehouse', 'items.product']);
        });
    }

    public function destroy(Sale $sale)
    {
        $sale->delete();

        return response()->noContent();
    }
}
