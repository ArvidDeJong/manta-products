# Changelog

All notable changes to **darvis/manta-product** will be documented in this file.

## [1.0.0] - 2025-11-05

> **🚀 Production Release**: Stable production version with comprehensive documentation, gift card system, and enhanced functionality.

### Added
- **Gift Cards Documentation** - Complete documentation for gift card system (`docs/11-giftcards.md`)
- **Comprehensive Documentation Overhaul** - All documentation translated to English and significantly expanded
- **GitHub Templates** - Added bug report, feature request, and pull request templates
- **Contributing Guidelines** - Added CONTRIBUTING.md with development guidelines
- **Troubleshooting Guide** - Added comprehensive troubleshooting documentation (`docs/10-troubleshooting.md`)

### Enhanced
- **README.md** - Added badges, table of contents, quick start guide, and better structure
- **Configuration Documentation** - Expanded with examples and environment variables (`docs/02-configuration.md`)
- **Models Documentation** - Added detailed model relationships and properties (`docs/03-models.md`)
- **Usage Documentation** - Complete workflow examples and advanced use cases (`docs/04-usage.md`)
- **Variants Documentation** - Comprehensive variant matrix and management guide (`docs/05-variants.md`)
- **Unit Pricing Documentation** - Detailed pricing system with industry examples (`docs/06-unit-pricing.md`)
- **Extending Documentation** - Advanced extension patterns and best practices (`docs/07-extending.md`)

### Documentation
- All documentation now in English for international accessibility
- Added gift cards and shopping cart to features list
- Updated database overview to include gift card and cart tables
- Added links to all new documentation sections

## [0.1.1] - 2025-09-12
- Added resource_id on products + Product::resource()
- Added HasFactory to all models with package factories
- ReservationItem now has productVariant() relation
- AvailabilityService note updated
- README tables overview added

## [0.1.0] - 2025-09-12
- Initial release: products, attributes, attribute values
- Product variants with capacity/stock overrides
- Unit pricing (piece/meter/m²/m³) and dimensions (mm)
- Helper service to generate variant matrix
- Config publish & migrations
