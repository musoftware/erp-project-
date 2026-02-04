<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    public function index()
    {
        return Purchase::query()
            ->with(['supplier', 'warehouse', 'items.product'])
            ->latest()
            ->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'warehouse_id' => ['required', 'exists:warehouses,id'],
            'number' => ['required', 'string', 'max:100', 'unique:purchases,number'],
            'status' => ['nullable', 'string', 'max:50'],
            'ordered_at' => ['nullable', 'date'],
            'received_at' => ['nullable', 'date'],
            'tax' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.001'],
            'items.*.unit_cost' => ['required', 'numeric', 'min:0'],
        ]);

        return DB::transaction(function () use ($data) {
            $items = $data['items'];
            unset($data['items']);

            $subtotal = 0;
            $itemRows = [];

            foreach ($items as $item) {
                $lineTotal = $item['quantity'] * $item['unit_cost'];
                $subtotal += $lineTotal;

                $itemRows[] = [
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_cost' => $item['unit_cost'],
                    'total' => $lineTotal,
                ];
            }

            $tax = $data['tax'] ?? 0;
            $purchase = Purchase::create([
                ...$data,
                'subtotal' => $subtotal,
                'tax' => $tax,
                'total' => $subtotal + $tax,
            ]);

            foreach ($itemRows as $row) {
                $purchase->items()->create($row);
            }

            return $purchase->load(['supplier', 'warehouse', 'items.product']);
        });
    }

    public function show(Purchase $purchase)
    {
        return $purchase->load(['supplier', 'warehouse', 'items.product']);
    }

    public function update(Request $request, Purchase $purchase)
    {
        $data = $request->validate([
            'supplier_id' => ['sometimes', 'required', 'exists:suppliers,id'],
            'warehouse_id' => ['sometimes', 'required', 'exists:warehouses,id'],
            'number' => ['sometimes', 'required', 'string', 'max:100', 'unique:purchases,number,' . $purchase->id],
            'status' => ['nullable', 'string', 'max:50'],
            'ordered_at' => ['nullable', 'date'],
            'received_at' => ['nullable', 'date'],
            'tax' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
            'items' => ['nullable', 'array', 'min:1'],
            'items.*.product_id' => ['required_with:items', 'exists:products,id'],
            'items.*.quantity' => ['required_with:items', 'numeric', 'min:0.001'],
            'items.*.unit_cost' => ['required_with:items', 'numeric', 'min:0'],
        ]);

        return DB::transaction(function () use ($purchase, $data) {
            $items = $data['items'] ?? null;
            unset($data['items']);

            $purchase->update($data);

            if ($items !== null) {
                $purchase->items()->delete();

                $subtotal = 0;
                foreach ($items as $item) {
                    $lineTotal = $item['quantity'] * $item['unit_cost'];
                    $subtotal += $lineTotal;

                    $purchase->items()->create([
                        'product_id' => $item['product_id'],
                        'quantity' => $item['quantity'],
                        'unit_cost' => $item['unit_cost'],
                        'total' => $lineTotal,
                    ]);
                }

                $tax = $data['tax'] ?? $purchase->tax;
                $purchase->update([
                    'subtotal' => $subtotal,
                    'tax' => $tax,
                    'total' => $subtotal + $tax,
                ]);
            }

            return $purchase->refresh()->load(['supplier', 'warehouse', 'items.product']);
        });
    }

    public function destroy(Purchase $purchase)
    {
        $purchase->delete();

        return response()->noContent();
    }
}
