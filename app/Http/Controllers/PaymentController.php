<?php

namespace App\Http\Controllers;

use App\Mail\SendPayment;
use App\Models\Clients;
use App\Models\Payment;
use Illuminate\Http\Request;
use App\Http\Requests\StorePaymentRequest;
use App\Http\Requests\UpdatePaymentRequest;
use Illuminate\Support\Number;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Illuminate\Support\Facades\Mail;

class PaymentController extends Controller
{

   


    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        //

        $client = Clients::find($request->query("clientId"));

        return Inertia::render("Payments/Create", [
            'client' => $client
        ]);
    }

    public function createNewPayment(Request $request) {


        
        
 

            if($request->resendPayment) {

            // find payment to resend
                $payment = Payment::find($request->paymentId);
                $createdPayment = false;

            } else {

            // store or 
                $payment = $this->store($request);
                $createdPayment = true;
            }
        

    // send payment email
       Mail::to($payment->client->email)->send(new SendPayment($payment));


        return redirect()->route('payments.show', [
          'payment' => $payment->id,
          'createdPayment' => $createdPayment
        ]);


    }//#===

    /**
     * Store a newly created resource in storage.
     */
    public function store($order, $initial_payment = false)
    {
        //
        
      

    // create new payment
        $payment = new Payment();

      
        $client = $order['client'];
      

        $order['for'] = $initial_payment ? "50% Deposit for EBD Website and Graphic Design Services" : $order['for'];
        


        $order['for'] = $order['for'] ? $order['for'] : "EBD Website and Graphic Services";

        

    // get client if not initial payment
        if($initial_payment === false) $client = Clients::find($client['id']);



    // create payment id
        $payment->invoice_id = Str::of(Str::ulid())->substr(16);

        

    // set payment "for"
        $payment->for = $initial_payment ? "EBD 50% Initial Service Deposit" : $order->for;

    // if recurring payment is created use Credit Card payment only!
        if($order['frequency'] === 'recurring') $client->payment_option = "Credit Card";
            

    // credit card payment
        if($client->payment_option === "Credit Card") {

        // create processing fee 
            $processing_fee = Number::format($order['amount'] * .034, precision: 2);


        // set payment fields
            $payment->processing_fee  = $processing_fee;
            $payment->card_amount = Number::format($order['amount'] + $processing_fee, precision: 2);
            
            $productOrder = [
                'name' => $client->name, 
                'amount' => $payment->card_amount, 
                'for' => $payment->for,
                'frequency' => $initial_payment ? 'one_time' : $order['frequency'], 
                'invoiceId' => $payment->invoice_id
            ];

    

    // create payment "product" obj 
           $product = $this->CreatePayment($productOrder);

        

        // store payment link
           $payment->payment_link = $product->url;

        }// end if;
       

    // store the rest of payment data
       
        $payment->payment_method = $client->payment_option;
        $payment->amount = $order['amount'];
        $payment->notes = $order['notes'];
        $payment->initial_payment = $initial_payment;
        $payment->frequency = $order['frequency'];



// save payment
        $client->payments()->save($payment);


    // save payment
        return $payment;
            
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        //
      
        $payment = Payment::find($id);
        $payment['client'] = $payment->client;

        $payment["createdAt"] = $payment->created_at->format("F jS, Y, g:i a");
        $payment["updatedAt"] = $payment->updated_at->format("F jS, Y, g:i a");


        return Inertia::render("Payments/Show", [
            'payment' => $payment
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Payment $payment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Payment $payment)
    {
        //
       
       
        $payment = $payment->find($request->id);

        $payment->status = $request->status;
        $payment->notes = $request->notes;

        $payment->save();

        $payment['client'] = $payment->client;
       
        
        return to_route('payments.show', [
            'payment' => $payment,
            'paymentUpdated' => true
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Payment $payment)
    {
        //
    }

    private function CreatePayment($order) {


        $stripe = new \Stripe\StripeClient(env("STRIPE_SECRET_KEY"));

      

        if($order['frequency'] === 'one_time') {
            $service_fee = $stripe->prices->create([
                'currency' => 'usd',
                'unit_amount' => $order['amount'] * 100,
                'product_data' => [
                    'name' => "{$order['for']}. {$order['name']}-(invoice_id: {$order['invoiceId']})",
                ]
            ]);
        }

        
        if($order['frequency'] === "recurring") {
           
              $service_fee = $stripe->prices->create([
                'currency' => 'usd',
                'unit_amount' => $order['amount'] * 100,
                'product_data' => [
                    'name' => "{$order['for']}. {$order['name']}-(invoice_id: {$order['invoiceId']})",
                ],
                'recurring' => [
                    'interval' => 'month',
                    'interval_count' => 1
                ]
              ]);
        }
       
       

        $product = $stripe->paymentLinks->create([
            'line_items' => [
              [
                'price' => $service_fee->id,
                'quantity' => 1,
              ],
            ],
          ]);

          return $product;

    }//==
}
