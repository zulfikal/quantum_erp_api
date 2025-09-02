<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\HRM\Company;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    private Company $company;

    public function __construct()
    {
        // $this->middleware('can:department.index')->only('index'); 

        $this->middleware(function ($request, $next) {
            $this->company = auth()->user()->employee->company;
            if (is_null($this->company)) {
                return response()->json(['message' => 'User is not associated with a company.'], 403);
            }
            return $next($request);
        });
    }

    public function index() {}
}
