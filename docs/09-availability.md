# Availability

Use `Manta\Products\Services\AvailabilityService::usedQuantity()` to sum reservation quantities and active holds for an interval.

Pseudo:
```php
$used = app(AvailabilityService::class)->usedQuantity(productId: $product->id, resourceId: null, start: $start, end: $end);
$isAvailable = $used < ($product->capacity ?? 1);
```
