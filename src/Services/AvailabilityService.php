<?php

namespace Manta\Products\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Manta\Products\Models\Hold;
use Manta\Products\Models\ReservationItem;

class AvailabilityService
{
    /**
     * Compute used quantity for a product or resource within an interval.
     * Returns integer used units (reservation quantities + active holds).
     */
    public function usedQuantity(int $productId = null, int $resourceId = null, Carbon $start, Carbon $end): int
    {
        $resQuery = ReservationItem::query()
            ->when($productId, fn($q)=>$q->where('product_id', $productId))
            ->when($resourceId, fn($q)=>$q) // resource-level checks can be implemented by your app's mapping
            ->where(function($q) use ($start, $end) {
                $q->whereBetween('starts_at', [$start, $end])
                  ->orWhereBetween('ends_at', [$start, $end])
                  ->orWhere(function($q2) use ($start, $end) {
                      $q2->where('starts_at', '<=', $start)->where('ends_at', '>=', $end);
                  });
            });

        $reserved = (int) $resQuery->sum('quantity');

        $holds = (int) Hold::query()
            ->when($productId, fn($q)=>$q->where('product_id', $productId))
            ->when($resourceId, fn($q)=>$q->where('resource_id', $resourceId))
            ->where('expires_at', '>', now())
            ->where(function($q) use ($start, $end) {
                $q->whereBetween('starts_at', [$start, $end])
                  ->orWhereBetween('ends_at', [$start, $end])
                  ->orWhere(function($q2) use ($start, $end) {
                      $q2->where('starts_at', '<=', $start)->where('ends_at', '>=', $end);
                  });
            })
            ->sum('quantity');

        return $reserved + $holds;
    }
}
