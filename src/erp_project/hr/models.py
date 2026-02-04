from __future__ import annotations

from dataclasses import dataclass
from datetime import date

from erp_project.core.models import Address, AuditInfo, ContactInfo, Money


@dataclass(frozen=True)
class Employee:
    employee_id: str
    name: str
    hire_date: date
    job_title: str
    salary: Money
    contact: ContactInfo
    address: Address
    audit: AuditInfo
