<?php

namespace App\Http\Controllers\User;

use App\Helpers\Transformers\InvoiceTransformer;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreInvoiceItemRequest;
use App\Models\HRM\Company;
use App\Models\Sales\Invoice;
use App\Models\Sales\InvoiceItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvoiceItemController extends Controller
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

    public function store(StoreInvoiceItemRequest $request, Invoice $invoice)
    {
        if ($invoice->company_id != $this->company->id) {
            return response()->json([
                'message' => 'You are not authorized to create this invoice item.',
            ], 403);
        }

        DB::transaction(function () use ($invoice, $request) {
            $price = $request->price;
            $quantity = $request->quantity;
            $discount = $request->discount;
            $taxPercentage = $request->tax_percentage;
            $taxAmount = $price * $quantity * $taxPercentage / 100;
            $total = $price * $quantity - $discount + $taxAmount;

            $invoice->items()->create([
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

            $itemsTotal = $invoice->items()->sum('total') - $invoice->items()->sum('tax_amount');
            $itemsDiscount = $invoice->items()->sum('discount');
            $itemsTaxAmount = $invoice->items()->sum('tax_amount');

            $invoice->update([
                'total_amount' => $itemsTotal + $itemsDiscount,
                'discount_amount' => $itemsDiscount,
                'tax_amount' => $itemsTaxAmount,
                'grand_total' => $itemsTotal + $invoice->shipping_amount + $itemsTaxAmount,
            ]);
        });

        return response()->json([
            'message' => 'Invoice item created successfully',
            'invoice' => InvoiceTransformer::invoiceWithItems($invoice),
        ], 201);
    }

    public function update(StoreInvoiceItemRequest $request, InvoiceItem $invoiceItem)
    {
        if ($invoiceItem->invoice->company_id != $this->company->id) {
            return response()->json([
                'message' => 'You are not authorized to update this invoice item.',
            ], 403);
        }

        DB::transaction(function () use ($invoiceItem, $request) {
            $price = $request->price;
            $quantity = $request->quantity;
            $discount = $request->discount;
            $taxPercentage = $request->tax_percentage;
            $taxAmount = $price * $quantity * $taxPercentage / 100;
            $total = $price * $quantity - $discount + $taxAmount;

            $invoiceItem->update([
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

            $invoice = $invoiceItem->invoice;
            $itemsTotal = $invoice->items()->sum('total') - $invoice->items()->sum('tax_amount');
            $itemsDiscount = $invoice->items()->sum('discount');
            $itemsTaxAmount = $invoice->items()->sum('tax_amount');

            $invoice->update([
                'total_amount' => $itemsTotal + $itemsDiscount,
                'discount_amount' => $itemsDiscount,
                'tax_amount' => $itemsTaxAmount,
                'grand_total' => $itemsTotal + $invoice->shipping_amount + $itemsTaxAmount,
            ]);
        });

        return response()->json([
            'message' => 'Invoice item updated successfully',
            'invoice' => InvoiceTransformer::invoiceWithItems($invoiceItem->invoice),
        ], 200);
    }

    public function destroy(InvoiceItem $invoiceItem)
    {
        DB::transaction(function () use ($invoiceItem) {
            $invoiceItem->delete();

            $invoice = $invoiceItem->invoice;
            $itemsTotal = $invoice->items()->sum('total') - $invoice->items()->sum('tax_amount');
            $itemsDiscount = $invoice->items()->sum('discount');
            $itemsTaxAmount = $invoice->items()->sum('tax_amount');

            $invoice->update([
                'total_amount' => $itemsTotal + $itemsDiscount,
                'discount_amount' => $itemsDiscount,
                'tax_amount' => $itemsTaxAmount,
                'grand_total' => $itemsTotal + $invoice->shipping_amount + $itemsTaxAmount,
            ]);
        });

        return response()->json([
            'message' => 'Invoice item deleted successfully',
            'invoice' => InvoiceTransformer::invoiceWithItems($invoiceItem->invoice),
        ], 200);
    }
}
