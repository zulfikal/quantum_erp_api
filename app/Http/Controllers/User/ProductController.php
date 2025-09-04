<?php

namespace App\Http\Controllers\User;

use App\Helpers\Constants\ProductStaticData;
use App\Helpers\Transformers\ProductTransformer;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Models\HRM\Company;
use App\Models\Product\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    protected Company $company;

    public function __construct()
    {
        $this->middleware('can:product.index')->only(['index']);
        $this->middleware('can:product.show')->only(['show']);
        $this->middleware('can:product.create')->only(['store']);
        $this->middleware('can:product.edit')->only(['update']);
        $this->middleware('can:product.edit')->only(['toggleIsActive']);
        $this->middleware('can:product.destroy')->only(['destroy']);

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
        $search = $request->input('search');
        $category = $request->input('category_id');
        $type = $request->input('type');

        $products = Product::where('company_id', $this->company->id)
            ->when($search, fn($query) => $query->where('name', 'like', "%{$search}%"))
            ->when($category, fn($query) => $query->where('category_id', $category))
            ->when($type, fn($query) => $query->whereHas('category', fn($query) => $query->where('type', $type)))
            ->with('category', 'company')
            ->paginate(25);

        $products->through(fn($product) => ProductTransformer::product($product));

        return response()->json([
            'constants' => [
                'categories' => ProductStaticData::categories(),
                'types' => ProductStaticData::types(),
            ],
            'statistics' => array_map('intval', (array) DB::table('products')
                ->where('company_id', $this->company->id)
                ->selectRaw('
                    COALESCE(COUNT(*), 0) as total,
                    COALESCE(SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END), 0) as active,
                    COALESCE(SUM(CASE WHEN is_active = 0 THEN 1 ELSE 0 END), 0) as inactive
                ')
                ->first()),
            'products' => $products,
        ], 200);
    }

    public function store(StoreProductRequest $request)
    {
        $product = $this->company->products()->create($request->validated());

        return response()->json([
            'message' => 'Product created successfully',
            'product' => ProductTransformer::product($product),
        ], 201);
    }

    public function show(Product $product)
    {
        if ($product->company_id != $this->company->id) {
            return response()->json([
                'message' => 'You are not authorized to view this product.',
            ], 403);
        }
        return response()->json(ProductTransformer::product($product), 200);
    }

    public function update(StoreProductRequest $request, Product $product)
    {
        if ($product->company_id != $this->company->id) {
            return response()->json([
                'message' => 'You are not authorized to update this product.',
            ], 403);
        }
        $product->update($request->validated());
        return response()->json([
            'message' => 'Product updated successfully',
            'product' => ProductTransformer::product($product->refresh()),
        ], 200);
    }

    public function toggleIsActive(Product $product)
    {
        if ($product->company_id != $this->company->id) {
            return response()->json([
                'message' => 'You are not authorized to update this product.',
            ], 403);
        }
        $product->is_active = !$product->is_active;
        $product->save();
        return response()->json([
            'message' => 'Product status is set to ' . ($product->is_active ? 'active' : 'inactive'),
            'product' => ProductTransformer::product($product->refresh()),
        ], 200);
    }

    public function globalProduct()
    {
        $products = Product::where('company_id', $this->company->id)
            ->with('category', 'company')
            ->latest()
            ->get();

        $products->transform(fn($product) => ProductTransformer::product($product));

        return response()->json([
            'products' => $products,
        ], 200);
    }
}
