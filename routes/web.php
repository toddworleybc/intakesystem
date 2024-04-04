<?php

use App\Http\Controllers\ClientsController;
use App\Http\Controllers\SendEmailsController;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get("/", function () {
    return Inertia::render("Clients/Read");
})->name('clients.index');




Route::resource('clients', ClientsController::class);

Route::resource('payments', PaymentController::class);

Route::post('payments/create-new-payment', [PaymentController::class,'createNewPayment'])->name('payments.create.new');

Route::post('welcome-email', [SendEmailsController::class,'welcomeEmailSend'])->name('welcome.email.send');


// Emails View TESTING

// Route::get('email-test', function() {
//     return view('Emails/welcomeEmail');
// });


   





