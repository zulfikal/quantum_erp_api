<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\CompanyController;
use App\Http\Controllers\Admin\CompanyBranchController;
use App\Http\Controllers\Admin\DesignationController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\SalaryController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AttendanceController;

Route::get('/welcome', function () {
    return response()->json([
        'message' => 'Welcome to Quantum HRM API',
    ], 200);
});

Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::prefix('attendances')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [AttendanceController::class, 'index']);
    Route::post('/clock-in', [AttendanceController::class, 'clockIn']);
    Route::post('/clock-out', [AttendanceController::class, 'clockOut']);
    Route::post('/break-start/{attendance}', [AttendanceController::class, 'breakStart']);
    Route::post('/break-end/{attendance}', [AttendanceController::class, 'breakEnd']);
    Route::post('/approve/{attendance}', [AttendanceController::class, 'approve']);
    Route::post('/reject/{attendance}', [AttendanceController::class, 'reject']);
});

Route::prefix('admin')->middleware('auth:sanctum')->group(function () {

    Route::prefix('companies')->group(function () {

        Route::get('/', [CompanyController::class, 'index']);
        Route::post('/', [CompanyController::class, 'store']);
        Route::post('/{company}', [CompanyController::class, 'update']);

        Route::prefix('branches')->group(function () {
            Route::get('/{company}', [CompanyBranchController::class, 'index']);
            Route::post('/{company}', [CompanyBranchController::class, 'store']);
            Route::post('/update/{companyBranch}', [CompanyBranchController::class, 'update']);
        });

        Route::prefix('designations')->group(function () {
            Route::get('/{company}', [DesignationController::class, 'index']);
            Route::post('/{company}', [DesignationController::class, 'store']);
            Route::post('/update/{designation}', [DesignationController::class, 'update']);
        });
    });

    Route::prefix('human-resources')->group(function () {

        Route::prefix('employees')->group(function () {
            Route::get('/{companyBranch}', [EmployeeController::class, 'index']);
            Route::post('/{companyBranch}', [EmployeeController::class, 'store']);
            Route::post('/update/{employee}', [EmployeeController::class, 'update']);
        });

        Route::prefix('salary')->group(function () {

            Route::prefix('types')->group(function () {
                Route::get('/', [SalaryController::class, 'salaryTypeIndex']);
                Route::post('/', [SalaryController::class, 'salaryTypeStore']);
                Route::post('/update/{salaryType}', [SalaryController::class, 'salaryTypeUpdate']);
            });

            Route::prefix('items')->group(function () {
                Route::get('/{employee}', [SalaryController::class, 'salaryItemIndex']);
                Route::post('/{employee}', [SalaryController::class, 'salaryItemStore']);
                Route::post('/update/{salaryItem}', [SalaryController::class, 'salaryItemUpdate']);
            });

            Route::prefix('processes')->group(function () {
                Route::get('/{company}', [SalaryController::class, 'salaryProcessIndex']);
                Route::post('/{company}', [SalaryController::class, 'salaryProcessStore']);
                Route::post('/update/{salaryProcess}', [SalaryController::class, 'salaryProcessUpdate']);
            });
        });
    });
});
