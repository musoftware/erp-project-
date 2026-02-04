from __future__ import annotations

from dataclasses import dataclass
from datetime import date

from erp_project.core.models import AuditInfo, Money


@dataclass(frozen=True)
class PurchaseOrderLine:
    sku: str
    quantity: int
    unit_cost: Money


@dataclass(frozen=True)
class PurchaseOrder:
    order_number: str
    vendor_id: str
    order_date: date
    lines: tuple[PurchaseOrderLine, ...]
    audit: AuditInfo
