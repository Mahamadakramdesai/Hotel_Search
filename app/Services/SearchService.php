<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\Room;
use App\Models\RoomInventory;

class SearchService
{
    public function search(array $data)
    {
        $checkIn = Carbon::parse($data['check_in']);
        $checkOut = Carbon::parse($data['check_out']);
        $guests=$data['guests'];
        $rooms = Room::with(['inventories' => function ($q) use ($checkIn, $checkOut) {
            $q->whereBetween('date', [$checkIn, $checkOut->copy()->subDay()]);
        }])->get();

        $results = [];

        foreach ($rooms as $room) {

            $inventories = $room->inventories;

            $minAvailable = $inventories->min('available_rooms');

            // Sold out
            if ($minAvailable <= 0) {
                $results[] = [
                    'room_type' => $room->name,
                    'availability' => [
                        'status' => 'sold_out'
                    ]
                ];
                continue;
            }
            $totalRoomOnly = $this->getRoomOnlyPrice($inventories->sum('price'),$guests);
            
            // Price calculation
            $totalPrice = $inventories->sum('price');
            $days = $checkIn->diffInDays($checkOut);

            // Add breakfast cost (example: ₹500 per night)
            $days = $checkIn->diffInDays($checkOut);
            $breakfastCost =$guests* 400 * $days;

           // $totalWithBreakfast = $totalPrice + $breakfastCost;
           $totalWithBreakfast = $this->getBreakfastPrice($totalRoomOnly, $guests);
            $discount = 0;

            // Long stay
            if ($days >= 3) {
                $discount += 10;
            }

            // Last minute
            if (now()->diffInDays($checkIn) <= 2) {
                $discount += 5;
            }

            $roomOnlyFinal = $totalRoomOnly - ($totalRoomOnly * $discount / 100);
            $breakfastFinal = $totalWithBreakfast - ($totalWithBreakfast * $discount / 100);


            $results[] = [
                'room_type' => $room->name,
                'availability' => [
                    'status' => 'available',
                    'rooms_left' => $minAvailable
                ],
                'meal_plans' => [
                    [
                        'type' => 'room_only',
                        'original_price' => $totalRoomOnly,
                        'final_price' => round($roomOnlyFinal),
                        'discount' => $discount
                    ],
                    [
                        'type' => 'breakfast',
                        'original_price' => $totalWithBreakfast,
                        'final_price' => round($breakfastFinal),
                        'discount' => $discount
                    ]
                ]
            ];
        }

        return $results;
    }

    private function getRoomOnlyPrice($basePrice, $guests)
    {
        switch ($guests) {
            case 1:
                return $basePrice ; // approx 1900/2850
            case 2:
                return $basePrice +500;
            case 3:
                return $basePrice+1000;
            default:
                return $basePrice;
        }
    }

    private function getBreakfastPrice($roomPrice, $guests)
    {
        switch ($guests) {
            case 1:
                return $roomPrice + 400;
            case 2:
                return $roomPrice + 800;
            case 3:
                return $roomPrice + 1200;
            default:
                return $roomPrice;
        }
    }
}
