<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Sales\Invoice;
use Illuminate\Http\Request;

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
}
