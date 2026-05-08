<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\CandidateController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PackController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\ResultController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\VoteController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\VotingController;
use Illuminate\Support\Facades\Route;

Route::get('/',           [PublicController::class, 'home'])->name('home');
Route::get('/candidates', [VotingController::class, 'showCandidates'])->name('candidates');
Route::get('/resultats',  [PublicController::class, 'results'])->name('results');
// ── Vote AJAX ────────────────────────────────────────────────────────────────
Route::post('/voter', [VotingController::class, 'process'])->name('vote.process');
Route::post('/vote/confirm', [VotingController::class, 'confirmVote'])->name('vote.confirm');

// ── Routes FedaPay ───────────────────────────────────────────────────────────
// Le Webhook : appelé par le serveur FedaPay (Notification invisible)
Route::get('/fedapay/webhook', [VotingController::class, 'callback'])->name('voting.callback');


// ── Statut de paiement (polling optionnel) ───────────────────────────────────
Route::get('/vote/status/{transactionId}', [VotingController::class, 'checkStatus'])->name('vote.status');


Route::get('/login',         [AuthController::class, 'showLogin'])->name('login');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::post('/login',        [AuthController::class, 'login'])->name('login.post');
    Route::post('/logout',       [AuthController::class, 'logout'])->name('logout');
});


Route::prefix('admin')->name('admin.')->middleware('admin.auth')->group(function () {

    // Dashboard
    Route::get('/',              [DashboardController::class, 'index'])->name('dashboard');

    // Candidates
    Route::resource('candidates', CandidateController::class)
         ->except(['show']);
    Route::patch('candidates/{candidate}/toggle',
                 [CandidateController::class, 'toggleActive'])->name('candidates.toggle');

    // Votes
    Route::get('votes',          [VoteController::class, 'index'])->name('votes.index');

    // Paiements
    Route::get('payments',       [PaymentController::class, 'index'])->name('payments.index');
    Route::get('payments/export',[PaymentController::class, 'export'])->name('payments.export');

    // Packs
    Route::get('packs',          [PackController::class, 'index'])->name('packs.index');
    Route::put('packs/{pack}',   [PackController::class, 'update'])->name('packs.update');
    Route::patch('packs/{pack}/toggle',
                 [PackController::class, 'toggleActive'])->name('packs.toggle');

    // Paramètres
    Route::get('settings',       [SettingController::class, 'index'])->name('settings.index');
    Route::put('settings',       [SettingController::class, 'update'])->name('settings.update');

    // Résultats
    Route::get('results',        [ResultController::class, 'index'])->name('results.index');
    Route::post('results/toggle',[ResultController::class, 'toggleVisibility'])->name('results.toggle');
});