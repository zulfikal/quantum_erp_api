# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Quantum HRM API is a Laravel 12-based REST API for a comprehensive Enterprise Resource Planning (ERP) system with a focus on Human Resource Management. The system manages:
- HRM: Employees, attendance, leaves, claims, payroll
- Sales: Quotations, invoices, customer management
- Business Partners: Entities (customers/vendors), contacts, addresses
- Products: Product catalog and categories
- Accounting: Transactions, company banks
- Projects: Project management with boards, tasks, and activities

## Development Commands

### Setup
```bash
# Install dependencies
composer install
npm install

# Setup environment
cp .env.example .env
php artisan key:generate

# Database setup
php artisan migrate
php artisan db:seed

# Storage link (for media files)
php artisan storage:link
```

### Running the Application
```bash
# Start all services (server, queue, logs, vite)
composer dev

# Or individually:
php artisan serve              # Development server
php artisan queue:listen       # Queue worker
php artisan pail               # Real-time logs
npm run dev                    # Vite dev server
```

### Testing
```bash
# Run all tests
composer test
# Or: php artisan test

# Run specific test file
php artisan test tests/Feature/ExampleTest.php

# Run specific test method
php artisan test --filter test_example
```

### Code Quality
```bash
# Laravel Pint (code formatting)
./vendor/bin/pint

# Format specific files
./vendor/bin/pint app/Models/User.php
```

### Other Utilities
```bash
# Clear caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Debugging
php artisan tinker              # REPL
php artisan telescope:prune     # Clean Telescope data
```

## Architecture Overview

### Multi-Tenancy Pattern
The application uses a soft multi-tenancy approach based on Companies and Company Branches:
- Each Employee belongs to a CompanyBranch
- CompanyBranch belongs to a Company
- Most controllers use middleware to ensure users only access their company's data
- Pattern: `$this->company = auth()->user()->employee->company;`

### Authentication & Authorization
- **Authentication**: Laravel Sanctum (token-based API authentication)
- **Authorization**: Spatie Laravel Permission (roles & permissions)
- **Roles**: `admin`, `employee` (configured via Spatie)
- **Middleware**: Controllers commonly use `->middleware('auth:sanctum')` and `->middleware('role:employee|admin')`
- User model uses `HasRoles`, `HasApiTokens` traits

### Namespace Organization
The app follows a domain-driven structure within `app/`:
- **Models**: Domain-based subdirectories
  - `HRM/`: Employee, Attendance, Leave, Claim, Department, Designation, etc.
  - `Salary/`: SalaryType, SalaryItem, SalaryProcess, SalaryProcessItem
  - `BusinessPartner/`: Entity, EntityAddress, EntityContact
  - `Product/`: Product, ProductCategory
  - `Sales/`: Quotation, Invoice, CustomerReference, SaleStatus
  - `Accounting/`: Transaction, TransactionMethod
  - `Project/`: Project, ProjectBoard, ProjectTask, etc.
- **Controllers**: Separated by access level
  - `Admin/`: Admin-only operations (CompanyController, EmployeeController, etc.)
  - `User/`: Operations available to authenticated users with role checks
- **Helpers**:
  - `Transformers/`: Transform models to API response format (e.g., `AttendanceTransformer`, `EmployeeTransformer`)
  - `Constants/`: Static data classes (e.g., `AttendanceStaticData`, `LeaveStaticData`)
  - `Activity/`: Business logic helpers (e.g., `LogProjectActivity`)
  - `Services/`: Service classes (e.g., `CustomPathGenerator` for media)

### Data Transformation Pattern
Controllers consistently use Transformer classes instead of API Resources:
```php
// Pattern used throughout the codebase:
AttendanceTransformer::attendance($attendance)
EmployeeTransformer::transform($employee)
```
Transformers return simple arrays with formatted data, not Laravel Resource objects.

### Model Patterns
- **Fillable**: Models use `$fillable` for mass assignment
- **Relationships**: Extensive use of Eloquent relationships (belongsTo, hasMany, hasOneThrough)
- **Scopes**: Query scopes for common filters (e.g., `scopeForEmployee`, `scopeBetweenDates`)
- **Accessors**: Computed attributes (e.g., `getWorkedHoursAttribute`, `getFullNameAttribute`)
- **Business Logic**: Domain logic methods on models (e.g., `computeWorkedSeconds()`, `salaryItemTotal()`)

### Media Handling
Uses Spatie Media Library for file uploads (avatars, documents):
- Employee model implements `HasMedia` interface
- Media collections: `avatar` (with thumbnail conversion)
- Custom path generator: `CustomPathGenerator`

### Static Data Pattern
Constants classes provide static reference data for dropdowns/filters:
```php
AttendanceStaticData::types()     // Returns attendance status options
LeaveStaticData::statuses()       // Returns leave status options
```

### API Response Pattern
Controllers return JSON responses consistently:
```php
return response()->json([
    'data' => $transformedData,
    'constants' => [...],  // Often includes dropdown/filter options
    'message' => '...',
]);
```

### Route Structure
All API routes in `routes/api.php`:
- `/login`, `/register`, `/logout` - Authentication
- `/admin/*` - Admin-only routes (companies, designations, salary processing)
- `/application/*` - User routes (attendance, leaves, claims, entities, products, sales, projects)
- Route parameters use model binding (e.g., `Attendance $attendance`)

### Controller Patterns
Common patterns in User controllers:
1. Middleware setup in `__construct()` with role checks
2. Company context setup via middleware callback
3. Authorization checks: Verify user has access to requested resource
4. Query building with `when()` for conditional filters
5. Pagination (typically `->paginate(20)`)
6. Transformation before response

### Database Transactions
Critical operations wrapped in DB transactions:
```php
DB::transaction(function () use ($data) {
    // Multiple database operations
});
```

### Attendance System
- Clock in/out tracking with geolocation support
- Break tracking (start/end with duration calculation)
- Status types: present, absent, on_leave, half_day, pending
- Real-time worked time calculation considering breaks
- Approval workflow (approved_by, approved_at)

### Salary Processing
- Hierarchical structure: SalaryProcess → SalaryProcessItem → SalaryProcessItemDetail
- Employee has base salary + SalaryItems (allowances/deductions/contributions)
- SalaryType defines category (allowance, deduction, company_contribution)
- Computed totals via model methods: `salaryItemTotal()`, `salaryItemAllowances()`, `salaryItemDeductions()`

### Leave & Claim Workflow
- Leave requests with multiple dates (LeaveDate model)
- Claim requests with amounts and types
- Approval controllers separate from request controllers
- Status transitions: pending → approved/rejected

### Sales Flow
- Quotation can be converted to Invoice
- Both use item-based structure (QuotationItem, InvoiceItem)
- CustomerReference tracks referral information
- PDF generation support for both quotations and invoices

### Project Management
- Project → ProjectBoard → ProjectTask hierarchy
- Task assignees and project assignees (many-to-many)
- Task comments with author tracking
- Activity logging via `LogProjectActivity` helper
- Board and task reordering support

## Important Conventions

### Update Routes
Most update routes use POST instead of PATCH/PUT:
```php
Route::post('/update/{model}', [Controller::class, 'update']);
```

### Foreign Key Naming
Consistent naming: `{model}_id` (e.g., `employee_id`, `company_branch_id`)

### Timestamp Tracking
Models track: `created_by`, `updated_by` (user IDs) in addition to Laravel timestamps

### Status Fields
String-based status fields (not enums): 'present', 'absent', 'pending', etc.

### Date Handling
- Use Carbon for date operations
- Database stores `date` columns as dates, `*_at` columns as datetimes
- API responses format dates: `$date->format('Y-m-d H:i:s')`

### Global Entities/Products
Entities and Products support a `is_global` flag for company-wide vs branch-specific resources

## Testing Environment
Tests use in-memory SQLite database (configured in `phpunit.xml`):
- `DB_CONNECTION=sqlite`
- `DB_DATABASE=:memory:`
- Telescope/Pulse disabled in tests

## Key Dependencies
- **laravel/sanctum**: API authentication
- **spatie/laravel-permission**: Role/permission management
- **spatie/laravel-medialibrary**: File uploads and media management
- **barryvdh/laravel-dompdf**: PDF generation
- **laravel/telescope**: Debugging and monitoring (dev only)
- **laravel/pint**: Code style enforcement

## Development Notes
- Queue connection: `database` (requires running `queue:listen`)
- Cache driver: `database`
- Session driver: `database`
- Mail driver: `log` (in development)
- Use `composer dev` to run all services concurrently
