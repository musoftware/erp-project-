<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\InventoryMovement;
use Illuminate\Http\Request;

class InventoryMovementController extends Controller
{
    public function index()
    {
        return InventoryMovement::query()
            ->with(['product', 'warehouse', 'user'])
            ->latest()
            ->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'warehouse_id' => ['required', 'exists:warehouses,id'],
            'user_id' => ['nullable', 'exists:users,id'],
            'type' => ['required', 'string', 'max:50'],
            'quantity' => ['required', 'numeric'],
            'unit_cost' => ['nullable', 'numeric'],
            'reference_type' => ['nullable', 'string', 'max:255'],
            'reference_id' => ['nullable', 'integer'],
            'note' => ['nullable', 'string'],
        ]);

        return InventoryMovement::create($data);
    }

    public function show(InventoryMovement $inventoryMovement)
    {
        return $inventoryMovement->load(['product', 'warehouse', 'user']);
    }

    public function update(Request $request, InventoryMovement $inventoryMovement)
    {
        $data = $request->validate([
            'product_id' => ['sometimes', 'required', 'exists:products,id'],
            'warehouse_id' => ['sometimes', 'required', 'exists:warehouses,id'],
            'user_id' => ['nullable', 'exists:users,id'],
            'type' => ['sometimes', 'required', 'string', 'max:50'],
            'quantity' => ['sometimes', 'required', 'numeric'],
            'unit_cost' => ['nullable', 'numeric'],
            'reference_type' => ['nullable', 'string', 'max:255'],
            'reference_id' => ['nullable', 'integer'],
            'note' => ['nullable', 'string'],
        ]);

        $inventoryMovement->update($data);

        return $inventoryMovement->refresh()->load(['product', 'warehouse', 'user']);
    }

    public function destroy(InventoryMovement $inventoryMovement)
    {
        $inventoryMovement->delete();

        return response()->noContent();
    }
}
