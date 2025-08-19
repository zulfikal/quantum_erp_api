<?php

namespace App\Http\Controllers\User;

use App\Helpers\Transformers\ProductTransformer;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductCategoryRequest;
use App\Models\HRM\Company;
use App\Models\Product\ProductCategory;
use Illuminate\Http\Request;

class ProductCategoryController extends Controller
{
    protected Company $company;

    public function __construct()
    {
        $this->middleware('can:product_category.index')->only(['index']);
        $this->middleware('can:product_category.show')->only(['show']);
        $this->middleware('can:product_category.create')->only(['store']);
        $this->middleware('can:product_category.edit')->only(['update']);
        $this->middleware('can:product_category.destroy')->only(['destroy']);

        $this->middleware(function ($request, $next) {
            $this->company = auth()->user()->employee->company;
            if (is_null($this->company)) {
                return response()->json(['message' => 'User is not associated with a company.'], 403);
            }
            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $category = $request->category;
        $search = $request->search;

        $categories = ProductCategory::where('company_id', $this->company->id)
            ->when($category, function ($query) use ($category) {
                $query->where('type', $category);
            })
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->get();
        $categories->transform(fn($category) => ProductTransformer::category($category));
        return response()->json([
            'categories' => $categories,
        ], 200);
    }

    public function store(StoreProductCategoryRequest $request)
    {
        $category = $this->company->productCategories()->create($request->validated());

        return response()->json([
            'message' => 'Category created successfully',
            'category' => ProductTransformer::category($category),
        ], 201);
    }

    public function update(StoreProductCategoryRequest $request, ProductCategory $category)
    {
        if ($category->company_id != $this->company->id) {
            return response()->json([
                'message' => 'You are not authorized to update this category.',
            ], 403);
        }
        $category->update($request->validated());
        return response()->json([
            'message' => 'Category updated successfully',
            'category' => ProductTransformer::category($category->refresh()),
        ], 200);
    }

    public function destroy(ProductCategory $category)
    {
        if ($category->company_id != $this->company->id) {
            return response()->json([
                'message' => 'You are not authorized to delete this category.',
            ], 403);
        }
        $category->delete();
        return response()->json([
            'message' => 'Category deleted successfully.',
        ], 200);
    }
}
