<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use DB;
class RoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('rooms')->insert([
        ['name' => 'Standard', 'capacity' => 3, 'total_rooms' => 5],
        ['name' => 'Deluxe', 'capacity' => 3, 'total_rooms' => 5],
    ]);
    }
}
