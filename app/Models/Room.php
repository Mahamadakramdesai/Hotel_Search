<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\RoomInventory;
class Room extends Model
{
    //

    public function inventories()
    {
        return $this->hasMany(RoomInventory::class);
    }
}
