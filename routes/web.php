<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\JobController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
    Route::get('/customers/create', [CustomerController::class, 'create'])->name('customers.create');
    Route::post('/customers', [CustomerController::class, 'store'])->name('customers.store');
    Route::get('/customers/{customer}', [CustomerController::class, 'show'])->name('customers.show');
    Route::get('/customers/{customer}/edit', [CustomerController::class, 'edit'])->name('customers.edit');
    Route::put('/customers/{customer}', [CustomerController::class, 'update'])->name('customers.update');

    Route::get('/jobs', [JobController::class, 'index'])->name('jobs.index');
    Route::get('/jobs/calendar', [JobController::class, 'calendar'])->name('jobs.calendar');
    Route::get('/jobs/create', [JobController::class, 'create'])->name('jobs.create');
    Route::post('/jobs', [JobController::class, 'store'])->name('jobs.store');
    Route::get('/jobs/{job}', [JobController::class, 'show'])->name('jobs.show');
    Route::get('/jobs/{job}/edit', [JobController::class, 'edit'])->name('jobs.edit');
    Route::put('/jobs/{job}', [JobController::class, 'update'])->name('jobs.update');
    Route::post('/jobs/{job}/confirm', [JobController::class, 'confirm'])->name('jobs.confirm');
    Route::post('/jobs/{job}/schedule', [JobController::class, 'schedule'])->name('jobs.schedule');
    Route::post('/jobs/{job}/dispatch', [JobController::class, 'markDispatched'])->name('jobs.dispatch.mark');
    Route::post('/jobs/{job}/complete', [JobController::class, 'complete'])->name('jobs.complete');
    Route::post('/jobs/{job}/payment', [JobController::class, 'payment'])->name('jobs.payment');
    Route::post('/jobs/{job}/notes', [JobController::class, 'storeNote'])->name('jobs.notes.store');
    Route::get('/jobs/{job}/dispatch', [JobController::class, 'dispatch'])->name('jobs.dispatch');
    Route::get('/jobs/{job}/dispatch/print', [JobController::class, 'printDispatch'])->name('jobs.dispatch.print');
    Route::get('/jobs/{job}/quote', [JobController::class, 'quote'])->name('jobs.quote');
    Route::get('/jobs/{job}/quote/download', [JobController::class, 'downloadQuote'])->name('jobs.quote.download');
    Route::get('/jobs/{job}/invoice', [JobController::class, 'invoice'])->name('jobs.invoice');
    Route::get('/jobs/{job}/invoice/download', [JobController::class, 'downloadInvoice'])->name('jobs.invoice.download');
    Route::delete('/jobs/{job}', [JobController::class, 'destroy'])->name('jobs.destroy');
    Route::get('/deleted-jobs', [JobController::class, 'deleted'])->name('jobs.deleted');
    Route::get('/deleted-jobs/{job}', [JobController::class, 'showDeleted'])->name('jobs.deleted.show')->withTrashed();
    Route::post('/deleted-jobs/{job}/restore', [JobController::class, 'restore'])->name('jobs.restore')->withTrashed();

});

require __DIR__.'/auth.php';
