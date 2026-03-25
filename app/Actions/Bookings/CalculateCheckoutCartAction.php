<?php

namespace App\Actions\Bookings;

use App\Models\RoomType;

class CalculateCheckoutCartAction
{
    /**
     * Compute selected rooms, subtotal from a payload array
     * (e.g. ['qty_1' => 2, 'qty_2' => 1])
     */
    public function execute(array $payload, int $nights): array
    {
        $selectedRooms = [];
        $subtotal = 0;

        foreach ($payload as $key => $value) {
            if (str_starts_with($key, 'qty_') && (int) $value > 0) {
                $rtId = (int) substr($key, 4);
                $rt   = RoomType::with(['images'])->find($rtId);
                if ($rt) {
                    $qty   = (int) $value;
                    $price = (float) $rt->daily_price;
                    $lineTotal = $price * $qty * $nights;
                    $selectedRooms[] = [
                        'room_type'  => $rt,
                        'qty'        => $qty,
                        'price'      => $price,
                        'line_total' => $lineTotal,
                    ];
                    $subtotal += $lineTotal;
                }
            }
        }

        return [
            'selectedRooms' => $selectedRooms,
            'subtotal'      => $subtotal,
        ];
    }
}
