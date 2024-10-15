<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Http\Requests\StoreItemRequest;
use App\Http\Requests\UpdateItemRequest;
use App\Models\Category;
use App\Models\ItemsHistory;
use App\Models\Subcategory;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    use HttpResponses;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $items = Item::orderBy('created_at', 'desc')->paginate(20);
        $count = $items->count();
        // or: $items = Item::latest()->get();

        return $this->success([
            "items_count" => $count,
            "items" => $items
        ], "Items");
    }

    /**
     * Show the form for creating a new resource.
     */
    public function show_items_by_category_id($category_id)
    {
        $category = Category::find($category_id);

        $subcategories = Subcategory::where("category_id", $category_id)->pluck("id");
        $items = Item::whereIn("subcategory_id", $subcategories)->get();
        $count = $items->count();
        return $this->success([
            "items_count" => $count,
            "items" => $items,
        ], "Items for  category $category->name");
    }

    public function show_items_by_subcategory_id($subcategory_id)
    {
        $subcategory = Subcategory::find($subcategory_id);
        $items = Item::where("subcategory_id", $subcategory->id)->get();
        $count = $items->count();

        return $this->success([
            "items_count" => $count,
            "items" => $items,
        ], "Items for  subcategory $subcategory->name");
    }

    public function search(Request $request)
    {
        $query = Item::query();

        if (!$request->has('name')) {
            return $this->error(" please select what you are looking for", "No Search Results Found", 404);
        }

        $query = $query->where('name', 'like', '%' . $request->name . '%');

        $result = $query->get();

        $items_count = $result->count();

        if ($items_count == 0 /*$intersectedStatesResult->empty()*/) {
            return $this->error(" please try another search", "No Search Results Found", 404);
        }
        return $this->success([
            "count_of_items_found" => $items_count,
            "items" => $result,
        ], 'Searching done', 200);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreItemRequest $request)
    {
        $item = Item::create([

            'subcategory_id' => $request->subcategory_id,
            'name' => $request->name,
            'quantity' => $request->quantity,
            // 'unit_price' => $request->unit_price,

        ]);

        $itemHistory = ItemsHistory::create([

            'item_id' => $item->id,

            // 'updated_type' => "-",
            'updated_quantity' => $request->quantity,
            // 'updated_unit_price' => $request->unit_price,

        ]);

        return $this->success([
            // 'images by files' => $images,
            'item' => $item->load('subcategory'),
        ], "Item added successfully");
    }

    /**
     * Display the specified resource.
     */
    public function show($item_id)
    {
        $item = Item::find($item_id);
        return $this->success([
            "item" => $item,
        ], "$item->name details");
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateItemRequest $request, $item_id)
    {
        $item = Item::find($item_id);

        $testController = new ItemsHistoryController();
        $testController->add_item_to_history($item, $request);

        $item->update($request->all());
        $item->save();

        return $this->success([
            "item" => $item,
        ], "Item " . $item->name . " updated successfully");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($item_id)
    {
        $item = Item::findOrFail($item_id);
        $item_name = $item->name;
        $item->delete();
        return $this->success([], "Item $item_name deleted successfully");
    }
}
