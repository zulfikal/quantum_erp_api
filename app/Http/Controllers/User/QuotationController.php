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
        // Get counts for all statuses in a single query
        $statusCounts = Quotation::where('company_id', $this->company->id)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Get total count
        $total = array_sum($statusCounts);

        // Prepare the result with all possible statuses
        $result = [
            'total' => $total,
            'draft' => $statusCounts['draft'] ?? 0,
            'sent' => $statusCounts['sent'] ?? 0,
            'approved' => $statusCounts['approved'] ?? 0,
            'rejected' => $statusCounts['rejected'] ?? 0,
            'completed' => $statusCounts['completed'] ?? 0,
        ];

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
            ->when($status, fn($query) => $query->where('status', $status))
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
                'total_amount' => $quotationItems->sum('total_amount'),
                'discount_amount' => $quotationItems->sum('discount'),
                'grand_total' => $quotationItems->sum('total_after_discount'),
                'status' => $quotation['status'],
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
        return response()->json([
            'quotation' => QuotationTransformer::quotationWithItems($quotation),
        ], 200);
    }

    public function update(UpdateQuotationRequest $request, Quotation $quotation)
    {
        $quotation->update($request->validated()['quotation']);
        $quotation->customerReferences()->update($request->validated()['customer']);

        return response()->json([
            'message' => 'Quotation updated successfully',
            'quotation' => QuotationTransformer::quotationWithItems($quotation),
        ], 200);
    }

    public function destroy(Quotation $quotation)
    {
        DB::transaction(function () use ($quotation) {
            $quotation->delete();
            $quotation->customerReferences()->delete();
        });

        return response()->json([
            'message' => 'Quotation deleted successfully',
        ], 200);
    }
}
