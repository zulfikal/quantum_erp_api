<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\CompanyController;
use App\Http\Controllers\Admin\CompanyBranchController;
use App\Http\Controllers\Admin\DesignationController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\SalaryController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\BankController;
use App\Http\Controllers\User\AttendanceController as UserAttendanceController;
use App\Http\Controllers\User\BranchController;
use App\Http\Controllers\User\ClaimApprovalController;
use App\Http\Controllers\User\ClaimController;
use App\Http\Controllers\User\ClaimTypeController;
use App\Http\Controllers\User\DepartmentController;
use App\Http\Controllers\User\DesignationController as UserDesignationController;
use App\Http\Controllers\User\EmployeeController as UserEmployeeController;
use App\Http\Controllers\User\LeaveRequestApprovalController;
use App\Http\Controllers\User\LeaveRequestController;
use App\Http\Controllers\User\LeaveTypeController;
use App\Http\Controllers\User\PayrollController;
use App\Http\Controllers\User\SalaryController as UserSalaryController;
use App\Http\Controllers\User\SalaryProcessController;
use App\Http\Controllers\User\SalaryTypeController;

Route::get('/welcome', function () {
    return response()->json([
        'message' => 'Welcome to Quantum ERP API',
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
        Route::get('/{company}', [CompanyController::class, 'show']);
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
            Route::post('/', [EmployeeController::class, 'store']);
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

Route::prefix('application')->middleware('auth:sanctum')->group(function () {
    Route::prefix('banks')->middleware('auth:sanctum')->group(function () {
        Route::get('/', [BankController::class, 'index']);
    });

    Route::prefix('branches')->group(function () {
        Route::get('/', [BranchController::class, 'index']);
        Route::post('/', [BranchController::class, 'store']);
        Route::get('/{companyBranch}', [BranchController::class, 'show']);
        Route::post('/update/{companyBranch}', [BranchController::class, 'update']);
    });

    Route::prefix('departments')->group(function () {
        Route::get('/', [DepartmentController::class, 'index']);
        Route::post('/', [DepartmentController::class, 'store']);
        Route::get('/{department}', [DepartmentController::class, 'show']);
        Route::post('/update/{department}', [DepartmentController::class, 'update']);
    });

    Route::prefix('designations')->group(function () {
        Route::get('/', [UserDesignationController::class, 'index']);
        Route::post('/', [UserDesignationController::class, 'store']);
        Route::get('/{designation}', [UserDesignationController::class, 'show']);
        Route::post('/update/{designation}', [UserDesignationController::class, 'update']);
    });

    Route::prefix('employees')->group(function () {
        Route::get('/', [UserEmployeeController::class, 'index']);
        Route::post('/', [UserEmployeeController::class, 'store']);
        Route::get('/{employee}', [UserEmployeeController::class, 'show']);
        Route::post('/update/{employee}', [UserEmployeeController::class, 'update']);
    });

    Route::prefix('attendances')->group(function () {
        Route::get('/', [UserAttendanceController::class, 'index']);
        Route::get('/{attendance}', [UserAttendanceController::class, 'show']);
        Route::post('/clock-in', [UserAttendanceController::class, 'clockIn']);
        Route::post('/clock-out/{attendance}', [UserAttendanceController::class, 'clockOut']);
        Route::post('/break-start/{attendance}', [UserAttendanceController::class, 'breakStart']);
        Route::post('/break-end/{attendanceBreak}', [UserAttendanceController::class, 'breakEnd']);
    });

    Route::prefix('salaries')->group(function () {
        Route::get('/types', [SalaryTypeController::class, 'index']);

        Route::get('/show/{employee}', [UserSalaryController::class, 'salaryShow']);

        Route::prefix('items')->group(function () {
            Route::get('/', [UserSalaryController::class, 'salaryItemIndex']);
            Route::post('/{employee}', [UserSalaryController::class, 'salaryItemStore']);
            Route::post('/update/{salaryItem}', [UserSalaryController::class, 'salaryItemUpdate']);
        });

        Route::prefix('processes')->group(function () {
            Route::get('/', [SalaryProcessController::class, 'index']);
            Route::post('/{companyBranch}', [SalaryProcessController::class, 'store']);
            Route::post('/update/{salaryProcess}', [SalaryProcessController::class, 'update']);
        });
    });

    Route::prefix('payrolls')->group(function () {
        Route::get('/', [PayrollController::class, 'index']);
        Route::get('/{salaryProcessItem}', [PayrollController::class, 'show']);
    });

    Route::prefix('leaves')->group(function () {
        Route::prefix('types')->group(function () {
            Route::get('/', [LeaveTypeController::class, 'index']);
            Route::post('/', [LeaveTypeController::class, 'store']);
            Route::get('/{leaveType}', [LeaveTypeController::class, 'show']);
            Route::post('/update/{leaveType}', [LeaveTypeController::class, 'update']);
        });

        Route::prefix('requests')->group(function () {
            Route::get('/', [LeaveRequestController::class, 'index']);
            Route::post('/', [LeaveRequestController::class, 'store']);
            Route::get('/{leave}', [LeaveRequestController::class, 'show']);
            Route::post('/update/{leave}', [LeaveRequestController::class, 'updateLeave']);
            Route::post('/delete/{leave}', [LeaveRequestController::class, 'deleteLeave']);

            Route::prefix('dates')->group(function () {
                Route::post('/{leave}', [LeaveRequestController::class, 'storeLeaveDate']);
                Route::post('/update/{leaveDate}', [LeaveRequestController::class, 'updateLeaveDate']);
                Route::post('/delete/{leaveDate}', [LeaveRequestController::class, 'deleteLeaveDate']);
            });
        });

        Route::prefix('approval')->group(function () {
            Route::get('/', [LeaveRequestApprovalController::class, 'index']);
            Route::get('/{leave}', [LeaveRequestApprovalController::class, 'show']);
            Route::post('/{leave}', [LeaveRequestApprovalController::class, 'approval']);
            Route::post('/cancel/{leave}', [LeaveRequestApprovalController::class, 'cancel']);
        });
    });

    Route::prefix('claims')->group(function () {
        Route::prefix('types')->group(function () {
            Route::get('/', [ClaimTypeController::class, 'index']);
        });

        Route::prefix('requests')->group(function () {
            Route::get('/', [ClaimController::class, 'index']);
            Route::post('/', [ClaimController::class, 'store']);
            Route::get('/{claim}', [ClaimController::class, 'show']);
            Route::post('/update/{claim}', [ClaimController::class, 'update']);
            Route::post('/delete/{claim}', [ClaimController::class, 'destroy']);
        });

        Route::prefix('approval')->group(function () {
            Route::get('/', [ClaimApprovalController::class, 'index']);
            Route::get('/{claim}', [ClaimApprovalController::class, 'show']);
            Route::post('/{claim}', [ClaimApprovalController::class, 'approval']);
        });
    });
});
