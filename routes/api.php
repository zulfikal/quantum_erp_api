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
use App\Http\Controllers\User\BoardController;
use App\Http\Controllers\User\BranchController;
use App\Http\Controllers\User\ClaimApprovalController;
use App\Http\Controllers\User\ClaimController;
use App\Http\Controllers\User\ClaimTypeController;
use App\Http\Controllers\User\CompanyBankController;
use App\Http\Controllers\User\DepartmentController;
use App\Http\Controllers\User\DesignationController as UserDesignationController;
use App\Http\Controllers\User\EmployeeController as UserEmployeeController;
use App\Http\Controllers\User\EntityAddressController;
use App\Http\Controllers\User\EntityContactController;
use App\Http\Controllers\User\EntityController;
use App\Http\Controllers\User\InvoiceController;
use App\Http\Controllers\User\InvoiceItemController;
use App\Http\Controllers\User\LeaveRequestApprovalController;
use App\Http\Controllers\User\LeaveRequestController;
use App\Http\Controllers\User\LeaveTypeController;
use App\Http\Controllers\User\PayrollController;
use App\Http\Controllers\User\PermissionController;
use App\Http\Controllers\User\ProductCategoryController;
use App\Http\Controllers\User\ProductController;
use App\Http\Controllers\User\ProjectAssigneeController;
use App\Http\Controllers\User\ProjectController;
use App\Http\Controllers\User\ProjectTaskAssigneeController;
use App\Http\Controllers\User\ProjectTaskCommentController;
use App\Http\Controllers\User\QuotationController;
use App\Http\Controllers\User\QuotationItemController;
use App\Http\Controllers\User\SalaryController as UserSalaryController;
use App\Http\Controllers\User\SalaryProcessController;
use App\Http\Controllers\User\SalaryTypeController;
use App\Http\Controllers\User\TaskController;
use App\Http\Controllers\User\TransactionController;

Route::get('/welcome', function () {
    return response()->json([
        'message' => 'Welcome to Quantum ERP API',
    ], 200);
});

Route::post('/login', [AuthController::class, 'login']);
Route::post('/check', [AuthController::class, 'check']);
Route::post('/register', [AuthController::class, 'register']);
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
    Route::prefix('permissions')->group(function () {
        Route::get('/', [PermissionController::class, 'index']);
        Route::post('/manage', [PermissionController::class, 'manage']);
    });

    Route::prefix('banks')->middleware('auth:sanctum')->group(function () {
        Route::get('/', [BankController::class, 'index']);
    });

    Route::prefix('branches')->group(function () {
        Route::get('/', [BranchController::class, 'index']);
        Route::post('/', [BranchController::class, 'store']);
        Route::get('/{companyBranch}', [BranchController::class, 'show']);
        Route::post('/update/{companyBranch}', [BranchController::class, 'update']);
    });

    Route::prefix('company-banks')->group(function () {
        Route::get('/', [CompanyBankController::class, 'index']);
        Route::post('/', [CompanyBankController::class, 'store']);
        Route::get('/{companyBank}', [CompanyBankController::class, 'show']);
        Route::post('/update/{companyBank}', [CompanyBankController::class, 'update']);
    });

    Route::prefix('transactions')->group(function () {
        Route::get('/', [TransactionController::class, 'index']);
        Route::post('/', [TransactionController::class, 'store']);
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


        Route::get('/show/{employee}', [UserSalaryController::class, 'salaryShow']);

        Route::prefix('types')->group(function () {
            Route::get('/', [SalaryTypeController::class, 'index']);
            Route::post('/', [SalaryTypeController::class, 'store']);
            Route::get('/{salaryType}', [SalaryTypeController::class, 'show']);
            Route::post('/update/{salaryType}', [SalaryTypeController::class, 'update']);
        });

        Route::prefix('items')->group(function () {
            Route::get('/', [UserSalaryController::class, 'salaryItemIndex']);
            Route::post('/{employee}', [UserSalaryController::class, 'salaryItemStore']);
            Route::post('/update/{salaryItem}', [UserSalaryController::class, 'salaryItemUpdate']);
            Route::post('/delete/{salaryItem}', [UserSalaryController::class, 'salaryItemDestroy']);
        });

        Route::prefix('processes')->group(function () {
            Route::get('/', [SalaryProcessController::class, 'index']);
            Route::get('/{salaryProcess}', [SalaryProcessController::class, 'show']);
            Route::post('/{companyBranch}', [SalaryProcessController::class, 'store']);
            Route::post('/update/{salaryProcess}', [SalaryProcessController::class, 'update']);
        });
    });

    Route::prefix('payrolls')->group(function () {
        Route::get('/', [PayrollController::class, 'index']);
        Route::get('/{salaryProcessItem}', [PayrollController::class, 'show']);
        Route::get('/pdf/{salaryProcessItem}', [PayrollController::class, 'pdf']);
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

    Route::prefix('entities')->group(function () {
        Route::get('/', [EntityController::class, 'index']);
        Route::post('/', [EntityController::class, 'store']);
        Route::get('/{entity}', [EntityController::class, 'show']);
        Route::post('/update/{entity}', [EntityController::class, 'update']);
        Route::post('/delete/{entity}', [EntityController::class, 'destroy']);

        Route::prefix('addresses')->group(function () {
            Route::get('/{entity}', [EntityAddressController::class, 'index']);
            Route::post('/{entity}', [EntityAddressController::class, 'store']);
            Route::get('/show/{address}', [EntityAddressController::class, 'show']);
            Route::post('/update/{address}', [EntityAddressController::class, 'update']);
            Route::post('/delete/{address}', [EntityAddressController::class, 'destroy']);
        });

        Route::prefix('contacts')->group(function () {
            Route::get('/{entity}', [EntityContactController::class, 'index']);
            Route::post('/{entity}', [EntityContactController::class, 'store']);
            Route::get('/show/{contact}', [EntityContactController::class, 'show']);
            Route::post('/update/{contact}', [EntityContactController::class, 'update']);
            Route::post('/delete/{contact}', [EntityContactController::class, 'destroy']);
        });
    });

    Route::prefix('products')->group(function () {
        Route::prefix('main')->group(function () {
            Route::get('/', [ProductController::class, 'index']);
            Route::post('/', [ProductController::class, 'store']);
            Route::get('/{product}', [ProductController::class, 'show']);
            Route::post('/update/{product}', [ProductController::class, 'update']);
            Route::post('/toggle-is-active/{product}', [ProductController::class, 'toggleIsActive']);
        });

        Route::prefix('categories')->group(function () {
            Route::get('/', [ProductCategoryController::class, 'index']);
            Route::post('/', [ProductCategoryController::class, 'store']);
            Route::get('/{category}', [ProductCategoryController::class, 'show']);
            Route::post('/update/{category}', [ProductCategoryController::class, 'update']);
            Route::post('/delete/{category}', [ProductCategoryController::class, 'destroy']);
        });
    });

    Route::prefix('sales')->group(function () {
        Route::prefix('quotations')->group(function () {
            Route::prefix('main')->group(function () {
                Route::get('/', [QuotationController::class, 'index']);
                Route::post('/', [QuotationController::class, 'store']);
                Route::get('/{quotation}', [QuotationController::class, 'show']);
                Route::get('/pdf/{quotation}', [QuotationController::class, 'pdf']);
                Route::post('/update/{quotation}', [QuotationController::class, 'update']);
                Route::post('/delete/{quotation}', [QuotationController::class, 'destroy']);
                Route::post('/convert/{quotation}', [QuotationController::class, 'convertToInvoice']);
            });

            Route::prefix('items')->group(function () {
                Route::post('/{quotation}', [QuotationItemController::class, 'store']);
                Route::post('/update/{quotationItem}', [QuotationItemController::class, 'update']);
                Route::post('/delete/{quotationItem}', [QuotationItemController::class, 'destroy']);
            });
        });

        Route::prefix('invoices')->group(function () {
            Route::prefix('main')->group(function () {
                Route::get('/', [InvoiceController::class, 'index']);
                Route::post('/', [InvoiceController::class, 'store']);
                Route::get('/{invoice}', [InvoiceController::class, 'show']);
                Route::get('/pdf/{invoice}', [InvoiceController::class, 'pdf']);
                Route::post('/update/{invoice}', [InvoiceController::class, 'update']);
                Route::post('/delete/{invoice}', [InvoiceController::class, 'destroy']);
            });

            Route::prefix('items')->group(function () {
                Route::post('/{invoice}', [InvoiceItemController::class, 'store']);
                Route::post('/update/{invoiceItem}', [InvoiceItemController::class, 'update']);
                Route::post('/delete/{invoiceItem}', [InvoiceItemController::class, 'destroy']);
            });
        });
    });

    Route::prefix('projects')->group(function () {
        Route::prefix('main')->group(function () {
            Route::get('/', [ProjectController::class, 'index']);
            Route::post('/', [ProjectController::class, 'store']);
            Route::get('/{project}', [ProjectController::class, 'show']);
            Route::post('/update/{project}', [ProjectController::class, 'update']);

            Route::prefix('assignees')->group(function () {
                Route::post('/{project}', [ProjectAssigneeController::class, 'store']);
                Route::post('/delete/{projectAssignee}', [ProjectAssigneeController::class, 'destroy']);
            });
        });

        Route::prefix('boards')->group(function () {
            Route::get('/{project}', [BoardController::class, 'index']);
            Route::post('/{project}', [BoardController::class, 'store']);
            Route::post('/update/{board}', [BoardController::class, 'update']);
            Route::post('/delete/{board}', [BoardController::class, 'destroy']);
            Route::post('/reorder-boards/{project}', [BoardController::class, 'reorderBoards']);
        });

        Route::prefix('tasks')->group(function () {
            Route::get('/{task}', [TaskController::class, 'show']);
            Route::post('/{projectBoard}', [TaskController::class, 'store']);
            Route::post('/update/{task}', [TaskController::class, 'update']);
            Route::post('/delete/{task}', [TaskController::class, 'destroy']);
            Route::post('/reorder-tasks/{fromBoard}/{toBoard}', [TaskController::class, 'reorderTasks']);

            Route::prefix('assignees')->group(function () {
                Route::post('/{task}', [ProjectTaskAssigneeController::class, 'store']);
                Route::post('/delete/{taskAssignee}', [ProjectTaskAssigneeController::class, 'destroy']);
            });

            Route::prefix('comments')->group(function () {
                Route::post('/{task}', [ProjectTaskCommentController::class, 'store']);
                Route::post('/delete/{taskComment}', [ProjectTaskCommentController::class, 'destroy']);
            });
        });
    });
});
