# ERP Project

A lightweight, modular Enterprise Resource Planning (ERP) starter project. This repository provides a clean foundation with domain models and service scaffolding for common ERP modules such as inventory, sales, purchasing, HR, and finance.

## Goals
- **Modularity**: Keep business domains isolated and easy to extend.
- **Clarity**: Provide readable, testable data models and services.
- **Scalability**: Offer a structure that can grow into a full application.

## Project Structure
```
src/
  erp_project/
    core/        # Shared primitives and base models
    inventory/   # Inventory domain models and services
    sales/       # Sales domain models and services
    purchasing/  # Purchasing domain models and services
    hr/          # HR domain models and services
    finance/     # Finance domain models and services
```

## Getting Started
1. Ensure you have Python 3.10+.
2. Add your own service layer, persistence, and API endpoints on top of the provided domain models.

## Next Steps
- Add persistence (e.g., PostgreSQL) and migrations.
- Build APIs (e.g., FastAPI or Django REST Framework).
- Implement workflows and reporting.

## License
MIT
