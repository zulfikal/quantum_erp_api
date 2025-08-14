<?php

namespace App\Http\Controllers\User;

use App\Helpers\Transformers\SalaryTransformer;
use App\Http\Controllers\Controller;
use App\Models\Salary\SalaryType;
use Illuminate\Http\Request;

class SalaryTypeController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:salary_type.index')->only('index');
        $this->middleware('can:salary_type.create')->only('store');
        $this->middleware('can:salary_type.edit')->only('update');
        $this->middleware('can:salary_type.show')->only('show');
    }

    public function index()
    {
        $salaryTypes = SalaryType::paginate(25);

        $salaryTypes->through(function ($salaryType) {
            return SalaryTransformer::salaryType($salaryType);
        });

        return response()->json($salaryTypes);
    }
}
