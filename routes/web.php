<?php

use App\Http\Controllers\ClientsController;
use App\Http\Controllers\SendEmailsController;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Models\Clients;
use App\Models\Payment;



// Client Routes ----------------------/

    Route::get("/",[ClientsController::class, 'index']);

    Route::resource('clients', ClientsController::class);


// Payment Routes--------------------/

    Route::resource('payments', PaymentController::class);

    // Route::post('/create-new-payment', [PaymentController::class,'createNewPayment'])->name('payments.create.new');

// Email Routes---------------------------/
    Route::post('/welcome-email', [SendEmailsController::class,'welcomeEmailSend'])->name('welcome.email.send');

    Route::post('/send-payment-email', [SendEmailsController::class, 'sendPayment'])->name('payment.send');


    Route::post('/send-receipt', [SendEmailsController::class, 'sendReceipt'])->name('receipt.send');


    Route::post('/view-email', [SendEmailsController::class, 'viewEmail'])->name('view.email');

    
// Arcives------------------------/
    Route::get('archives', function () {

        $payments = Payment::all();
        $payments_created_at = [];

        
        foreach($payments as $payment) {
            $payments_created_at[] = [
                'id' => $payment->id,
                'created_date' => $payment->created_at
            ];
        }


    return inertia( 'Archives/Read', [
            'clients' => Clients::all(),
            'payments' => Payment::all(),
            'payments_created_at' => $payments_created_at
        ]);
    })->name('archives');
//   archives ===








