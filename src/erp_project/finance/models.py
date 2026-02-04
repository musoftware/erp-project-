from __future__ import annotations

from dataclasses import dataclass
from datetime import date

from erp_project.core.models import AuditInfo, Money


@dataclass(frozen=True)
class LedgerEntry:
    entry_id: str
    entry_date: date
    account_code: str
    description: str
    debit: Money
    credit: Money
    audit: AuditInfo
