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


            if($request->view === 'welcome_email') {

                return view('Emails/welcomeEmail', [
                    'client' => $client,
                    'payment' => $payment,
                    'view' => true
                ]);

            }

            if($request->view === 'payment_email') {

                return view('Emails/sendPayment', [
                    'client' =>  $client,
                    'payment' => $payment,
                    'view' => true
                ]);

            }


            if($request->view === 'receipt_email') {

                return view('Emails/receiptSend', [
                    'client' => $client,
                    'payment' => $payment,
                    'view' => true
                ]);

            }

    }//

// Send welcome email

    public function welcomeEmailSend(Request $request) {

       

        $client = Clients::find($request->id);
        $payment = $client->payments()->where("payment_welcome_email", 1)->first();

        // non converted payment for set payment count
        $nonConverted = Payment::find($payment->id);
     
        $payment->amount = Number::currency($payment->amount);

        if($payment->payment_method === 'Credit Card') {
            $payment->card_amount = Number::currency($payment->card_amount);
            $payment->processing_fee = Number::currency($payment->processing_fee);
        }


    // Send Email
       $welcomeEmailSent = Mail::to($client->email)->send(new WelcomeEmail($client, $payment));



        if($welcomeEmailSent) {
            // set welcome email sent count
                $client->welcome_email_sent_count = $this->setWelcomeEmailSentCount($client);

           
            // add sent payment count 
                $this->setPaymentSentCount($nonConverted);            
        
        }



        // set message and status
        $welcomeEmailSentStatus = $welcomeEmailSent ? 
        [
            'status' => 'success',
            'type' => 'safe',
            'message' => "Welcome email sent to {$client->name}!"
        ] :
        [
            'status' => 'error',
            'type' => 'danger',
            'message' => "Failed to send welcome email {$client->name}!"
        ];  


        return to_route('clients.show', $client)->with('message', $welcomeEmailSentStatus);

    }
    //==



    /**
     * Send created payment.
     */

    public function sendPayment(Request $request) {

        $payment = Payment::find($request->id);
        $client = Clients::find($payment->client->id);
 
         // add sent count
        $this->setPaymentSentCount($payment);

       $payment->amount = Number::currency($payment->amount);

        if($payment->payment_method === 'Credit Card') {
            $payment->card_amount = Number::currency($payment->card_amount);
            $payment->processing_fee = Number::currency($payment->processing_fee);
        }


// send payment email
       $paymentSent = Mail::to($payment->client->email)->send(new SendPayment($payment));

   

        // set message and status
        $paymentSentStatus = $paymentSent ? 
        [
            'status' => 'success',
            'type' => 'safe',
            'message' => "Payment sent to {$client->name}!"
        ] :
        [
            'status' => 'error',
            'type' => 'danger',
            'message' => "Failed to send payment to {$client->name}!"
        ];  

        return to_route('payments.show', $payment->id)->with('message', $paymentSentStatus);
 
     }//



      /**
     * Send Receipt.
     */
    public function sendReceipt(Request $request) {

        $payment = Payment::find($request->id);
        $client = Clients::find($payment->clients_id);
        
// add sent count
        $this->setReceiptSentCount($payment);
 
        $payment->amount = Number::currency($payment->amount);

        if($payment->payment_method === 'Credit Card') {
            $payment->card_amount = Number::currency($payment->card_amount);
            $payment->processing_fee = Number::currency($payment->processing_fee);
        }
       

// send payment email
      $receiptSent =  Mail::to($payment->client->email)->send(new SendReceipt($payment));

      $receiptSentStatus = $receiptSent ? 
      [
          'status' => 'success',
          'type' => 'safe',
          'message' => "Receipt sent to {$client->name}!"
      ] :
      [
          'status' => 'error',
          'type' => 'danger',
          'message' => "Failed to send receipt to {$client->name}!"
      ];  

        return to_route('payments.show', $payment->id)->with('message', $receiptSentStatus);
 
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

        $paymentDates = $payment->payment_sent_count;
 
        $paymentDates[] = (string) now()->format("F jS, Y, g:i a");

        $payment->payment_sent_count = $paymentDates; 
 
        $payment->save();

     }//

     private function setReceiptSentCount($payment) {


        $receiptDates = $payment->receipt_sent_dates;
 
        $receiptDates[] = (string) now()->format("F jS, Y, g:i a");

        $payment->receipt_sent_dates = $receiptDates; 
 
        $payment->save();

     }//


     private function setWelcomeEmailSentCount($client) {
       

        $welcomeEmailCount = $client->welcome_email_sent_count;

        $welcomeEmailCount[] = (string) now()->format("F jS, Y, g:i a");;

        $client->welcome_email_sent_count = $welcomeEmailCount;

        $client->save();

        return $client->welcome_email_sent_count;

     }//

}
