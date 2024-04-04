<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Models\Clients;
use App\Mail\WelcomeEmail;
use App\Http\Controllers\PaymentController;



class SendEmailsController extends Controller
{
    //
    


    public function welcomeEmailSend(Request $request, PaymentController $payment) {

        $client = Clients::find($request->id);
        $initialPaymentCreated = $client->payments()->where("initial_payment", 1)->first();
     

    // Send Email
        Mail::to($client->email)->send(new WelcomeEmail($client, $initialPaymentCreated));

        $client->welcome_email_sent = true;
        $client->save();
        
        return redirect()->route('clients.show', ['client'=> $client->id, 'emailSent' => true]);


    }
    //==



}
