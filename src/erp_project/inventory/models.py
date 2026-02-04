from __future__ import annotations

from dataclasses import dataclass
from datetime import datetime

from erp_project.core.models import AuditInfo, Money


@dataclass(frozen=True)
class StockItem:
    sku: str
    name: str
    description: str
    unit_cost: Money
    audit: AuditInfo


@dataclass(frozen=True)
class StockLevel:
    sku: str
    warehouse: str
    quantity_on_hand: int
    reorder_point: int
    last_counted_at: datetime
