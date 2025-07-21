<?php

use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\AuthPageController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\NoticeBoardController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\FAQController;
use App\Http\Controllers\HomePageController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OTPController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PaymentController;
use App\Models\User;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ServicePartController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\WORequestController;
use App\Http\Controllers\EstimationController;
use App\Http\Controllers\WorkOrderController;
use App\Http\Controllers\WOTypeController;
use App\Http\Controllers\InvoiceController;

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

require __DIR__ . '/auth.php';

Route::get('/', [HomeController::class,'index'])->middleware(
    [

        'XSS',
    ]
);
Route::get('home', [HomeController::class,'index'])->name('home')->middleware(
    [

        'XSS',
    ]
);
Route::get('dashboard', [HomeController::class,'index'])->name('dashboard')->middleware(
    [

        'XSS',
    ]
);

//-------------------------------User-------------------------------------------

Route::resource('users', UserController::class)->middleware(
    [
        'auth',
        'XSS',
    ]
);


Route::get('login/otp', [OTPController::class, 'show'])->name('otp.show')->middleware(
    [

        'XSS',
    ]
);
Route::post('login/otp', [OTPController::class, 'check'])->name('otp.check')->middleware(
    [

        'XSS',
    ]
);
Route::get('login/2fa/disable', [OTPController::class, 'disable'])->name('2fa.disable')->middleware(['XSS',]);

//-------------------------------Subscription-------------------------------------------

Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
        ],
    ], function (){

    Route::resource('subscriptions', SubscriptionController::class);
    Route::get('coupons/history', [CouponController::class,'history'])->name('coupons.history');
    Route::delete('coupons/history/{id}/destroy', [CouponController::class,'historyDestroy'])->name('coupons.history.destroy');
    Route::get('coupons/apply', [CouponController::class, 'apply'])->name('coupons.apply');
    Route::resource('coupons', CouponController::class);
    Route::get('subscription/transaction', [SubscriptionController::class,'transaction'])->name('subscription.transaction');
}
);

//-------------------------------Subscription Payment-------------------------------------------

Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
        ],
    ], function (){

    Route::post('subscription/{id}/stripe/payment', [SubscriptionController::class,'stripePayment'])->name('subscription.stripe.payment');
}
);
//-------------------------------Settings-------------------------------------------
Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
        ],
    ], function (){
    Route::get('settings', [SettingController::class,'index'])->name('setting.index');

    Route::post('settings/account', [SettingController::class,'accountData'])->name('setting.account');
    Route::delete('settings/account/delete', [SettingController::class,'accountDelete'])->name('setting.account.delete');
    Route::post('settings/password', [SettingController::class,'passwordData'])->name('setting.password');
    Route::post('settings/general', [SettingController::class,'generalData'])->name('setting.general');
    Route::post('settings/smtp', [SettingController::class,'smtpData'])->name('setting.smtp');
    Route::get('settings/smtp-test', [SettingController::class, 'smtpTest'])->name('setting.smtp.test');
    Route::post('settings/smtp-test', [SettingController::class, 'smtpTestMailSend'])->name('setting.smtp.testing');
    Route::post('settings/payment', [SettingController::class,'paymentData'])->name('setting.payment');
    Route::post('settings/site-seo', [SettingController::class,'siteSEOData'])->name('setting.site.seo');
    Route::post('settings/google-recaptcha', [SettingController::class,'googleRecaptchaData'])->name('setting.google.recaptcha');
    Route::post('settings/company', [SettingController::class,'companyData'])->name('setting.company');
    Route::post('settings/2fa', [SettingController::class, 'twofaEnable'])->name('setting.twofa.enable');

    Route::get('footer-setting', [SettingController::class, 'footerSetting'])->name('footerSetting');
    Route::post('settings/footer', [SettingController::class,'footerData'])->name('setting.footer');

    Route::get('language/{lang}', [SettingController::class,'lanquageChange'])->name('language.change');
    Route::post('theme/settings', [SettingController::class,'themeSettings'])->name('theme.settings');
}
);


//-------------------------------Role & Permissions-------------------------------------------
Route::resource('permission', PermissionController::class)->middleware(
    [
        'auth',
        'XSS',
    ]
);

Route::resource('role', RoleController::class)->middleware(
    [
        'auth',
        'XSS',
    ]
);

//-------------------------------Note-------------------------------------------
Route::resource('note', NoticeBoardController::class)->middleware(
    [
        'auth',
        'XSS',
    ]
);

//-------------------------------Contact-------------------------------------------
Route::resource('contact', ContactController::class)->middleware(
    [
        'auth',
        'XSS',
    ]
);

//-------------------------------logged History-------------------------------------------

Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
        ],
    ], function () {

    Route::get('logged/history', [UserController::class,'loggedHistory'])->name('logged.history');
    Route::get('logged/{id}/history/show', [UserController::class,'loggedHistoryShow'])->name('logged.history.show');
    Route::delete('logged/{id}/history', [UserController::class,'loggedHistoryDestroy'])->name('logged.history.destroy');
});


//-------------------------------Plan Payment-------------------------------------------
Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
        ],
    ], function (){
    Route::post('subscription/{id}/bank-transfer', [PaymentController::class, 'subscriptionBankTransfer'])->name('subscription.bank.transfer');
    Route::get('subscription/{id}/bank-transfer/action/{status}', [PaymentController::class, 'subscriptionBankTransferAction'])->name('subscription.bank.transfer.action');
    Route::post('subscription/{id}/paypal', [PaymentController::class, 'subscriptionPaypal'])->name('subscription.paypal');
    Route::get('subscription/{id}/paypal/{status}', [PaymentController::class, 'subscriptionPaypalStatus'])->name('subscription.paypal.status');
    Route::post('subscription/{id}/{user_id}/manual-assign-package', [PaymentController::class, 'subscriptionManualAssignPackage'])->name('subscription.manual_assign_package');
    Route::get('subscription/flutterwave/{sid}/{tx_ref}', [PaymentController::class, 'subscriptionFlutterwave'])->name('subscription.flutterwave');
}
);

//-------------------------------Notification-------------------------------------------
Route::resource('notification', NotificationController::class)->middleware(
    [
        'auth',
        'XSS',

    ]
 );

 Route::get('email-verification/{token}', [VerifyEmailController::class, 'verifyEmail'])->name('email-verification')->middleware(
    [
        'XSS',
    ]
);

//-------------------------------FAQ-------------------------------------------
Route::resource('FAQ', FAQController::class)->middleware(
    [
        'auth',
        'XSS',
    ]
);

//-------------------------------Home Page-------------------------------------------
Route::resource('homepage', HomePageController::class)->middleware(
    [
        'auth',
        'XSS',
    ]
);
//-------------------------------FAQ-------------------------------------------
Route::resource('pages', PageController::class)->middleware(
    [
        'auth',
        'XSS',
    ]
);

//-------------------------------Auth page-------------------------------------------
Route::resource('authPage', AuthPageController::class)->middleware(
    [
        'auth',
        'XSS',
    ]
);

//-------------------------------Client-------------------------------------------
Route::resource('client', ClientController::class)->middleware(
    [
        'auth',
        'XSS',
    ]
);

//-------------------------------Services & Parts-------------------------------------------
Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
        ],
    ], function () {
    Route::delete('services/tasks', [ServicePartController::class, 'taskDestroy'])->name('service.task.destroy');
    Route::resource('services-parts', ServicePartController::class);
}
);

//-------------------------------Asset-------------------------------------------
Route::resource('asset', AssetController::class)->middleware(
    [
        'auth',
        'XSS',
    ]
);
//-------------------------------WO Request-------------------------------------------
Route::resource('wo-request', WORequestController::class)->middleware(
    [
        'auth',
        'XSS',
    ]
);
//-------------------------------Estimation-------------------------------------------
Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
        ],
    ], function () {

    Route::get('estimation/{id}/status', [EstimationController::class,'estimationStatus'])->name('estimation.status');
    Route::delete('estimation/service/part/destroy', [EstimationController::class,'servicePartDestroy'])->name('estimation.service.part.destroy');
    Route::get('estimation/service/part', [EstimationController::class, 'getServicePart'])->name('estimation.service.part');
    Route::resource('estimation', EstimationController::class);
}
);


//-------------------------------Work Order-------------------------------------------
Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
        ],
    ], function () {
    Route::get('workorder/{id}/service/task/create', [WorkOrderController::class,'serviceTaskCreate'])->name('workorder.service.task.create');
    Route::post('workorder/{id}/service/task/store', [WorkOrderController::class,'serviceTaskStore'])->name('workorder.service.task.store');
    Route::get('workorder/{id}/service/task/{tid}/edit', [WorkOrderController::class,'serviceTaskEdit'])->name('workorder.service.task.edit');
    Route::put('workorder/{id}/service/task/{tid}/update', [WorkOrderController::class,'serviceTaskUpdate'])->name('workorder.service.task.update');
    Route::delete('workorder/{id}/service/task/{tid}/delete', [WorkOrderController::class,'serviceTaskDestroy'])->name('workorder.service.task.destroy');

    Route::get('workorder/{id}/service/appointment', [WorkOrderController::class,'serviceAppointment'])->name('workorder.service.appointment');
    Route::put('workorder/{id}/service/appointment/store', [WorkOrderController::class,'serviceAppointmentStore'])->name('workorder.service.appointment.store');
    Route::delete('workorder/{id}/service/appointment/delete', [WorkOrderController::class,'serviceAppointmentDestroy'])->name('workorder.service.appointment.destroy');

    Route::get('workorder/{id}/status', [WorkOrderController::class,'workorderStatus'])->name('workorder.status');
    Route::delete('workorder/service/part/destroy', [WorkOrderController::class,'servicePartDestroy'])->name('workorder.service.part.destroy');
    Route::get('workorder/service/part', [WorkOrderController::class, 'getServicePart'])->name('workorder.service.part');
    Route::resource('workorder', WorkOrderController::class);
}
);


//-------------------------------WO Type-------------------------------------------
Route::resource('wo-type', WOTypeController::class)->middleware(
    [
        'auth',
        'XSS',
    ]
);

//-------------------------------Invoice-------------------------------------------

Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
        ],
    ], function () {
    Route::get('client/workorder/list', [InvoiceController::class,'getWorkorder'])->name('client.workorder');
    Route::get('workorders/details', [InvoiceController::class,'getWorkorderDetails'])->name('workorder.details');
    Route::resource('invoice', InvoiceController::class);
}
);

Route::get('page/{slug}', [PageController::class, 'page'])->name('page');
//-------------------------------FAQ-------------------------------------------
Route::impersonate();

