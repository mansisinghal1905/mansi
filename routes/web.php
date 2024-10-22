<?php
use App\Http\Middleware;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\DeveloperController;
use App\Http\Controllers\Admin\ChangePasswordController;
use App\Http\Controllers\Admin\UserProfileController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\QuotationController;
use App\Http\Controllers\Admin\SendPurposalController;
use App\Http\Controllers\Admin\ProjectStatusController;
use App\Http\Controllers\Admin\ClientUserController;
use App\Http\Controllers\Admin\TaskController;
use App\Http\Controllers\Admin\TaskAssignController;
use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\ProjectAssignController;
use App\Http\Controllers\Admin\InvoicePaymentController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\TicketController;
use App\Http\Controllers\Admin\HostCustomerController;
use App\Http\Controllers\Admin\HostPaymentController;
use App\Http\Controllers\Admin\ChatController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\AttendanceController;
use App\Http\Controllers\Admin\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Admin\Auth\ForgetPasswordController;
use App\Http\Middleware\AuthCheck;
use Illuminate\Support\Facades\Mail;
use App\Models\SendPurposal;
use Illuminate\Support\Facades\Artisan;


Route::get('/clear-cache', function() {
    Artisan::call('cache:clear');
    Artisan::call('optimize');
    return "Cache is cleared!";
});

// Route::get('dashboard', [CustomAuthController::class, 'dashboard']); 
Route::get('/admin', [AuthenticatedSessionController::class, 'index'])->name('login');
Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.custom'); 
Route::get('registration', [AuthenticatedSessionController::class, 'registration'])->name('register-user');
Route::post('admin/registration', [AuthenticatedSessionController::class, 'registerUser'])->name('registerUser'); 

Route::get('admin/fortgot-password', [ForgetPasswordController::class, 'create'])->name('admin.forgotPassword');
Route::post('admin/fortgot-password', [ForgetPasswordController::class, 'store'])->name('admin.resetPassword');
Route::get('admin/reset-password/{token}', [ForgetPasswordController::class, 'showResetPasswordForm'])->name('reset.password.get');
Route::post('admin/reset-password', [ForgetPasswordController::class, 'submitResetPasswordForm'])->name('reset.password.post');


//****************************** Admin route start here ******************************************* */

Route::middleware([AuthCheck::class])->group(function(){

    Route::prefix('admin')->name('admin.')->group(function () {
        
        Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('adminDashboard');
        Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('adminlogout');
        Route::post('/change-password', [ChangePasswordController::class, 'changePassword'])->name('change.password');
        Route::post('/userprofile', [UserProfileController::class, 'store'])->name('profile.update');
        
        // user route
        Route::resource('users', UserController::class);
        Route::post('admin/users/user-ajax', [UserController::class, 'userAjax'])->name('userAjax');
        Route::post('change-user-status', [UserController::class, 'changeUserStatus'])->name('changeUserStatus');
        Route::post('admin/users/destory', [UserController::class, 'userdestory'])->name('userdestory');

        Route::put('update-device-token', [UserController::class, 'updateDeviceToken'])->name('update.device.token');
        Route::post('send-fcm-notification', [UserController::class, 'sendFcmNotification']);

        // Developer route
        Route::resource('developers', DeveloperController::class);
        Route::post('admin/developers/developer-ajax', [DeveloperController::class, 'developerAjax'])->name('developerAjax');
        Route::post('change-developers-status', [DeveloperController::class, 'ChangeDeveloperStatus'])->name('ChangeDeveloperStatus');
        Route::post('admin/developers/destory', [DeveloperController::class, 'developerdestory'])->name('developerdestory');

        // client user route
        // Route::resource('clientusers', ClientUserController::class);
        // Route::post('admin/clientusers/user-ajax', [ClientUserController::class, 'clientuserAjax'])->name('clientuserAjax');
        // Route::post('change-clientuser-status', [ClientUserController::class, 'ChangeClientUserStatus'])->name('ChangeClientUserStatus');
        // Route::post('admin/clientusers/destory', [ClientUserController::class, 'clientuserdestory'])->name('clientuserdestory');

        Route::post('/getStatelistByCountryId', [UserController::class, 'getStatelistByCountryId'])->name('getStatelistByCountryId');
        Route::post('/getCitylistByStateId', [UserController::class, 'getCitylistByStateId'])->name('getCitylistByStateId');
        
        // project route
        Route::resource('projects', ProjectController::class);
        Route::post('admin/projects/project-ajax', [ProjectController::class, 'projectAjax'])->name('projectAjax');
        Route::post('change-project-status', [ProjectController::class, 'changeProjectStatus'])->name('changeProjectStatus');
        Route::post('admin/projects/destory', [ProjectController::class, 'projectdestory'])->name('projectdestory');
        Route::post('/update-ProStatus', [ProjectController::class, 'updateProStatus'])->name('updateProStatus');
        
        Route::get('admin/view-fixed-invoice/{id}', [ProjectController::class, 'viewfixedinvoice'])->name('viewfixedinvoice');
        Route::get('/fixedinvoice-send/{invoiceId}', [ProjectController::class, 'generateFixedPdfandSendEmail'])->name('fixedinvoice.send');

        // category route
        Route::resource('category', CategoryController::class);
        Route::post('admin/category/category-ajax', [CategoryController::class, 'categoryAjax'])->name('categoryAjax');
        Route::post('change-category-status', [CategoryController::class, 'changeCategoryStatus'])->name('changeCategoryStatus');
        Route::post('admin/category/destory', [CategoryController::class, 'categorydestory'])->name('categorydestory');

        // project status route
        Route::resource('projectstatus', ProjectStatusController::class);
        Route::post('admin/projectstatus/projectstatus-ajax', [ProjectStatusController::class, 'projectstatusAjax'])->name('projectstatusAjax');
        Route::post('change-projectstatus-status', [ProjectStatusController::class, 'changeProjectstatusStatus'])->name('changeProjectstatusStatus');
        Route::post('admin/projectstatus/destory', [ProjectStatusController::class, 'projectstatusdestory'])->name('projectstatusdestory');

        // // quotation route
        Route::resource('quotation', QuotationController::class);
        Route::post('admin/quotation/quotation-ajax', [QuotationController::class, 'quotationAjax'])->name('quotationAjax');
        Route::post('change-quotation-status', [QuotationController::class, 'changeQuotationStatus'])->name('changeQuotationStatus');
        Route::post('admin/quotation/destory', [QuotationController::class, 'quotationdestory'])->name('quotationdestory');

        // quotation route
        // Route::resource('quotationmail', QuotationSendMailController::class);
        // Route::post('admin/quotationmail/quotationmail-ajax', [QuotationSendMailController::class, 'quotationmailAjax'])->name('quotationmailAjax');
        // Route::post('change-quotationmail-status', [QuotationSendMailController::class, 'changeQuotationMailStatus'])->name('changeQuotationMailStatus');
        // // Route::post('admin/quotationmail/destory', [QuotationSendMailController::class, 'quotationMaildestory'])->name('quotationMaildestory');

        // task route
        Route::resource('tasks', TaskController::class);
        Route::post('admin/tasks/task-ajax', [TaskController::class, 'taskAjax'])->name('taskAjax');
        Route::post('admin/tasks/destory', [TaskController::class, 'taskdestory'])->name('taskdestory');
        Route::post('/update-status', [TaskController::class, 'updateProjectStatus'])->name('update.status');

        Route::get('admin/taskshow/{project_id}/{developer_id}', [TaskController::class, 'taskshow'])->name('taskshow');

        Route::post('admin/showtasks/showtask-ajax', [TaskController::class, 'ShowtaskAjax'])->name('ShowtaskAjax');

        
        Route::resource('sendpurposal', SendPurposalController::class);
        Route::post('admin/sendpurposal/sendpurposal-ajax', [SendPurposalController::class, 'sendpurposalAjax'])->name('sendpurposalAjax');
        Route::post('change-sendpurposal-status', [SendPurposalController::class, 'changeSendPurposalStatus'])->name('changeSendPurposalStatus');

        Route::resource('roles', RoleController::class);
        Route::post('admin/roles/role-ajax', [RoleController::class, 'roleAjax'])->name('roleAjax');

        // project route
        Route::resource('projects-assign', ProjectAssignController::class);
        Route::post('admin/projects-assign/projectassign-ajax', [ProjectAssignController::class, 'projectassignAjax'])->name('projectassignAjax');
        Route::post('change-projectassign-status', [ProjectAssignController::class, 'changeProjectAssignStatus'])->name('changeProjectAssignStatus');
        Route::post('admin/projects-assign/destory', [ProjectAssignController::class, 'projectassigndestory'])->name('projectassigndestory');
        
        Route::resource('taskassign', TaskAssignController::class);
        Route::get('createtask/{id?}', [TaskAssignController::class, 'createtask'])->name('createtask');
        Route::post('admin/taskassign/task-ajax', [TaskAssignController::class, 'taskassignAjax'])->name('taskassignAjax');
        Route::post('admin/taskassign/destory', [TaskAssignController::class, 'taskassigndestory'])->name('taskassigndestory');
        Route::post('change-taskassign-status', [TaskAssignController::class, 'changeTaskAssignStatus'])->name('changeTaskAssignStatus');
        Route::post('TaskStatus', [TaskAssignController::class, 'TaskStatus'])->name('TaskStatus');

        // invoice route
        Route::resource('invoice', InvoicePaymentController::class);
        Route::post('admin/invoices/invoice-ajax', [InvoicePaymentController::class, 'invoicepaymentAjax'])->name('invoicepaymentAjax');
        Route::post('change-invoice-status', [InvoicePaymentController::class, 'ChangeInvoicePaymentStatus'])->name('ChangeInvoicePaymentStatus');
        Route::post('admin/invoice/destory', [InvoicePaymentController::class, 'invoicepaymentdestory'])->name('invoicepaymentdestory');
        
        Route::get('/send-invoice/{invoiceId}', [InvoicePaymentController::class, 'generatePdfAndSendEmail'])->name('send.invoice');

        // Route::get('invoice/pdf/{invoice}', [InvoicePaymentController::class, 'pdf']);
        // Route::get('invoice/{id}/send', [InvoicePaymentController::class, 'sendInvoice'])->name('invoice.send');

        Route::resource('payments', PaymentController::class);
        Route::post('admin/payments/payment-ajax', [PaymentController::class, 'paymentAjax'])->name('paymentAjax');
        Route::post('admin/payments/paymenthistory-ajax', [PaymentController::class, 'paymenthistoryAjax'])->name('paymenthistoryAjax');
       
        Route::resource('ticket-system', TicketController::class);
        Route::post('admin/ticket-system/task-ajax', [TicketController::class, 'ticketAjax'])->name('ticketAjax');
        Route::post('admin/ticket-system/destory', [TicketController::class, 'ticketdestory'])->name('ticketdestory');
        Route::post('change-ticketsystem-status', [TicketController::class, 'ChangeTicketStatus'])->name('ChangeTicketStatus');
    
        Route::resource('host-customer', HostCustomerController::class);
        Route::post('admin/host-customer/host-ajax', [HostCustomerController::class, 'hostAjax'])->name('hostAjax');
        Route::post('admin/host-customer/destory', [HostCustomerController::class, 'hostdestory'])->name('host-customer.destory');
    
        Route::resource('hostpayments', HostPaymentController::class);
        Route::post('admin/hostpayments/hostpayment-ajax', [HostPaymentController::class, 'hostpaymentAjax'])->name('hostpaymentAjax');
        Route::post('admin/hostpayments/hostpaymenthistory-ajax', [HostPaymentController::class, 'hostpaymenthistoryAjax'])->name('hostpaymenthistoryAjax');
       
        Route::get('/export', [HostPaymentController::class, 'exportHostPayments'])->name('hostpayments.export');

        Route::get('chat', [ChatController::class, 'index'])->name('chat.index');

        // Route::get('/messages', [ChatController::class, 'fetchMessages'])->name('chat.fetchMessages');
        // Route::get('/fetchUsers', [ChatController::class, 'fetchUsers'])->name('chat.fetchUsers');
        Route::post('/chat/getuserlist', [ChatController::class, 'getuserlist'])->name('chat.getuserlist');
        
        Route::post('/chat/sendMessage', [ChatController::class, 'sendMessage'])->name('chat.sendMessage');
        Route::post('/showMessage', [ChatController::class, 'showMessage'])->name('chat.showMessage');

        Route::get('notification', [NotificationController::class, 'fetchNotifications'])->name('fetchNotifications');
        Route::get('/notifications/count', [NotificationController::class, 'getNotificationCount'])->name('getNotificationCount');
        Route::post('/notifications/mark-read/{id?}', [NotificationController::class, 'markAsRead'])->name('markAsRead');
        Route::post('/notifications/delete/{id?}', [NotificationController::class, 'deleteNotification'])->name('deleteNotification');
    
        // invoice route
        Route::resource('attendances', AttendanceController::class);
        Route::post('admin/attendances/attendance-ajax', [AttendanceController::class, 'attendanceAjax'])->name('attendanceAjax');
        // Route::post('change-attendances-status', [AttendanceController::class, 'ChangeAttendanceStatus'])->name('ChangeAttendanceStatus');
        // Route::post('admin/attendances/destory', [AttendanceController::class, 'attendancedestory'])->name('attendancedestory');
        
        Route::get('/attendancesshow/{employee_id}', [AttendanceController::class, 'attendancesshow'])->name('attendancesshow');
        Route::post('admin/attendancesshow/attendanceshow-ajax', [AttendanceController::class, 'ShowattendanceAjax'])->name('ShowattendanceAjax');

    });
});
Route::get('/pathroute', function () {
    $assetdata = asset('/public/purposaldocument/1727357069invoice_mail.pdf');
    // dd($assetdata);
    
 });


