from __future__ import annotations

from dataclasses import replace

from erp_project.inventory.models import StockLevel


class InventoryService:
    """Basic inventory operations."""

    @staticmethod
    def receive_stock(level: StockLevel, quantity: int) -> StockLevel:
        return replace(level, quantity_on_hand=level.quantity_on_hand + quantity)

    @staticmethod
    def issue_stock(level: StockLevel, quantity: int) -> StockLevel:
        if quantity > level.quantity_on_hand:
            raise ValueError("Insufficient stock on hand")
        return replace(level, quantity_on_hand=level.quantity_on_hand - quantity)
