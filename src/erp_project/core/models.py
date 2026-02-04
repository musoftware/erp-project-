from __future__ import annotations

from dataclasses import dataclass
from datetime import datetime
from enum import Enum


class Currency(str, Enum):
    USD = "USD"
    EUR = "EUR"
    GBP = "GBP"


@dataclass(frozen=True)
class Money:
    amount: float
    currency: Currency = Currency.USD


@dataclass(frozen=True)
class Address:
    line_1: str
    city: str
    region: str
    postal_code: str
    country: str
    line_2: str | None = None


@dataclass(frozen=True)
class ContactInfo:
    name: str
    email: str
    phone: str | None = None


@dataclass(frozen=True)
class AuditInfo:
    created_at: datetime
    created_by: str
    updated_at: datetime | None = None
    updated_by: str | None = None
