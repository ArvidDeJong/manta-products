# Changelog

All notable changes to **darvis/manta-products** will be documented in this file.

## [0.1.0] - 2025-09-12
- Initial release: products, attributes, attribute values
- Product variants with capacity/stock overrides
- Unit pricing (piece/meter/m²/m³) and dimensions (mm)
- Helper service to generate variant matrix
- Config publish & migrations

## [0.1.1] - 2025-09-12
- Added resource_id on products + Product::resource()
- Added HasFactory to all models with package factories
- ReservationItem now has productVariant() relation
- AvailabilityService note updated
- README tables overview added
