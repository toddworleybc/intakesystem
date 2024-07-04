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

        return Inertia::render('Payments/Read', [
            'payments' => Payment::all(),
            'clients' => Clients::all()
        ]);


    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        //

        $client = Clients::find($request->query("clientId"));
        $initialPayment = null;

        foreach($client->payments as $payment) {

            if($payment->initial_payment === 1) {
                $initialPayment = $payment;
            }

        }

        return Inertia::render("Payments/Create", [
            'client' => $client,
            'initialPayment' => $initialPayment
        ]);
    }

    public function createNewPayment(Request $request) {

            // store or 
                $payment = $this->store($request);


        return to_route('payments.show', $payment)->with('success', 'Payment created');
              

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
        

// set default for payment if for is not filed
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
            $processing_fee = $order['amount'] * .034;


        // set payment fields
            $payment->processing_fee  = $processing_fee;
            $payment->card_amount = $order['amount'] + $processing_fee;
            
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
        $payment->payment_sent_count = json_encode([]);
        $payment->receipt_sent_dates = json_encode([]);

        if($payment->payment_method === "Credit Card") {
            $payment->card_amount =  Number::format($payment->card_amount, precision: 2);
            $payment->processing_fee =  Number::format($payment->processing_fee, precision: 2);

           $payment->card_amount = preg_replace('/,/', "", $payment->card_amount);
           $payment->processing_fee = preg_replace('/,/', "", $payment->processing_fee);
    
        }

        


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

        $payment["payment_sent_count"] = json_decode($payment->payment_sent_count);
        
        $payment["receipt_sent_dates"] = json_decode($payment->receipt_sent_dates);

      

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
    public function update(Request $request, Payment $payment, Clients $clients)
    {
        //
       
       
        $payment = $payment->find($request->id);

        $payment->status = $request->status;
        $payment->notes = $request->notes;

        $payment->save();

// activate client after intial payment
        if($payment->initial_payment && $payment->status === 'paid') {
  
            $client = $clients::find($payment->client->id);

            $client->status = 'active';

            $client->save();

        }

        if($payment->initial_payment && $payment->status === 'void') {
            $client = $clients->find($payment->client->id);
            $client->status = 'cancelled';
            $client->save();
        }

        


        $payment['client'] = $payment->client;

        
        
        return to_route('payments.show', $payment)->with('success', 'Payment has been updated!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Payment $payment)
    {
        //
    }

    private function CreatePayment($order) {


        $stripe = new \Stripe\StripeClient(config('services.stripe.key'));



        $order['amount'] = (float) preg_replace('/,/', "", $order['amount']);
        $order['amount'] = $order['amount'] * 100;


        if($order['frequency'] === 'one_time') {
            $service_fee = $stripe->prices->create([
                'currency' => 'usd',
                'unit_amount' => (int) $order['amount'],
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
