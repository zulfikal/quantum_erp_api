<?php

namespace App\Http\Controllers\User;

use App\Helpers\Constants\InvoiceStaticData;
use App\Helpers\Transformers\InvoiceTransformer;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreInvoiceRequest;
use App\Http\Requests\UpdateInvoiceRequest;
use App\Models\Sales\Invoice;
use App\Models\Sales\SaleStatus;
use App\Models\HRM\Company;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use stdClass;

class InvoiceController extends Controller
{
    protected Company $company;
    protected Invoice $invoice;

    public function __construct()
    {
        $this->middleware('can:invoice.index')->only(['index']);
        $this->middleware('can:invoice.show')->only(['show']);
        $this->middleware('can:invoice.create')->only(['store']);
        $this->middleware('can:invoice.edit')->only(['update']);
        $this->middleware('can:invoice.destroy')->only(['destroy']);

        $this->middleware(function ($request, $next) {
            $this->company = auth()->user()->employee->company;
            if (is_null($this->company)) {
                return response()->json(['message' => 'User is not associated with a company.'], 403);
            }
            return $next($request);
        });
    }

    private function getInvoiceStatusCounts(): array
    {
        $statuses = SaleStatus::whereIn('type', ['invoice', 'quotation_invoice'])->pluck('name', 'id')->toArray();
        // Get counts for all statuses in a single query
        $statusCounts = Invoice::where('company_id', $this->company->id)
            ->selectRaw('sale_status_id, COUNT(*) as count')
            ->groupBy('sale_status_id')
            ->pluck('count', 'sale_status_id')
            ->toArray();

        // Get total count
        $total = array_sum($statusCounts);

        // Initialize result with total count
        $result = [
            ['name' => 'Total', 'count' => $total]
        ];

        // Dynamically add all status counts using names from the database
        foreach ($statuses as $id => $name) {
            $result[] = ['name' => $name, 'count' => $statusCounts[$id] ?? 0];
        }

        return $result;
    }

    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');

        $invoices = Invoice::where('company_id', $this->company->id)
            ->when($search, fn($query) => $query->where('invoice_number', $search)
                ->orWhereHas('invoiceCustomer', function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%");
                }))
            ->when($status, fn($query) => $query->where('sale_status_id', $status))
            ->with('invoiceCustomer', 'items.product')->paginate(25);

        $invoices->through(fn($i) => InvoiceTransformer::invoice($i));

        return response()->json([
            'constants' => [
                'statuses' => InvoiceStaticData::statuses(),
            ],
            'statistics' => $this->getInvoiceStatusCounts(),
            'invoices' => $invoices,
        ], 200);
    }

    public function store(StoreInvoiceRequest $request)
    {
        $invoice = $request->validated()['invoice'];
        $customer = $request->validated()['customer'];
        $items = $request->validated()['items'];

        $invoiceItems = new Collection();

        foreach ($items as $item) {

            $taxAmount = $item['price'] * $item['quantity'] * $item['tax_percentage'] / 100;

            $tempItem = new stdClass();

            $tempItem->product_id = $item['product_id'];
            $tempItem->name = $item['name'];
            $tempItem->type = $item['type'];
            $tempItem->sku = $item['sku'];
            $tempItem->description = $item['description'];
            $tempItem->price = $item['price'];
            $tempItem->discount = $item['discount'];
            $tempItem->tax_percentage = $item['tax_percentage'];
            $tempItem->tax_amount = $taxAmount;
            $tempItem->quantity = $item['quantity'];
            $tempItem->total_amount = $item['price'] * $item['quantity'] + $taxAmount;
            $tempItem->total_after_discount = $item['price'] * $item['quantity'] - $item['discount'] + $taxAmount;

            $invoiceItems->push($tempItem);
        }


        DB::transaction(function () use ($invoice, $customer, $invoiceItems) {
            $invoiceNumber = $invoice['invoice_number'];

            if (is_null($invoiceNumber)) {
                $invoiceNumber = 'INV' . str_pad($this->company->id, 3, '0', STR_PAD_LEFT)  . str_pad(Invoice::where('company_id', $this->company->id)->count() + 1, 5, '0', STR_PAD_LEFT);
            }

            $create_invoice = Invoice::create([
                'company_id' => $this->company->id,
                'branch_id' => auth()->user()->employee->company_branch_id,
                'invoice_date' => $invoice['invoice_date'],
                'due_date' => $invoice['due_date'],
                'employee_id' => auth()->user()->employee->id,
                'invoice_number' => $invoiceNumber,
                'total_amount' => $invoiceItems->sum('total_amount') - $invoiceItems->sum('tax_amount'),
                'tax_amount' => $invoiceItems->sum('tax_amount'),
                'discount_amount' => $invoiceItems->sum('discount'),
                'grand_total' => $invoiceItems->sum('total_after_discount') + $invoice['shipping_amount'],
                'shipping_amount' => $invoice['shipping_amount'],
                'sale_status_id' => $invoice['sale_status_id'],
                'notes' => $invoice['notes'],
                'description' => $invoice['description'],
            ]);
            $create_invoice->invoiceCustomer()->create($customer);

            foreach ($invoiceItems as $item) {
                $create_invoice->items()->create([
                    'product_id' => $item->product_id,
                    'name' => $item->name,
                    'type' => $item->type,
                    'sku' => $item->sku,
                    'description' => $item->description,
                    'price' => $item->price,
                    'discount' => $item->discount,
                    'tax_percentage' => $item->tax_percentage,
                    'tax_amount' => $item->tax_amount,
                    'quantity' => $item->quantity,
                    'total' => $item->total_after_discount,
                ]);
            }

            $this->invoice = $create_invoice;
        });

        return response()->json([
            'message' => 'Invoice created successfully',
            'invoice' => InvoiceTransformer::invoiceWithItems($this->invoice),
        ], 201);
    }

    public function show(Invoice $invoice)
    {
        if ($invoice->company_id != $this->company->id) {
            return response()->json([
                'message' => 'You are not authorized to view this invoice',
            ], 403);
        }
        return response()->json([
            'invoice' => InvoiceTransformer::invoiceWithItems($invoice),
        ], 200);
    }

    public function update(UpdateInvoiceRequest $request, Invoice $invoice)
    {
        if ($invoice->company_id != $this->company->id) {
            return response()->json([
                'message' => 'You are not authorized to update this invoice',
            ], 403);
        }
        $invoice->invoiceCustomer()->update($request->validated()['customer']);

        $itemsTotal = $invoice->items()->sum('total') - $invoice->items()->sum('tax_amount');
        $itemsDiscount = $invoice->items()->sum('discount');
        $itemsTaxAmount = $invoice->items()->sum('tax_amount');

        $invoice->update([
            'total_amount' => $itemsTotal + $itemsDiscount,
            'shipping_amount' => $request->validated()['invoice']['shipping_amount'],
            'sale_status_id' => $request->validated()['invoice']['sale_status_id'],
            'invoice_date' => $request->validated()['invoice']['invoice_date'],
            'due_date' => $request->validated()['invoice']['due_date'],
            'notes' => $request->validated()['invoice']['notes'],
            'description' => $request->validated()['invoice']['description'],
            'discount_amount' => $itemsDiscount,
            'tax_amount' => $itemsTaxAmount,
            'grand_total' => $itemsTotal +  $request->validated()['invoice']['shipping_amount'] + $itemsTaxAmount,
        ]);


        return response()->json([
            'message' => 'Invoice updated successfully',
            'invoice' => InvoiceTransformer::invoiceWithItems($invoice),
        ], 200);
    }

    public function destroy(Invoice $invoice)
    {
        if ($invoice->company_id != $this->company->id) {
            return response()->json([
                'message' => 'You are not authorized to delete this invoice',
            ], 403);
        }

        DB::transaction(function () use ($invoice) {
            $invoice->delete();
            $invoice->invoiceCustomer()->delete();
        });

        return response()->json([
            'message' => 'Invoice deleted successfully',
        ], 200);
    }
}
