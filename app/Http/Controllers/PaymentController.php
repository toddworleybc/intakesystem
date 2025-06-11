<?php

namespace App\Http\Controllers;

use App\Models\Clients;
use App\Models\Payment;
use Illuminate\Http\Request;

use Illuminate\Support\Number;
use Illuminate\Support\Str;
use Inertia\Inertia;

class PaymentController extends Controller
{

   


    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // 

        $payments = Payment::all();
        $payments_created_at = [];
        $processing_fee = (float) config('services.stripe.processing_fee');


        foreach($payments as $payment) {
            $payments_created_at[] = [
                'id' => $payment->id,
                'created_date' => $payment->created_at
            ];
        }



        return inertia('Payments/Read', [
            'payments' => Payment::all(),
            'clients' => Clients::all(),
            'payments_created_at' => $payments_created_at,
            'processing_fee' => $processing_fee
        ]);


    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        //

        $client = Clients::find($request->query("clientId"));
        $client['payments'] = $client->payments;
       
        

        return Inertia::render("Payments/Create", [
            'client' => $client
        ]);
    }

   

    /**
     * Store a newly created resource in storage.
     */


    public function store(Request $request)
    {
        // 
      
        $client = Clients::find($request->client["id"]);
        $message = null;
        $paymentCreatedStatus = null;
        $recurringPayment = $request['frequency'] === "recurring" ? true : false;


// add additional request information  
        $request["payment_method"] = $recurringPayment ? 'Credit Card' : $client["payment_option"];
        $request["for"] = $request["for"] ? $request["for"] : "EBD Online Marketing Services";
        $request['notes'] = $request['notes'] ? $request["notes"] : null;
        $request["invoice_id"] = Str::of(Str::ulid())->substr(16);
        $request["status"] = 'pending';
        $request["payment_sent_count"] = [];
        $request["receipt_sent_dates"] = [];

        
    // create Credit Card Link
        if($client["payment_option"] === "Credit Card" || $recurringPayment) {

        // add processing fee
            $request["processing_fee"] = $request->amount * config('services.stripe.processing_fee');

        // add card amount
            $request["card_amount"] = $request->amount + $request->processing_fee;


        // add and create payment link
            $request["payment_link"] = $this->CreatePaymentLink($request);

        }

        

// Create the payment
        $paymentCreated = $client->payments()->create($request->all());


    // set payment created status message
        $paymentCreatedStatus = $paymentCreated ?
        [
            'status' => 'success',
            'type' => 'safe',
            'message' => "Payment for {$client["name"]} has been created"
        ] :
        [
            'status' => 'error',
            'type' => 'danger',
            'message' => "Failed to create payment for {$client["name"]}"
        ];

        return to_route("payments.show", $paymentCreated)->with('message', $paymentCreatedStatus);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        //
      
        $payment = Payment::find($id);
        $payment['client'] = $payment->client;
        $payment['client_payments'] = $payment['client']->payments;
        $created_at = $payment->created_at;
        $updated_at = $payment->updated_at;

        $payment->amount = Number::currency($payment->amount);

        if($payment->payment_method === 'Credit Card') {
            $payment->card_amount = Number::currency($payment->card_amount);
            $payment->processing_fee = Number::currency($payment->processing_fee);
        }
        
       
        return inertia("Payments/Show", [
            'payment' => $payment,
            'created_at' => $created_at,
            'updated_at' => $updated_at
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
    public function update(Request $request, $id)
    {
        //
      
        $payment = Payment::find($id);

        $payment->status = $request->status;
        $payment->notes = $request->notes;
        $payment->payment_welcome_email = $request->payment_welcome_email;

      

        $paymentSaved = $payment->save();

        $payment['client'] = $payment->client;

        $paymentUpdateStatus = $paymentSaved ?
        [
            'status' => 'success',
            'type' => 'safe',
            'message' => "Payment for {$payment->client->name} has been updated"
        ] :
        [
            'status' => 'error',
            'type' => 'danger',
            'message' => "Failed to update payment for {$payment->client->name}"
        ];

        return to_route("payments.show", $id)->with('message', $paymentUpdateStatus);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Payment $payment)
    {
        //
    }

    private function CreatePaymentLink($order) {

    // Automatically sets Stripe into either "test" or "live" depending on local or production enviroment
        $stripe = config("app.env") === "local" ? 
            new \Stripe\StripeClient(config('services.stripe.test')) : 
            new \Stripe\StripeClient(config('services.stripe.live'));


    // set card ammount format
        $card_amount = (int) ($order["card_amount"] * 100);
       
        if($order["frequency"] === 'one_time') {
            $service_fee = $stripe->prices->create([
                'currency' => 'usd',
                'unit_amount' => $card_amount,
                'product_data' => [
                    'name' => "{$order['for']}. {$order['name']}-(invoice_id: {$order['invoice_id']})",
                ]
            ]);
        }

        
        if($order["frequency"] === "recurring") {
           
              $service_fee = $stripe->prices->create([
                'currency' => 'usd',
                'unit_amount' => $card_amount,
                'product_data' => [
                    'name' => "{$order['for']}. {$order['name']}-(invoice_id: {$order['invoice_id']})",
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

          return $product["url"];

    }//==
}
