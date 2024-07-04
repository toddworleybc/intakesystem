<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Models\Clients;
use App\Mail\WelcomeEmail;
use App\Models\Payment;
use App\Mail\SendPayment;
use App\Mail\SendReceipt;
use Illuminate\Support\Number;



class SendEmailsController extends Controller
{
    //
    



    public function viewEmail(Request $request) {
      

            $client = (object) $request->client;
            $payment = (object) $request->payment;

            $clientFormattedForEmail = $this->formatNumberToCurrency($client, 'client');
            $paymentFormattedForEmail = $this->formatNumberToCurrency($payment, 'payment');

            if($request->view === 'welcome_email') {

                return view('Emails/welcomeEmail', [
                    'client' => $clientFormattedForEmail,
                    'payment' => $paymentFormattedForEmail,
                    'view' => true
                ]);

            }

            if($request->view === 'payment_email') {

                return view('Emails/sendPayment', [
                    'client' =>  $clientFormattedForEmail,
                    'payment' => $paymentFormattedForEmail,
                    'view' => true
                ]);

            }


            if($request->view === 'receipt_email') {

                return view('Emails/receiptSend', [
                    'client' => $clientFormattedForEmail,
                    'payment' => $paymentFormattedForEmail,
                    'view' => true
                ]);

            }

    }//

// Send welcome email

    public function welcomeEmailSend(Request $request) {

       


        $client = Clients::find($request->id);
        $initialPaymentCreated = $client->payments()->where("initial_payment", 1)->first();
     
                
// set welcome email sent count
        $client->welcome_email_sent_count = $this->setWelcomeEmailSentCount($client);


// add payment count
        $this->setPaymentSentCount($initialPaymentCreated);


        $clientFormattedForEmail = $this->formatNumberToCurrency($client, 'client');
        $initialPaymentFormattedForEmail = $this->formatNumberToCurrency($initialPaymentCreated, 'payment');


    // Send Email
        Mail::to($client->email)->send(new WelcomeEmail($clientFormattedForEmail, $initialPaymentFormattedForEmail));


        return to_route('clients.show', $client)->with('success', "Welcome email has been sent!");

    }
    //==



    /**
     * Send created payment.
     */

    public function sendPayment(Request $request) {

        $payment = Payment::find($request->id);

// add sent count
    $this->setPaymentSentCount($payment);
 

        $payment->amount = Number::currency($payment->amount);

        if($payment->payment_method === 'Credit Card') {
            $payment->card_amount = Number::currency($payment->card_amount);
            $payment->processing_fee = Number::currency($payment->processing_fee);
        }

// send payment email
        Mail::to($payment->client->email)->send(new SendPayment($payment));

        return to_route('payments.show', $payment->id)->with('success', 'Payment sent!');
 
     }//



      /**
     * Send Receipt.
     */
    public function sendReceipt(Request $request) {

        $payment = Payment::find($request->id);

// add sent count
        $this->setReceiptSentCount($payment);
 
        $payment->amount = Number::currency($payment->amount);

        if($payment->payment_method === 'Credit Card') {
            $payment->card_amount = Number::currency($payment->card_amount);
            $payment->processing_fee = Number::currency($payment->processing_fee);
        }
       

// send payment email
        Mail::to($payment->client->email)->send(new SendReceipt($payment));

        return to_route('payments.show', $payment->id)->with('success', 'Receipt sent!');
 
     }//


//=============================


   
/**
     @param $format Client or Payment to $format
     @param $for declare either Client or Payment
     @return The client or payment collection
*/
     private function formatNumberToCurrency($format, $for) {

        if($for === 'client') {

            $format->quote = Number::currency($format->quote);
            $format->deposit = Number::currency($format->deposit);

        }


        if($for === 'payment') {

            $format->amount = Number::currency($format->amount);

            if($format->card_amount) $format->card_amount = Number::currency($format->card_amount);

            if($format->processing_fee) $format->processing_fee = Number::currency($format->processing_fee);


        }

        return $format;

     }//==


     private function setPaymentSentCount($payment) {

        $payment = Payment::find($payment->id);

        $paymentDates = json_decode($payment->payment_sent_count);
 
        $paymentDates[] = now()->format("F jS, Y, g:i a");

        $payment->payment_sent_count = json_encode($paymentDates); 
 
        $payment->save();

     }//

     private function setReceiptSentCount($payment) {

        $payment = Payment::find($payment->id);

        $receiptDates = json_decode($payment->receipt_sent_dates);
 
        $receiptDates[] = now()->format("F jS, Y, g:i a");

        $payment->receipt_sent_dates = json_encode($receiptDates); 
 
        $payment->save();

     }//


     private function setWelcomeEmailSentCount($client) {

        $client->welcome_email_sent = true;

        $welcomeEmailCount = json_decode($client->welcome_email_sent_count);

        $welcomeEmailCount[] = now()->format("F jS, Y, g:i a");;

        $client->welcome_email_sent_count = json_encode($welcomeEmailCount);

        $client->save();

        return json_decode($client->welcome_email_sent_count);

     }//

}
