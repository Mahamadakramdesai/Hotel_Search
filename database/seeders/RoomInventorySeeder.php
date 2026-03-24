<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use DB;
class RoomInventorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */


    public function run()
    {
        $rooms = DB::table('rooms')->get();

        foreach ($rooms as $room) {
            for ($i = 0; $i < 30; $i++) {

                DB::table('room_inventories')->insert([
                    'room_id' => $room->id,
                    'date' => Carbon::today()->addDays($i),
                    'available_rooms' => 5,
                    'price' => rand(2000, 4000),
                ]);
            }
        }
    }
}
