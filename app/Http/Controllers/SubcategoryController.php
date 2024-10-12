<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Subcategory;
use App\Traits\HttpResponses;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreSubcategoryRequest;
use App\Http\Requests\UpdateSubcategoryRequest;

class SubcategoryController extends Controller
{
    use HttpResponses;
    /**
     * Display a listing of the resource.
     */
    public function index($category_id)
    {
        $category = Category::find($category_id);
        $sub_categories = Subcategory::where("category_id", $category_id)->get();
        return $this->success([
            "subcategories" => $sub_categories/*->load("bill_cases", "client")*/,
        ], "$category->name Subcategories");
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSubcategoryRequest $request)
    {
        $category = Category::find($request->category_id);
        $request->validated($request->all());

        $sub_category = Subcategory::create([
            'name' => $request->name,
            'category_id' => $request->category_id,
        ]);

        return $this->success([
            "subcategory" => $sub_category->load("category"),
        ], "Subcategory " . $sub_category->name . " created successfully");
    }

    /**
     * Display the specified resource.
     */
    public function show($subcategory_id)
    {
        $subcategory = Subcategory::find($subcategory_id);
        return $this->success([
            "subcategory" => $subcategory->load("items")
        ], "subcategory " . $subcategory->name . " details");
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Subcategory $subcategory)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($subcategory_id, UpdateSubcategoryRequest $request)
    {
        $subcategory = Subcategory::find($subcategory_id);

        if (Auth::user()->type !== "admin") {
            return $this->error('', 'You are not Authorized to make this process', 403);
            // you should use the trait   (use HttpResponses ;) above

        }

        $subcategory->update($request->all());
        $subcategory->save();

        return $this->success([
            "subcategory" => $subcategory->load("category"),
        ], "subcategory " . $subcategory->name . " updated Successfully");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($subcategory_id)
    {
        $subcategory = Subcategory::find($subcategory_id);
        $subcategory_name = $subcategory->name;
        $subcategory->delete();

        return $this->success([], "subcategory " . $subcategory_name . " deleted Successfully");
    }
}
