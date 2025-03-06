<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\ItemResource;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ItemGenerationController extends Controller
{

    public function generate()
    {
        $items = [];
        for ($i = 1; $i <= 1000; $i++) {
            $items[] = [
                'name' => 'Item ' . $i,
                'status' => $i % 2 === 0 ? 'Allowed' : 'Prohibited', // ~50/50 split
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        Item::insert($items); // Bulk insert for efficiency
        return response()->json(Item::all(), 200);
    }

    public function clear()
    {
        Item::truncate(); // Deletes all rows and resets auto-increment
        return response()->json([], 200); // Empty array signals success
    }
}
