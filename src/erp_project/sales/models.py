from __future__ import annotations

from dataclasses import dataclass
from datetime import date

from erp_project.core.models import AuditInfo, Money


@dataclass(frozen=True)
class SalesOrderLine:
    sku: str
    quantity: int
    unit_price: Money


@dataclass(frozen=True)
class SalesOrder:
    order_number: str
    customer_id: str
    order_date: date
    lines: tuple[SalesOrderLine, ...]
    audit: AuditInfo
