<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\ItemsHistory;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;

class ItemsHistoryController extends Controller
{
    use HttpResponses;
    public function show_item_history($item_id)
    {
        $item = Item::find($item_id);
        $itemHistory = ItemsHistory::where("item_id", $item_id)
            ->orderBy('created_at', 'desc')
            ->without("item")
            ->paginate(20);

        return $this->success([
            "item" => $item,
            "item_history" => $itemHistory
        ], "History of the Item : " . $item->name);
    }
    public function show_item_history_by_quantity($item_id)
    {
        $item = Item::find($item_id);
        $itemHistory = ItemsHistory::where("item_id", $item_id)
            ->whereIn("updated_type", ['-', 'الكمّيّة'])
            ->orderBy('created_at', 'desc')
            ->without("item")
            ->paginate(20);

        return $this->success([
            "item" => $item,
            "item_history" => $itemHistory
        ], message: "History of the Item : " . $item->name . " by quantity");
    }
    public function show_item_history_by_unit_price($item_id)
    {
        $item = Item::find($item_id);
        $itemHistory = ItemsHistory::where("item_id", $item_id)
            ->whereIn("updated_type", ['-', 'السعر'])
            ->orderBy('created_at', 'desc')
            ->without("item")
            ->paginate(20);

        return $this->success([
            "item" => $item,
            "item_history" => $itemHistory
        ], message: "History of the Item : " . $item->name . " by unit-price");
    }
    public function add_item_to_history($item_before, $request)
    {

        if (($item_before->quantity != $request->quantity) && ($item_before->unit_price != $request->unit_price)) {

            $itemHistory = ItemsHistory::create([

                'item_id' => $item_before->id,

                'updated_type' => "السعر",
                'updated_quantity' => $item_before->quantity,
                'updated_unit_price' => $request->unit_price,

            ]);

            $itemHistory = ItemsHistory::create([

                'item_id' => $item_before->id,

                'updated_type' => "الكمّيّة",
                'updated_quantity' => $request->quantity,
                'updated_unit_price' => $item_before->unit_price,

            ]);
        }

        if (($item_before->quantity == $request->quantity) && ($item_before->unit_price != $request->unit_price)) {

            $itemHistory = ItemsHistory::create([

                'item_id' => $item_before->id,

                'updated_type' => "السعر",
                'updated_quantity' => $item_before->quantity,
                'updated_unit_price' => $request->unit_price,

            ]);
        }

        if (($item_before->quantity != $request->quantity) && ($item_before->unit_price == $request->unit_price)) {

            $itemHistory = ItemsHistory::create([

                'item_id' => $item_before->id,

                'updated_type' => "الكمّيّة",
                'updated_quantity' => $request->quantity,
                'updated_unit_price' => $item_before->unit_price,

            ]);
        }
    }
}
