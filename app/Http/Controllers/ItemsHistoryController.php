<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\ItemsHistory;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;

class ItemsHistoryController extends Controller
{
    use HttpResponses;

    public function show_item_history_by_quantity($item_id)
    {
        $item = Item::find($item_id);
        $itemHistory = ItemsHistory::where("item_id", $item_id)
            ->orderBy('created_at', 'desc')
            ->without("item")
            ->paginate(20);

        return $this->success([
            "item" => $item,
            "item_history" => $itemHistory
        ], message: "Quantity History of the Item : " . $item->name);
    }

    public function add_item_to_history($item_before, $request)
    {

        if ($item_before->quantity != $request->quantity) {

            $itemHistory = ItemsHistory::create([

                'item_id' => $item_before->id,
                'updated_quantity' => $request->quantity,

            ]);
        }
    }
}
