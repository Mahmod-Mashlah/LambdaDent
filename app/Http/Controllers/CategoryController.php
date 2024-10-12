<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Traits\HttpResponses;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;

class CategoryController extends Controller
{
    use HttpResponses;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::all();
        return $this->success([
            "categories" => $categories/*->load("bill_cases", "client")*/,
        ], "Categories");
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
    public function store(StoreCategoryRequest $request)
    {
        $request->validated($request->all());

        $category = Category::create([
            'name' => $request->name,
        ]);

        return $this->success([
            "category" => $category/*->load("bill_cases", "client")*/,
        ], "Category " . $category->name . " created successfully");
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        return $this->success([
            "category" => $category->load("subcategories"),
        ], "Category " . $category->name . " details");
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $category = Category::find($category->id);
        if (Auth::user()->type !== "admin") {
            return $this->error('', 'You are not Authorized to make this request', 403);
            // you should use the trait   (use HttpResponses ;) above

        }

        $category->update($request->all());
        $category->save();

        return $this->success([
            "category" => $category/*->load("bill_cases", "client")*/,
        ], "Category " . $category->name . " updated Successfully");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        $old_category = $category;
        $category->delete();

        return $this->success([
            "category" => $old_category/*->load("bill_cases", "client")*/,
        ], "Category " . $old_category->name . " deleted Successfully");
    }
}
