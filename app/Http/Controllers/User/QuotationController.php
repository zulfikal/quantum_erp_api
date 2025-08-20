<?php

namespace App\Http\Controllers\User;

use App\Helpers\Constants\QuotationStaticData;
use App\Helpers\Transformers\QuotationTransformer;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreQuotationRequest;
use App\Http\Requests\UpdateQuotationRequest;
use App\Models\HRM\Company;
use App\Models\Sales\Quotation;
use App\Models\Sales\QuotationItem;
use App\Models\Sales\SaleStatus;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use stdClass;

class QuotationController extends Controller
{
    protected Company $company;
    protected Quotation $quotation;

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

    /**
     * Get counts of quotations by status
     * 
     * @return array
     */
    private function getQuotationStatusCounts(): array
    {
        $statuses = SaleStatus::whereIn('type', ['quotation', 'quotation_invoice'])->pluck('name', 'id')->toArray();
        // Get counts for all statuses in a single query
        $statusCounts = Quotation::where('company_id', $this->company->id)
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

        $quotations = Quotation::where('company_id', $this->company->id)
            ->when($search, fn($query) => $query->where('quotation_number', $search)
                ->orWhereHas('customerReferences', function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%");
                }))
            ->when($status, fn($query) => $query->where('sale_status_id', $status))
            ->with('customerReferences', 'items.product')->paginate(25);

        $quotations->through(fn($q) => QuotationTransformer::quotation($q));

        return response()->json([
            'constants' => [
                'statuses' => QuotationStaticData::statuses(),
            ],
            'statistics' => $this->getQuotationStatusCounts(),
            'quotations' => $quotations,
        ], 200);
    }

    public function store(StoreQuotationRequest $request)
    {
        $quotation = $request->validated()['quotation'];
        $customer = $request->validated()['customer'];
        $items = $request->validated()['items'];

        $quotationItems = new Collection();

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

            $quotationItems->push($tempItem);
        }


        DB::transaction(function () use ($quotation, $customer, $quotationItems) {
            $quotationNumber = $quotation['quotation_number'];

            if (is_null($quotationNumber)) {
                $quotationNumber = 'Q' . str_pad($this->company->id, 3, '0', STR_PAD_LEFT)  . str_pad(Quotation::where('company_id', $this->company->id)->count() + 1, 5, '0', STR_PAD_LEFT);
            }

            $create_quotation = Quotation::create([
                'company_id' => $this->company->id,
                'branch_id' => auth()->user()->employee->company_branch_id,
                'quotation_date' => $quotation['quotation_date'],
                'employee_id' => auth()->user()->employee->id,
                'quotation_number' => $quotationNumber,
                'total_amount' => $quotationItems->sum('total_amount') - $quotationItems->sum('tax_amount'),
                'tax_amount' => $quotationItems->sum('tax_amount'),
                'discount_amount' => $quotationItems->sum('discount'),
                'grand_total' => $quotationItems->sum('total_after_discount') + $quotation['shipping_amount'],
                'shipping_amount' => $quotation['shipping_amount'],
                'sale_status_id' => $quotation['sale_status_id'],
                'notes' => $quotation['notes'],
                'description' => $quotation['description'],
            ]);
            $create_quotation->customerReferences()->create($customer);

            foreach ($quotationItems as $item) {
                $create_quotation->items()->create([
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

            $this->quotation = $create_quotation;
        });

        return response()->json([
            'message' => 'Quotation created successfully',
            'quotation' => QuotationTransformer::quotationWithItems($this->quotation),
        ], 201);
    }

    public function show(Quotation $quotation)
    {
        if ($quotation->company_id != $this->company->id) {
            return response()->json([
                'message' => 'You are not authorized to view this quotation',
            ], 403);
        }
        return response()->json([
            'quotation' => QuotationTransformer::quotationWithItems($quotation),
        ], 200);
    }

    public function update(UpdateQuotationRequest $request, Quotation $quotation)
    {
        if ($quotation->company_id != $this->company->id) {
            return response()->json([
                'message' => 'You are not authorized to update this quotation',
            ], 403);
        }
        $quotation->customerReferences()->update($request->validated()['customer']);

        $itemsTotal = $quotation->items()->sum('total') - $quotation->items()->sum('tax_amount');
        $itemsDiscount = $quotation->items()->sum('discount');
        $itemsTaxAmount = $quotation->items()->sum('tax_amount');

        $quotation->update([
            'total_amount' => $itemsTotal + $itemsDiscount,
            'shipping_amount' => $request->validated()['quotation']['shipping_amount'],
            'sale_status_id' => $request->validated()['quotation']['sale_status_id'],
            'quotation_date' => $request->validated()['quotation']['quotation_date'],
            'notes' => $request->validated()['quotation']['notes'],
            'description' => $request->validated()['quotation']['description'],
            'discount_amount' => $itemsDiscount,
            'tax_amount' => $itemsTaxAmount,
            'grand_total' => $itemsTotal +  $request->validated()['quotation']['shipping_amount'] + $itemsTaxAmount,
        ]);


        return response()->json([
            'message' => 'Quotation updated successfully',
            'quotation' => QuotationTransformer::quotationWithItems($quotation),
        ], 200);
    }

    public function destroy(Quotation $quotation)
    {
        if ($quotation->company_id != $this->company->id) {
            return response()->json([
                'message' => 'You are not authorized to delete this quotation',
            ], 403);
        }

        DB::transaction(function () use ($quotation) {
            $quotation->delete();
            $quotation->customerReferences()->delete();
        });

        return response()->json([
            'message' => 'Quotation deleted successfully',
        ], 200);
    }
}
