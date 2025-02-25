<?php
//global
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\ForgotPassword;
use App\Http\Controllers\ListFeeController;
use App\Http\Controllers\MarbotController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\LogViewerController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ImamController;
use App\Http\Controllers\MasjidController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\ShalatController;
use App\Http\Controllers\FeeController;
use App\Http\Controllers\StatisticController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\RekapController;
use App\Http\Controllers\UserController;

// API
use App\Http\Controllers\API\APIController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\MusyrifController;
use App\Http\Controllers\StudentAttendanceController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\StudentMemorizationController;
use App\Http\Controllers\UserNotificationController;

Route::redirect('/', '/login');

//API
Route::middleware(['auth'])->group(function () {
    Route::get('/api/get-imam-schedule-data', [APIController::class, 'getImamScheduleData']);
    Route::get('/api/get-masjid-schedule-data', [APIController::class, 'getMasjidScheduleData']);
    Route::get('/api/get-masjid-shalat-schedule-data', [APIController::class, 'getMasjidShalatScheduleData']);
    Route::get('/api/get-notifications', [UserNotificationController::class, 'getNotifications']);
    Route::post('/api/mark-notification-as-read', [UserNotificationController::class, 'markNotificationAsRead']);
});

// Routes untuk Login dan Logout
Route::get('login', [LoginController::class, 'index'])->name('login.index');
Route::post('login', [LoginController::class, 'login'])->name('login');
Route::get('register', [RegisterController::class, 'index'])->name('register');
Route::get('register/imam', [RegisterController::class, 'imam'])->name('register.imam');
Route::get('register/student', [RegisterController::class, 'student'])->name('register.student');
Route::post('register/imam', [RegisterController::class, 'storeImam'])->name('register.imam.store');
Route::post('register/student', [RegisterController::class, 'storeStudent'])->name('register.student.store');
Route::get('clear-cookie', [LoginController::class, 'clearCookie'])->name('clear.cookie');

Route::post('logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/forgot-password', [ForgotPassword::class, 'index'])->middleware('guest')->name('password.request');
Route::post('/forgot-password', [ForgotPassword::class, 'email'])->middleware('guest')->name('password.email');
Route::get('/reset-password/{token}', [ForgotPassword::class, 'reset'])->middleware('guest')->name('password.reset');
Route::post('/reset-password', [ForgotPassword::class, 'update'])->middleware('guest')->name('password.update');

Route::get('/email/verify', [EmailVerificationController::class, 'notice'])->middleware('auth')->name('verification.notice');
Route::post('/email/verification-notification', [EmailVerificationController::class, 'resend', 'throttle:6,1'])->middleware('auth')->name('verification.send');
Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])->middleware('auth')->name('verification.verify');

// Routes untuk Account
Route::middleware(['auth'])->group(function () {
    Route::get('/account', [AccountController::class, 'index'])->name('account');
    Route::post('/account/shortcut', [AccountController::class, 'storeShortcut'])->name('account.shortcut');
    Route::put('/account/{user}/update', [AccountController::class, 'update'])->middleware('verified')->name('account.update');
});

// Routes untuk SuperAdmin
Route::prefix('superadmin')->name('superadmin.')->middleware(['auth', 'checkRole:superadmin'])->group(function () {
    Route::redirect('/', '/superadmin/home');
    Route::get('/home', [HomeController::class, 'adminHome'])->name('home');
    Route::get('/log-viewer', [LogViewerController::class, 'index'])->name('logs');
    Route::get('/log-viewer/show/{filename}', [LogViewerController::class, 'show'])->name('logs.show');
    Route::delete('/log-viewer/delete/{filename}', [LogViewerController::class, 'destroy'])->name('logs.destroy');
    Route::get('/log-viewer/download/{filename}', [LogViewerController::class, 'download'])->name('logs.download');

    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [AdminController::class, 'index'])->name('index');
        Route::get('/create', [AdminController::class, 'create'])->name('create');
        Route::post('/create', [AdminController::class, 'store'])->name('store');
        Route::get('/{admin}/edit', [AdminController::class, 'edit'])->name('edit');
        Route::put('/{admin}/edit', [AdminController::class, 'update'])->name('update');
        Route::delete('/{admin}/delete', [AdminController::class, 'destroy'])->name('destroy');

        Route::get('/{admin}/permissions', [AdminController::class, 'permissions'])->name('permissions');
        Route::post('/{admin}/permissions', [AdminController::class, 'permissionsStore'])->name('permissions.store');
        Route::get('/{admin}/permissions/edit', [AdminController::class, 'permissionsEdit'])->name('permissions.edit');
        Route::put('/{admin}/permissions/edit', [AdminController::class, 'permissionsUpdate'])->name('permissions.update');
        Route::delete('/{admin}/permissions/{permission}/delete', [AdminController::class, 'permissionsDestroy'])->name('permissions.destroy');
    });

    Route::prefix('user')->name('user.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/{user}/reset-password', [UserController::class, 'resetPassword'])->name('resetPassword');
        Route::delete('/{user}/delete', [UserController::class, 'destroy'])->name('destroy');
    });
});

// Routes untuk Admin
Route::prefix('admin')->name('admin.')->middleware(['auth', 'checkRole:admin'])->group(function () {
    Route::redirect('/', '/admin/home');
    Route::get('/home', [HomeController::class, 'adminHome'])->name('home');
    Route::put('/account', [AccountController::class, 'updateAdmin'])->middleware('verified')->name('update');

    Route::prefix('imam')->middleware(['auth', 'permission:imam_show'])->name('imam.')->group(function () {
        Route::get('/', [ImamController::class, 'index'])->name('index');
        Route::get('/{imam}/show', [ImamController::class, 'show'])->name('show');
        Route::get('/create', [ImamController::class, 'create'])->middleware(['auth', 'permission:imam_create'])->name('create');
        Route::post('/create', [ImamController::class, 'store'])->middleware(['auth', 'permission:imam_create'])->name('store');
        Route::get('/{imam}/edit', [ImamController::class, 'edit'])->middleware(['auth', 'permission:imam_edit'])->name('edit');
        Route::put('/{imam}/edit', [ImamController::class, 'update'])->middleware(['auth', 'permission:imam_edit'])->name('update');
        Route::delete('/{imam}/delete', [ImamController::class, 'destroy'])->middleware(['auth', 'permission:imam_delete'])->name('destroy');

        Route::get('/{imam}/detail', [ImamController::class, 'detail'])->middleware(['auth', 'permission:imam_detail'])->name('detail');
        Route::get('/active', [ImamController::class, 'isActive'])->name('is_active');
        Route::put('/active/{imam}', [ImamController::class, 'isActiveUpdate'])->name('is_active.update');
        Route::put('/active/{imam}/false', [ImamController::class, 'isActiveUpdateFalse'])->name('is_active.update.false');
    });
    Route::prefix('student')->middleware(['auth', 'permission:student_show'])->name('student.')->group(function () {
        Route::get('/', [StudentController::class, 'index'])->name('index');
        Route::get('/{student}/show', [StudentController::class, 'show'])->name('show');
        Route::get('/create', [StudentController::class, 'create'])->middleware(['auth', 'permission:student_create'])->name('create');
        Route::post('/create', [StudentController::class, 'store'])->middleware(['auth', 'permission:student_create'])->name('store');
        Route::get('/{student}/edit', [StudentController::class, 'edit'])->middleware(['auth', 'permission:student_edit'])->name('edit');
        Route::put('/{student}/edit', [StudentController::class, 'update'])->middleware(['auth', 'permission:student_edit'])->name('update');
        Route::delete('/{student}/delete', [StudentController::class, 'destroy'])->middleware(['auth', 'permission:student_delete'])->name('destroy');

        Route::get('/{student}/detail', [StudentController::class, 'detail'])->middleware(['auth', 'permission:student_detail'])->name('detail');
        Route::get('/active', [StudentController::class, 'isActive'])->name('is_active');
        Route::put('/active/{student}', [StudentController::class, 'isActiveUpdate'])->name('is_active.update');
        Route::put('/active/{student}/false', [StudentController::class, 'isActiveUpdateFalse'])->name('is_active.update.false');

        Route::prefix('memorization')->middleware(['auth', 'permission:memorization_show'])->name('memorization.')->group(function () {
            Route::get('/', [StudentMemorizationController::class, 'index'])->name('index');
            Route::get('/{memorization}/show', [StudentMemorizationController::class, 'show'])->name('show');
            Route::get('/create', [StudentMemorizationController::class, 'create'])->middleware(['auth', 'permission:memorization_create'])->name('create');
            Route::post('/create', [StudentMemorizationController::class, 'store'])->middleware(['auth', 'permission:memorization_create'])->name('store');
            Route::get('/{memorization}/edit', [StudentMemorizationController::class, 'edit'])->middleware(['auth', 'permission:memorization_edit'])->name('edit');
            Route::put('/{memorization}/edit', [StudentMemorizationController::class, 'update'])->middleware(['auth', 'permission:memorization_edit'])->name('update');
            Route::delete('/{memorization}/delete', [StudentMemorizationController::class, 'destroy'])->middleware(['auth', 'permission:memorization_delete'])->name('destroy');

            Route::post('/{memorization}/is-continue-true', [StudentMemorizationController::class, 'isContinueTrue'])->middleware(['auth', 'permission:memorization_edit'])->name('isContinueTrue');
            Route::post('/{memorization}/is-continue-false', [StudentMemorizationController::class, 'isContinueFalse'])->middleware(['auth', 'permission:memorization_edit'])->name('isContinueFalse');
        });
    });
    Route::prefix('masjid')->middleware(['auth', 'permission:masjid_show'])->name('masjid.')->group(function () {
        Route::get('/', [MasjidController::class, 'index'])->name('index');
        Route::get('/create', [MasjidController::class, 'create'])->middleware(['auth', 'permission:masjid_create'])->name('create');
        Route::post('/create', [MasjidController::class, 'store'])->middleware(['auth', 'permission:masjid_create'])->name('store');
        Route::get('/{masjid}/edit', [MasjidController::class, 'edit'])->middleware(['auth', 'permission:masjid_edit'])->name('edit');
        Route::put('/{masjid}/edit', [MasjidController::class, 'update'])->middleware(['auth', 'permission:masjid_edit'])->name('update');
        Route::delete('/{masjid}/delete', [MasjidController::class, 'destroy'])->middleware(['auth', 'permission:masjid_delete'])->name('destroy');
    });
    Route::prefix('shalat')->middleware(['auth', 'permission:shalat_show'])->name('shalat.')->group(function () {
        Route::get('/', [ShalatController::class, 'index'])->name('index');
        Route::get('/create', [ShalatController::class, 'create'])->middleware(['auth', 'permission:shalat_create'])->name('create');
        Route::post('/create', [ShalatController::class, 'store'])->middleware(['auth', 'permission:shalat_create'])->name('store');
        Route::get('/{shalat}/edit', [ShalatController::class, 'edit'])->middleware(['auth', 'permission:shalat_edit'])->name('edit');
        Route::put('/{shalat}/edit', [ShalatController::class, 'update'])->middleware(['auth', 'permission:shalat_edit'])->name('update');
        Route::delete('/{shalat}/delete', [ShalatController::class, 'destroy'])->middleware(['auth', 'permission:shalat_delete'])->name('destroy');
    });
    Route::prefix('jadwal')->middleware(['auth', 'permission:jadwal_show'])->name('jadwal.')->group(function () {
        Route::get('/', [ScheduleController::class, 'index'])->name('index');
        Route::get('/fetch', [ScheduleController::class, 'fetch'])->name('fetch');
        Route::get('/create', [ScheduleController::class, 'create'])->middleware(['auth', 'permission:jadwal_create'])->name('create');
        Route::post('/create', [ScheduleController::class, 'store'])->middleware(['auth', 'permission:jadwal_create'])->name('store');

        Route::post('/updateJSON', [ScheduleController::class, 'updateJSON'])->name('updateJSON');
        Route::get('/{schedule}/edit', [ScheduleController::class, 'edit'])->middleware(['auth', 'permission:jadwal_edit'])->name('edit');
        Route::put('/{schedule}/edit', [ScheduleController::class, 'update'])->middleware(['auth', 'permission:jadwal_edit'])->name('update');
        Route::delete('/{schedule}/delete', [ScheduleController::class, 'destroy'])->middleware(['auth', 'permission:jadwal_delete'])->name('destroy');
        Route::delete('/delete-selected', [ScheduleController::class, 'destroySelected'])->middleware(['auth', 'permission:jadwal_delete'])->name('destroySelected');

        Route::get('/cache', [ScheduleController::class, 'cache'])->name('cache');
        Route::post('/clear-cache', [ScheduleController::class, 'clearCache'])->name('clearCache');
    });
    Route::prefix('bayaran')->middleware(['auth', 'permission:bayaran_show'])->name('bayaran.')->group(function () {
        Route::get('/', [FeeController::class, 'index'])->name('index');
        Route::get('/create', [FeeController::class, 'create'])->middleware(['auth', 'permission:bayaran_create'])->name('create');
        Route::post('/create', [FeeController::class, 'store'])->middleware(['auth', 'permission:bayaran_create'])->name('store');
        Route::get('/{fee}/edit', [FeeController::class, 'edit'])->middleware(['auth', 'permission:bayaran_edit'])->name('edit');
        Route::put('/{fee}/edit', [FeeController::class, 'update'])->middleware(['auth', 'permission:bayaran_edit'])->name('update');
        Route::delete('/{fee}/delete', [FeeController::class, 'destroy'])->middleware(['auth', 'permission:bayaran_delete'])->name('destroy');

        Route::get('/{id}/list', [ListFeeController::class, 'index'])->middleware(['auth', 'permission:bayaran_list'])->name('list.index');
        Route::post('/{id}/list/create', [ListFeeController::class, 'store'])->middleware(['auth', 'permission:bayaran_list'])->name('list.store');

        Route::delete('/list/delete/{listFee}', [ListFeeController::class, 'destroy'])->middleware(['auth', 'permission:bayaran_list'])->name('list.destroy');
    });
    Route::prefix('statistik')->middleware(['auth', 'permission:statistik_show'])->name('statistik.')->group(function () {
        Route::get('/', [StatisticController::class, 'statistik'])->name('index');
    });
    Route::prefix('rekap')->middleware(['auth', 'permission:rekap_show'])->name('rekap.')->group(function () {
        Route::get('/berdasarkan-imam', [RekapController::class, 'berdasarkanImam'])->middleware(['auth', 'permission:rekap_berdasarkan_imam'])->name('berdasarkan-imam.index');
        // Route::get('/berdasarkan-imam/export', [RekapController::class, 'exportBerdasarkanImam'])->name('berdasarkan-imam.export');
        Route::get('/berdasarkan-shalat', [RekapController::class, 'berdasarkanShalat'])->middleware(['auth', 'permission:rekap_berdasarkan_shalat'])->name('berdasarkan-shalat.index');
    });
    Route::prefix('pengumuman')->middleware(['auth', 'permission:pengumuman_show'])->name('pengumuman.')->group(function () {
        Route::get('/', [AnnouncementController::class, 'index'])->name('index');
        Route::get('/create', [AnnouncementController::class, 'create'])->middleware(['auth', 'permission:pengumuman_create'])->name('create');
        Route::post('/create', [AnnouncementController::class, 'store'])->middleware(['auth', 'permission:pengumuman_create'])->name('store');
        Route::get('/{announcement}/edit', [AnnouncementController::class, 'edit'])->middleware(['auth', 'permission:pengumuman_edit'])->name('edit');
        Route::put('/{announcement}/edit', [AnnouncementController::class, 'update'])->middleware(['auth', 'permission:pengumuman_edit'])->name('update');
        Route::delete('/{announcement}/delete', [AnnouncementController::class, 'destroy'])->middleware(['auth', 'permission:pengumuman_delete'])->name('destroy');
    });
    Route::prefix('quote')->middleware(['auth', 'permission:quote_show'])->name('quote.')->group(function () {
        Route::get('/', [QuoteController::class, 'index'])->name('index');

        Route::get('/create', [QuoteController::class, 'create'])->middleware(['auth', 'permission:quote_create'])->name('create');
        Route::post('/create', [QuoteController::class, 'store'])->middleware(['auth', 'permission:quote_create'])->name('store');
        Route::get('/{quote}/edit', [QuoteController::class, 'edit'])->middleware(['auth', 'permission:quote_edit'])->name('edit');
        Route::put('/{quote}/edit', [QuoteController::class, 'update'])->middleware(['auth', 'permission:quote_edit'])->name('update');
        Route::delete('/{quote}/delete', [QuoteController::class, 'destroy'])->middleware(['auth', 'permission:quote_delete'])->name('destroy');
        Route::put('/{quote}/status', [QuoteController::class, 'toggleStatus'])->middleware(['auth', 'permission:quote_edit'])->name('status');
    });
    Route::prefix('marbot')->middleware(['auth', 'permission:marbot_show'])->name('marbot.')->group(function () {
        Route::get('/', [MarbotController::class, 'index'])->name('index');
        Route::get('/create', [MarbotController::class, 'create'])->middleware(['auth', 'permission:marbot_create'])->name('create');
        Route::post('/create', [MarbotController::class, 'store'])->middleware(['auth', 'permission:marbot_create'])->name('store');
        Route::get('/{marbot}/edit', [MarbotController::class, 'edit'])->middleware(['auth', 'permission:marbot_edit'])->name('edit');
        Route::put('/{marbot}/edit', [MarbotController::class, 'update'])->middleware(['auth', 'permission:marbot_edit'])->name('update');
        Route::delete('/{marbot}/delete', [MarbotController::class, 'destroy'])->middleware(['auth', 'permission:marbot_delete'])->name('destroy');
    });
    Route::prefix('musyrif')->name('musyrif.')->group(function () {
        Route::get('/', [MusyrifController::class, 'index'])->name('index');
        Route::get('/create', [MusyrifController::class, 'create'])->name('create');
        Route::post('/create', [MusyrifController::class, 'store'])->name('store');
        Route::get('/{musyrif}/edit', [MusyrifController::class, 'edit'])->name('edit');
        Route::put('/{musyrif}/edit', [MusyrifController::class, 'update'])->name('update');
        Route::delete('/{musyrif}/delete', [MusyrifController::class, 'destroy'])->name('destroy');
    });
});

// Routes untuk Imam
Route::prefix('imam')->name('imam.')->middleware(['auth', 'checkRole:imam'])->group(function () {
    Route::redirect('/', '/imam/home');
    Route::get('/home', [HomeController::class, 'imamHome'])->name('home');
    Route::put('/account', [AccountController::class, 'updateImam'])->middleware('verified')->name('update');

    Route::prefix('jadwal')->name('jadwal.')->group(function () {
        Route::get('/', [ScheduleController::class, 'index'])->name('index');
        Route::get('/fetch', [ScheduleController::class, 'fetch'])->name('fetch');
        Route::post('/updateJSON', [ScheduleController::class, 'updateJSON'])->name('updateJSON');
        Route::get('/create', [ScheduleController::class, 'create'])->name('create');
        Route::post('/create', [ScheduleController::class, 'store'])->name('store');
        Route::get('/{schedule}/edit', [ScheduleController::class, 'edit'])->name('edit');
        Route::put('/{schedule}/edit', [ScheduleController::class, 'update'])->name('update');
        Route::delete('/{schedule}/delete', [ScheduleController::class, 'destroy'])->name('destroy');

        Route::post('/{schedule}/cari-badal', [ScheduleController::class, 'imamCariBadal'])->name('cariBadal');
        Route::post('/{schedule}/done', [ScheduleController::class, 'imamDone'])->name('done');
        Route::post('/{schedule}/cancel', [ScheduleController::class, 'imamCancel'])->name('cancel');
    });
    Route::prefix('student')->middleware(['auth'])->name('student.')->group(function () {
        Route::prefix('memorization')->middleware(['auth'])->name('memorization.')->group(function () {
            Route::get('/', [StudentMemorizationController::class, 'index'])->name('index');
            Route::get('/{memorization}/show', [StudentMemorizationController::class, 'show'])->name('show');
            Route::get('/create', [StudentMemorizationController::class, 'create'])->name('create');
            Route::post('/create', [StudentMemorizationController::class, 'store'])->name('store');
            Route::get('/{memorization}/edit', [StudentMemorizationController::class, 'edit'])->name('edit');
            Route::put('/{memorization}/edit', [StudentMemorizationController::class, 'update'])->name('update');
            Route::delete('/{memorization}/delete', [StudentMemorizationController::class, 'destroy'])->name('destroy');

            Route::post('/{memorization}/is-continue-true', [StudentMemorizationController::class, 'isContinueTrue'])->name('isContinueTrue');
            Route::post('/{memorization}/is-continue-false', [StudentMemorizationController::class, 'isContinueFalse'])->name('isContinueFalse');
        });
    });
});
Route::prefix('student')->middleware(['auth', 'checkRole:student'])->name('student.')->group(function () {
    Route::redirect('/', '/student/home');
    Route::get('/home', [HomeController::class, 'studentHome'])->name('home');

    Route::prefix('student')->name('student.')->group(function () {
        Route::prefix('memorization')->middleware(['auth'])->name('memorization.')->group(function () {
            Route::get('/', [StudentMemorizationController::class, 'index'])->name('index');
            Route::get('/{memorization}/show', [StudentMemorizationController::class, 'show'])->name('show');
            Route::get('/create', [StudentMemorizationController::class, 'create'])->name('create');
            Route::post('/create', [StudentMemorizationController::class, 'store'])->name('store');
            Route::get('/{memorization}/edit', [StudentMemorizationController::class, 'edit'])->name('edit');
            Route::put('/{memorization}/edit', [StudentMemorizationController::class, 'update'])->name('update');
            Route::delete('/{memorization}/delete', [StudentMemorizationController::class, 'destroy'])->name('destroy');

            Route::post('/{memorization}/is-continue-true', [StudentMemorizationController::class, 'isContinueTrue'])->name('isContinueTrue');
            Route::post('/{memorization}/is-continue-false', [StudentMemorizationController::class, 'isContinueFalse'])->name('isContinueFalse');
        });

        Route::prefix('attendance')->name('attendance.')->group(function () {
            Route::get('/', [StudentAttendanceController::class, 'index'])->name('index');
            Route::get('/{attendance}/show', [StudentAttendanceController::class, 'show'])->name('show');
            Route::get('/create', [StudentAttendanceController::class, 'create'])->name('create');
            Route::post('/create', [StudentAttendanceController::class, 'store'])->name('store');
            Route::get('/{attendance}/edit', [StudentAttendanceController::class, 'edit'])->name('edit');
            Route::put('/{attendance}/edit', [StudentAttendanceController::class, 'update'])->name('update');
            Route::delete('/{attendance}/delete', [StudentAttendanceController::class, 'destroy'])->name('destroy');
        });
    });
});
Route::prefix('musyrif')->middleware(['auth', 'checkRole:musyrif'])->name('musyrif.')->group(function () {
    Route::redirect('/', '/musyrif/home');
    Route::get('/home', [HomeController::class, 'musyrifHome'])->name('home');

    Route::prefix('student')->name('student.')->group(function () {
        Route::prefix('attendance')->name('attendance.')->group(function () {
            Route::get('/', [StudentAttendanceController::class, 'index'])->name('index');
            Route::get('/{attendance}/show', [StudentAttendanceController::class, 'show'])->name('show');
            Route::get('/create', [StudentAttendanceController::class, 'create'])->name('create');
            Route::post('/create', [StudentAttendanceController::class, 'store'])->name('store');
            Route::post('/create-multiple', [StudentAttendanceController::class, 'storeMultiple'])->name('storeMultiple');
            Route::get('/{attendance}/edit', [StudentAttendanceController::class, 'edit'])->name('edit');
            Route::put('/{attendance}/edit', [StudentAttendanceController::class, 'update'])->name('update');
            Route::delete('/{attendance}/delete', [StudentAttendanceController::class, 'destroy'])->name('destroy');
        });
    });
});
