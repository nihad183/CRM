<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NewDossierController;
use App\Http\Controllers\ProfileController;

/*
|--------------------------------------------------------------------------
| AUTH ROUTES (Guest only)
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {

    Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');

    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');

    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');

    Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
});


/*
|--------------------------------------------------------------------------
| AUTHENTICATED ROUTES
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    /*
    |---------------------------
    | DASHBOARD
    |---------------------------
    */
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/data', [DashboardController::class, 'data'])->name('dashboard.data');


    /*
    |---------------------------
    | DOSSIERS
    |---------------------------
    */
    Route::get('/new-dossier', [NewDossierController::class, 'create'])->name('new-dossier');
    Route::post('/new-dossier/fiche-propose', [NewDossierController::class, 'storeFichePropose'])->name('fiche-propose.store');

    Route::get('/fiche-client', [NewDossierController::class, 'indexFicheClient'])->name('fiche-client');

    Route::get('/fiche-client/{fichePropose}/documents', [NewDossierController::class, 'editFicheClientDocuments'])->name('fiche-client.documents.edit');
    Route::post('/fiche-client/{fichePropose}/documents', [NewDossierController::class, 'updateFicheClientDocuments'])->name('fiche-client.documents.update');

    Route::get('/admin/client-conversion-requests', [NewDossierController::class, 'indexClientConversionRequests'])->name('admin.client-conversion-requests');
    Route::get('/admin/liste-de-comarecen', [NewDossierController::class, 'indexAdminUsers'])->name('admin.liste-de-comarecen');
    Route::get('/admin/competition-utilisateurs', [NewDossierController::class, 'indexAdminCompetition'])->name('admin.competition-utilisateurs');
    Route::post('/admin/client-conversion-requests/{fichePropose}/approve', [NewDossierController::class, 'approveClientConversionRequest'])->name('admin.client-conversion-requests.approve');
    Route::post('/admin/client-conversion-requests/{fichePropose}/reject', [NewDossierController::class, 'rejectClientConversionRequest'])->name('admin.client-conversion-requests.reject');

    Route::get('/fiche-propose', [NewDossierController::class, 'indexFichePropose'])->name('fiche-propose');
    Route::get('/fiche-propose/{fichePropose}', [NewDossierController::class, 'showFichePropose'])->name('fiche-propose.show');
    Route::get('/fiche-propose/{fichePropose}/historique', [NewDossierController::class, 'showFicheHistory'])->name('fiche-propose.history');

    Route::get('/fiche-propose/{fichePropose}/fiche-client', [NewDossierController::class, 'createFicheClientDocument'])->name('fiche-propose.fiche-client.create');
    Route::post('/fiche-propose/{fichePropose}/fiche-client', [NewDossierController::class, 'storeFicheClientDocument'])->name('fiche-propose.fiche-client.store');

    Route::get('/fiche-propose/{fichePropose}/resume/create', [NewDossierController::class, 'createFicheProposeResume'])->name('fiche-propose.resume.create');
    Route::post('/fiche-propose/{fichePropose}/resume', [NewDossierController::class, 'storeFicheProposeResume'])->name('fiche-propose.resume.store');

    Route::get('/fiche-propose/{fichePropose}/resume/{resume}/print', [NewDossierController::class, 'printFicheProposeResume'])->name('fiche-propose.resume.print');
    Route::get('/fiche-propose/{fichePropose}/resume/{resume}/pdf', [NewDossierController::class, 'downloadFicheProposeResumePdf'])->name('fiche-propose.resume.pdf');


    /*
    |---------------------------
    | PROFILE
    |---------------------------
    */
    Route::get('/profil', [ProfileController::class, 'index'])->name('profile');
    Route::put('/profil/update', [ProfileController::class, 'update'])->name('profile.update');

    Route::post('/profil/password', [ProfileController::class, 'changePassword'])->name('profile.password');


    /*
    |---------------------------
    | AUTH
    |---------------------------
    */
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
