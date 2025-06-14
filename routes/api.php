<?php

use App\Http\Controllers\ContractController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\StrengthController;
use App\Http\Controllers\VendorController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DropdownController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\DutyController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\ProposalTemplateController;
use App\Http\Controllers\ProposalTypeController;
use App\Http\Controllers\ProposalController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\WorkController;
use App\Http\Controllers\CashExpenseController;
use App\Http\Controllers\RecipientController;
use App\Http\Controllers\ExpenseHeadController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\AgreementController;
use App\Http\Controllers\LedgerController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\HeadController;

// Public routes (no auth required)
Route::middleware('throttle:10,1')->group(function () {
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/refresh', [AuthController::class, 'refresh'])->name('refresh');
});

// Authenticated routes (all need to be logged in)
Route::middleware('throttle:20,1')->middleware('auth:api')->group(function () {
    //Basic Routes
    Route::get('/validate', [AuthController::class, 'validateAccessToken']);
    Route::get('/dropdowns', [DropdownController::class, 'getDropdownData']);

    // Users Routes
    Route::middleware('checkPermission:view')->get('/users', [UserController::class, 'index']);
    Route::middleware('checkPermission:create')->post('/user', [UserController::class, 'store']);
    Route::middleware('checkPermission:edit')->put('/user/{id}', [UserController::class, 'update']);

    // Permissions Routes
    Route::middleware('checkPermission:view')->get('/permissions', [PermissionController::class, 'index']);
    Route::middleware('checkPermission:create')->post('/permission', [PermissionController::class, 'store']);

    // Tickets Routes
    Route::middleware('checkPermission:view')->get('/tickets', [TicketController::class, 'index']);
    Route::middleware('checkPermission:view')->get('/ticket', [TicketController::class, 'view']);
    Route::middleware('checkPermission:view')->get('/ticket_client', [TicketController::class, 'ticketByClient']);
    Route::middleware('checkPermission:remove')->delete('/ticket/{id}', [TicketController::class, 'destroy']);
    Route::middleware('checkPermission:create')->post('/ticket', [TicketController::class, 'store']);
    Route::middleware('checkPermission:edit')->put('/ticket/{id}', [TicketController::class, 'update']);

    // Duties Routes
    Route::middleware('checkPermission:view')->get('/duties', [DutyController::class, 'index']);
    Route::middleware('checkPermission:remove')->delete('/duty/{id}', [DutyController::class, 'destroy']);
    Route::middleware('checkPermission:create')->post('/duty', [DutyController::class, 'store']);
    Route::middleware('checkPermission:edit')->put('/duty/{id}', [DutyController::class, 'update']);
    Route::middleware('checkPermission:view')->get('/duty_summary', [DutyController::class, 'dutySummary']);

    // Organization Routes
    Route::middleware('checkPermission:create')->post('/organization', [OrganizationController::class, 'store']);
    Route::middleware('checkPermission:view')->get('/organizations', [OrganizationController::class, 'index']);
    Route::middleware('checkPermission:edit')->put('/organization/{id}', [OrganizationController::class, 'update']);

    // Staff Routes
    Route::middleware('checkPermission:view')->get('/staffs', [StaffController::class, 'index']);
    Route::middleware('checkPermission:remove')->delete('/staff/{id}', [StaffController::class, 'destroy']);
    Route::middleware('checkPermission:create')->post('/staff', [StaffController::class, 'store']);
    Route::middleware('checkPermission:edit')->put('/staff/{id}', [StaffController::class, 'update']);
    Route::middleware('checkPermission:view')->get('/merge_pdf', [StaffController::class, 'getPdf']);
    Route::middleware('checkPermission:create')->post('/merge_pdf', [StaffController::class, 'mergePdf']);
    Route::middleware('checkPermission:edit')->patch('/staff', [StaffController::class, 'exitStaff']);
    Route::middleware('checkPermission:view')->get('/generate_card', [StaffController::class, 'getImage']);

    // Location routes
    Route::middleware('checkPermission:view')->get('/locations', [LocationController::class, 'index']);
    Route::middleware('checkPermission:remove')->delete('/location/{id}', [LocationController::class, 'destroy']);
    Route::middleware('checkPermission:create')->post('/location', [LocationController::class, 'store']);
    Route::middleware('checkPermission:edit')->put('/location/{id}', [LocationController::class, 'update']);

    // Proposal Template routes
    Route::middleware('checkPermission:view')->get('/proposal_templates', [ProposalTemplateController::class, 'index']);
    Route::middleware('checkPermission:create')->post('/proposal_template', [ProposalTemplateController::class, 'store']);
    Route::middleware('checkPermission:edit')->put('/proposal_template/{id}', [ProposalTemplateController::class, 'update']);
    Route::middleware('checkPermission:remove')->delete('/proposal_template/{id}', [ProposalTemplateController::class, 'destroy']);
    Route::middleware('checkPermission:edit')->patch('/proposal_templates', [ProposalTemplateController::class, 'updateSequence']);

    // Proposal Type routes
    Route::middleware('checkPermission:create')->post('/proposal_type', [ProposalTypeController::class, 'store']);

    // Proposal routes
    Route::middleware('checkPermission:view')->get('/proposals', [ProposalController::class, 'index']);
    Route::middleware('checkPermission:view')->get('/proposal', [ProposalController::class, 'downloadProposal']);
    Route::middleware('checkPermission:create')->post('/proposal', [ProposalController::class, 'store']);
    Route::middleware('checkPermission:edit')->put('/proposal/{id}', [ProposalController::class, 'update']);

    // Attendance routes
    Route::middleware('checkPermission:view')->get('/attendances', [AttendanceController::class, 'index']);
    Route::middleware('checkPermission:create')->post('/attendance', [AttendanceController::class, 'store']);
    Route::middleware('checkPermission:edit')->patch('/attendance', [AttendanceController::class, 'update']);

    // Work routes
    Route::middleware('checkPermission:view')->get('/work', [WorkController::class, 'index']);
    Route::middleware('checkPermission:remove')->delete('/work/{id}', [WorkController::class, 'destroy']);
    Route::middleware('checkPermission:create')->post('/work', [WorkController::class, 'store']);

    // Cash Expense routes
    Route::middleware('checkPermission:view')->get('/expenses', [CashExpenseController::class, 'index']);
    Route::middleware('checkPermission:remove')->delete('/expense/{id}', [CashExpenseController::class, 'destroy']);
    Route::middleware('checkPermission:create')->post('/expense', [CashExpenseController::class, 'store']);
    Route::middleware('checkPermission:edit')->put('/expense/{id}', [CashExpenseController::class, 'update']);
    Route::middleware('checkPermission:create')->post('/recipient', [RecipientController::class, 'store']);
    Route::middleware('checkPermission:create')->post('/expense_head', [ExpenseHeadController::class, 'store']);
    Route::middleware('checkPermission:view')->get('/expense_statement', [CashExpenseController::class, 'getStatement']);
    Route::middleware('checkPermission:view')->get('/download_statement', [CashExpenseController::class, 'downloadStatement']);
    Route::middleware('checkPermission:view')->get('/monthly_report', [CashExpenseController::class, 'monthlyReport']);
    Route::middleware('checkPermission:view')->get('/export_expense', [CashExpenseController::class, 'exportExpense']);
    Route::middleware('checkPermission:view')->get('/balance_summary', [CashExpenseController::class, 'balanceSummary']);

    // Client routes
    Route::middleware('checkPermission:view')->get('/clients', [ClientController::class, 'index']);
    Route::middleware('checkPermission:remove')->delete('/client/{id}', [ClientController::class, 'destroy']);
    Route::middleware('checkPermission:create')->post('/client', [ClientController::class, 'store']);
    Route::middleware('checkPermission:edit')->put('/client/{id}', [ClientController::class, 'update']);

    // Agreement routes
    Route::middleware('checkPermission:view')->get('/agreements', [AgreementController::class, 'index']);
    Route::middleware('checkPermission:view')->get('/agreement', [AgreementController::class, 'getAgreementByClient']);
    Route::middleware('checkPermission:remove')->delete('/agreement/{id}', [AgreementController::class, 'destroy']);
    Route::middleware('checkPermission:create')->post('/agreement', [AgreementController::class, 'store']);
    Route::middleware('checkPermission:edit')->put('/agreement/{id}', [AgreementController::class, 'update']);

    // Strength routes
    Route::middleware('checkPermission:view')->get('/strengths', [StrengthController::class, 'index']);
    Route::middleware('checkPermission:remove')->delete('/strength/{id}', [StrengthController::class, 'destroy']);
    Route::middleware('checkPermission:create')->post('/strength', [StrengthController::class, 'store']);
    Route::middleware('checkPermission:edit')->put('/strength/{id}', [StrengthController::class, 'update']);

    // Vendor routes
    Route::middleware('checkPermission:view')->get('/vendors', [VendorController::class, 'index']);
    Route::middleware('checkPermission:remove')->delete('/vendor/{id}', [VendorController::class, 'destroy']);
    Route::middleware('checkPermission:create')->post('/vendor', [VendorController::class, 'store']);
    Route::middleware('checkPermission:edit')->put('/vendor/{id}', [VendorController::class, 'update']);

    // Contract routes
    Route::middleware('checkPermission:view')->get('/contracts', [ContractController::class, 'index']);
    Route::middleware('checkPermission:edit')->patch('/contract', [ContractController::class, 'terminate']);
    Route::middleware('checkPermission:remove')->delete('/contract/{id}', [ContractController::class, 'destroy']);
    Route::middleware('checkPermission:create')->post('/contract', [ContractController::class, 'store']);
    Route::middleware('checkPermission:edit')->put('/contract/{id}', [ContractController::class, 'update']);

    // Purchase Order routes
    Route::middleware('checkPermission:view')->get('/purchase_orders', [PurchaseOrderController::class, 'index']);
    Route::middleware('checkPermission:remove')->delete('/purchase_order/{id}', [PurchaseOrderController::class, 'destroy']);
    Route::middleware('checkPermission:edit')->patch('/purchase_order', [PurchaseOrderController::class, 'cancel']);
    Route::middleware('checkPermission:create')->post('/purchase_order', [PurchaseOrderController::class, 'store']);
    Route::middleware('checkPermission:view')->get('/purchase_order', [PurchaseOrderController::class, 'downloadPurchaseOrder']);
    Route::middleware('checkPermission:edit')->put('/purchase_order/{id}', [PurchaseOrderController::class, 'update']);

    // Ledger routes
    Route::middleware('checkPermission:view')->get('/ledgers', [LedgerController::class, 'index']);
    Route::middleware('checkPermission:remove')->delete('/ledger/{id}', [LedgerController::class, 'destroy']);
    Route::middleware('checkPermission:create')->post('/ledger', [LedgerController::class, 'store']);
    Route::middleware('checkPermission:edit')->put('/ledger/{id}', [LedgerController::class, 'update']);
    Route::middleware('checkPermission:view')->get('/ledger_statement', [LedgerController::class, 'getStatement']);
    Route::middleware('checkPermission:view')->get('/export_ledger', [LedgerController::class, 'exportLedger']);
    Route::middleware('checkPermission:view')->get('/ledger_summary', [LedgerController::class, 'ledgerSummary']);
    Route::middleware('checkPermission:view')->get('/service_invoices', [LedgerController::class, 'getServiceInvoices']);
    Route::middleware('checkPermission:remove')->delete('/service_invoices/{id}', [LedgerController::class, 'destroyInvoice']);
     Route::middleware('checkPermission:edit')->post('/service_invoices', [LedgerController::class, 'upsertInvoice']);
    
    // Item routes
    Route::middleware('checkPermission:view')->get('/item_profile', [ItemController::class, 'getItemProfile']);
    Route::middleware('checkPermission:view')->get('/items', [ItemController::class, 'index']);
    Route::middleware('checkPermission:remove')->delete('/item/{id}', [ItemController::class, 'destroy']);
    Route::middleware('checkPermission:create')->post('/category', [ItemController::class, 'addCategory']);
    Route::middleware('checkPermission:create')->post('/item', [ItemController::class, 'store']);
    Route::middleware('checkPermission:edit')->put('/item/{id}', [ItemController::class, 'update']);

    // Stock routes
    Route::middleware('checkPermission:view')->get('/stocks', [StockController::class, 'index']);
    Route::middleware('checkPermission:remove')->delete('/stock/{id}', [StockController::class, 'destroy']);
    Route::middleware('checkPermission:create')->post('/stock', [StockController::class, 'store']);
    Route::middleware('checkPermission:view')->get('/stock_summary', [StockController::class, 'stockSummary']);

    // Head routes
    Route::middleware('checkPermission:view')->get('/heads', [HeadController::class, 'index']);

    // Special function
    Route::middleware('checkPermission:view')->get('/staff', [StaffController::class, 'copyfile']);
});

