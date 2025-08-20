<?php

namespace App\Http\Controllers\User;

use App\Helpers\Transformers\QuotationTransformer;
use App\Http\Controllers\Controller;
use App\Models\HRM\Company;
use App\Models\Sales\QuotationItem;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\StoreQuotationItemRequest;
use App\Models\Sales\Quotation;

class QuotationItemController extends Controller
{
    protected Company $company;

    public function __construct()
    {
        $this->middleware('can:quotation.index')->only(['index']);
        $this->middleware('can:quotation.show')->only(['show']);
        $this->middleware('can:quotation.create')->only(['store']);
        $this->middleware('can:quotation.edit')->only(['update']);
        $this->middleware('can:quotation.destroy')->only(['destroy']);

        $this->middleware(function ($request, $next) {
            $this->company = auth()->user()->employee->company;
            if (is_null($this->company)) {
                return response()->json(['message' => 'User is not associated with a company.'], 403);
            }
            return $next($request);
        });
    }

    public function store(StoreQuotationItemRequest $request, Quotation $quotation)
    {
        if ($quotation->company_id != $this->company->id) {
            return response()->json([
                'message' => 'You are not authorized to create this quotation item.',
            ], 403);
        }

        DB::transaction(function () use ($quotation, $request) {
            // Calculate values once
            $price = $request->price;
            $quantity = $request->quantity;
            $discount = $request->discount;
            $taxPercentage = $request->tax_percentage;
            $taxAmount = $price * $quantity * $taxPercentage / 100;
            $total = $price * $quantity - $discount + $taxAmount;

            // Update quotation item directly
            $quotation->items()->create([
                'name' => $request->name,
                'type' => $request->type,
                'sku' => $request->sku,
                'description' => $request->description,
                'price' => $price,
                'discount' => $discount,
                'tax_percentage' => $taxPercentage,
                'tax_amount' => $taxAmount,
                'quantity' => $quantity,
                'total' => $total,
            ]);

            $itemsTotal = $quotation->items()->sum('total');
            $itemsDiscount = $quotation->items()->sum('discount');
            $itemsTaxAmount = $quotation->items()->sum('tax_amount');

            $quotation->update([
                'total_amount' => $itemsTotal + $itemsDiscount + $quotation->shipping_amount,
                'discount_amount' => $itemsDiscount,
                'tax_amount' => $itemsTaxAmount,
                'grand_total' => $itemsTotal + $quotation->shipping_amount,
            ]);
        });

        return response()->json([
            'message' => 'Quotation item created successfully',
            'quotation' => QuotationTransformer::quotationWithItems($quotation),
        ], 201);
    }

    public function update(StoreQuotationItemRequest $request, QuotationItem $quotationItem)
    {
        if ($quotationItem->quotation->company_id != $this->company->id) {
            return response()->json([
                'message' => 'You are not authorized to update this quotation item.',
            ], 403);
        }

        DB::transaction(function () use ($quotationItem, $request) {
            // Calculate values once
            $price = $request->price;
            $quantity = $request->quantity;
            $discount = $request->discount;
            $taxPercentage = $request->tax_percentage;
            $taxAmount = $price * $quantity * $taxPercentage / 100;
            $total = $price * $quantity - $discount + $taxAmount;

            // Update quotation item directly
            $quotationItem->update([
                'name' => $request->name,
                'type' => $request->type,
                'sku' => $request->sku,
                'description' => $request->description,
                'price' => $price,
                'discount' => $discount,
                'tax_percentage' => $taxPercentage,
                'tax_amount' => $taxAmount,
                'quantity' => $quantity,
                'total' => $total,
            ]);

            // Get quotation and update with a single query for each sum
            $quotation = $quotationItem->quotation;
            $itemsTotal = $quotation->items()->sum('total');
            $itemsDiscount = $quotation->items()->sum('discount');
            $itemsTaxAmount = $quotation->items()->sum('tax_amount');

            $quotation->update([
                'total_amount' => $itemsTotal + $itemsDiscount + $quotation->shipping_amount,
                'discount_amount' => $itemsDiscount,
                'tax_amount' => $itemsTaxAmount,
                'grand_total' => $itemsTotal + $quotation->shipping_amount,
            ]);
        });

        return response()->json([
            'message' => 'Quotation item updated successfully',
            'quotation' => QuotationTransformer::quotationWithItems($quotationItem->quotation),
        ], 200);
    }

    public function destroy(QuotationItem $quotationItem)
    {
        DB::transaction(function () use ($quotationItem) {
            $quotationItem->delete();

            $quotation = $quotationItem->quotation;
            $itemsTotal = $quotation->items()->sum('total');
            $itemsDiscount = $quotation->items()->sum('discount');
            $itemsTaxAmount = $quotation->items()->sum('tax_amount');

            $quotation->update([
                'total_amount' => $itemsTotal + $itemsDiscount + $quotation->shipping_amount,
                'discount_amount' => $itemsDiscount,
                'tax_amount' => $itemsTaxAmount,
                'grand_total' => $itemsTotal + $quotation->shipping_amount,
            ]);
        });

        return response()->json([
            'message' => 'Quotation item deleted successfully',
            'quotation' => QuotationTransformer::quotationWithItems($quotationItem->quotation),
        ], 200);
    }
}
