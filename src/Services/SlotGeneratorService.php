<?php

namespace Manta\Products\Services;

use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Collection;
use Manta\Products\Models\OpeningHour;
use Manta\Products\Models\CalendarException;
use Manta\Products\Services\AvailabilityService;
use Manta\Products\Models\Product;

class SlotGeneratorService
{
    public function __construct(protected AvailabilityService $availability) {}

    /**
     * Generate slots for a product between start and end (dates), respecting opening hours,
     * exceptions, capacity and product block_size/time_unit.
     *
     * @return array<int, array{start: string, end: string, available: bool, used: int, capacity: int}>
     */
    public function slots(Product $product, Carbon $fromDate, Carbon $toDate): array
    {
        $slots = [];
        $capacity = $product->capacity ?? 1;
        $unit = $product->time_unit ?? 'minute';
        $block = max((int)($product->block_size ?? 15), 1);

        $days = CarbonPeriod::create($fromDate->copy()->startOfDay(), $toDate->copy()->endOfDay())->toArray();

        foreach ($days as $day) {
            $weekday = (int) $day->isoWeekday(); // 1..7
            // Opening hours bound to product
            $hours = OpeningHour::query()
                ->where('owner_type', 'products')
                ->where('owner_id', $product->id)
                ->where('weekday', $weekday)
                ->get();

            if ($hours->isEmpty()) {
                // whole day closed unless time_unit is day and you want generic day booking
                if ($unit === 'day') {
                    $start = $day->copy()->startOfDay();
                    $end = (clone $start)->addDays($block);
                    $used = $this->availability->usedQuantity($product->id, null, $start, $end);
                    $slots[] = [
                        'start' => $start->toIso8601String(),
                        'end'   => $end->toIso8601String(),
                        'available' => $used < $capacity,
                        'used'      => $used,
                        'capacity'  => $capacity,
                    ];
                }
                continue;
            }

            // Check exceptions (closed/full/partial)
            $exceptions = CalendarException::query()
                ->where('owner_type', 'products')
                ->where('owner_id', $product->id)
                ->whereDate('date', $day->toDateString())
                ->get();

            foreach ($hours as $h) {
                $start = $day->copy()->setTimeFromTimeString($h->start_time);
                $end   = $day->copy()->setTimeFromTimeString($h->end_time);

                if ($unit === 'day') {
                    // Single all-day slot within opening window
                    $used = $this->availability->usedQuantity($product->id, null, $start, $end);
                    $closed = $exceptions->firstWhere('is_closed', true);
                    if ($closed) continue;

                    $slots[] = [
                        'start' => $start->toIso8601String(),
                        'end'   => $end->toIso8601String(),
                        'available' => $used < $capacity,
                        'used'      => $used,
                        'capacity'  => $capacity,
                    ];
                } else {
                    // minute-based -> slice into block-size steps
                    $cursor = $start->copy();
                    while ($cursor->lt($end)) {
                        $slotStart = $cursor->copy();
                        $slotEnd = $cursor->copy()->addMinutes($block);

                        // Skip if overlaps a closed exception period
                        $skip = false;
                        foreach ($exceptions as $ex) {
                            if ($ex->is_closed) {
                                $exStart = $ex->start_time ? $day->copy()->setTimeFromTimeString($ex->start_time) : $day->copy()->startOfDay();
                                $exEnd   = $ex->end_time ? $day->copy()->setTimeFromTimeString($ex->end_time) : $day->copy()->endOfDay();
                                if ($slotStart < $exEnd && $slotEnd > $exStart) {
                                    $skip = True; break;
                                }
                            }
                        }
                        if ($skip) { $cursor->addMinutes($block); continue; }

                        $used = $this->availability->usedQuantity($product->id, null, $slotStart, $slotEnd);
                        $slots.append({
                            'start': $slotStart.toIso8601String(),
                            'end': $slotEnd.toIso8601String(),
                            'available': $used < $capacity,
                            'used': $used,
                            'capacity': $capacity,
                        })
                        $cursor->addMinutes($block);
                    }
                }
            }
        }
        return $slots;
    }
}
