<?php

use App\Models\User;
use Illuminate\Support\Facades\Route;
use Carbon\Carbon;
use App\Http\Middleware\UserInboxes;
use App\Http\Middleware\usermenu;
use App\Http\Controllers\FinancialAdvanceController;
use App\Http\Controllers\ExpenseInvoiceController;
use App\Http\Controllers\ClosureController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\BranchesController;





/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/




Route::middleware(['auth','userInboxes','usermenu'])->group(function () { 

      Route::get("/",[\App\Http\Controllers\DashboardController::class,"index"])->name("
      ");
      Route::get("dashboard",[\App\Http\Controllers\DashboardController::class,"index"])->name("dashboard");

      Route::get('group', [\App\Http\Controllers\PermissionController::class, 'group'])->name('group');
      Route::get('pergroup/{id}', [\App\Http\Controllers\PermissionController::class, 'pergroup']);
      Route::post('doGroup', [\App\Http\Controllers\PermissionController::class, 'doGroup'])->name('doGroup');
      Route::post('delGroup', [\App\Http\Controllers\PermissionController::class, 'delGroup'])->name('delGroup');
      Route::post('doPergroup', [\App\Http\Controllers\PermissionController::class, 'doPergroup'])->name('doPergroup');

       
      Route::resource("users",\App\Http\Controllers\UserController::class);
      Route::get('myProfile', [\App\Http\Controllers\UserController::class, 'editUserProfile'])->name('myProfile');
      Route::post('updateMyProfile', [\App\Http\Controllers\UserController::class, 'updateUserProfile'])->name('updateMyProfile');
    
      

      Route::resource("/files",\App\Http\Controllers\FileController::class);
      Route::resource("messages",\App\Http\Controllers\MessageController::class);
      Route::post('/messages/reply', [\App\Http\Controllers\MessageController::class, 'reply'])->name('messages.reply');

      Route::resource('advances', FinancialAdvanceController::class)->names('advances');
      Route::post('/advances/{advance}/confirm', [FinancialAdvanceController::class, 'confirm']) ->name('advances.confirm');
      Route::get('advances/{advance}/details', [FinancialAdvanceController::class, 'showDetails'])->name('advances.show_details');

      Route::resource('invoices', ExpenseInvoiceController::class)->names('invoices');
      Route::get('invoices/{invoice}/download', [ExpenseInvoiceController::class, 'downloadFile'])->name('invoices.download');
      Route::get('invoices/{invoice}/view', [ExpenseInvoiceController::class, 'viewFile'])->name('invoices.view');
      Route::post('invoices/{invoice}/reject', [ExpenseInvoiceController::class, 'reject'])->name('invoices.reject');

      // عرض نموذج التقفيل للموظف
      Route::get('closures/form', [ClosureController::class, 'showClosureForm'])->name('closures.form');
      // معالجة طلب التقفيل
      Route::post('closures/process', [ClosureController::class, 'processClosure'])->name('closures.process');
      // التقفيل التلقائي 
      Route::post('closures/auto-process', [ClosureController::class, 'processAutoClosure'])->name('closures.auto.process');

      
      // عرض قائمة بعمليات التقفيل غير المعتمدة
      Route::get('closures/review', [ClosureController::class, 'reviewClosures'])->name('closures.review');
      // اعتماد عملية تقفيل نهائياً
      Route::post('closures/{closure}/approve', [ClosureController::class, 'approveClosure'])->name('closures.approve');
      // Route for rejection
      Route::post('closures/reject/{closure}', [ClosureController::class, 'rejectClosure'])->name('closures.reject');


      
      Route::resource('branches', BranchesController::class);
      Route::resource('suppliers', SupplierController::class);
      // رفع مرفقات جديدة
      Route::post('/suppliers/{supplier}/attachments', [SupplierController::class, 'uploadAttachments']);
      // حذف مرفق
      Route::delete('/suppliers/{supplier}/attachments/{index}', [SupplierController::class, 'deleteAttachment']);
      
      // Route لتعليم إشعار واحد كمقروء
      Route::post('/notifications/{id}/mark-as-read', [NotificationController::class, 'markOneAsRead'])
      ->name('notifications.markOne');

      // Route لتعليم جميع الإشعارات كمقروءة
      Route::post('/notifications/mark-all-as-read', [NotificationController::class, 'markAllAsRead'])
      ->name('notifications.markAll');

      
      // عرض تقرير العهد لموظف محدد
      Route::get('/advance-summary/employee', [ReportController::class, 'showAdvanceReportView'])
      ->name('reports.advance.employee.view');

      Route::post('/advance-summary/employee', [ReportController::class, 'getEmployeeAdvanceBalanceByDate'])
      ->name('reports.advance.employee.data');

      // عرض تقرير العهد لجميع الموظفين
      Route::get('/advance-summary/all', [ReportController::class, 'showAdvanceSummaryAllView'])
      ->name('reports.advance.all.view');

      Route::post('/advance-summary/all', [ReportController::class, 'getAllEmployeesAdvanceBalanceSummary'])
      ->name('reports.advance.all.data');

      
      Route::get('advance/closure-report', [ReportController::class, 'ConfirmedClosureReport'])
      ->name('advances.closure_report');

      Route::get('advance/closure-report/download-attachments', [ReportController::class, 'downloadConfirmedClosureAttachments'])
      ->name('advances.closure_report.download_attachments');



});



Auth::routes();

