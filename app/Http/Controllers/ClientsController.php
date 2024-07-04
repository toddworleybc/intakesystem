<?php

namespace App\Http\Controllers;
use Illuminate\Support\Str;

use App\Models\Clients;
use App\Models\Payment;
use App\Http\Requests\StoreClientsRequest;
use App\Http\Requests\UpdateClientsRequest;
use App\Http\Controllers\PaymentController;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Number;
use Illuminate\Support\Arr;


class ClientsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Clients $clients)
    {
        //
      
       
        
        $allClients = $clients->all()->reverse()->values();
        
    


        return Inertia::render("Clients/Read", ["clients" => $allClients]);

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return Inertia::render("Clients/Create");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreClientsRequest $request, PaymentController $payment)
    {
        //
        
        

        if($clientStore = $request->validated()) {

            $clientStore['welcome_email_sent_count'] = json_encode([]);
            
            
    // create request deposit
        $clientStore['deposit'] = Number::format(($request->quote / 2), precision: 2);

        $clientStore['deposit'] = preg_replace('/,/', '', $clientStore['deposit']);
       

            
        $clientStore['quote'] = $clientStore['quote'] ? $clientStore['quote'] : '0.00';

    // format domain to json
            $clientStore['domains'] = json_encode($request->domains);

    // format pro_emails to json
   
            $clientStore['pro_emails'] = json_encode($request->pro_emails);
           
    // create client
            $client = Clients::create($clientStore);


    // create an initial quote
        if($client->create_quote) {

            $order = [
                'client' => $client,
                'amount' => $client->deposit,
                'notes' => 'Initial deposit for services',
                'frequency' => 'one_time',
                'receipt_sent_dates' => []
            ];

    // create initial payment  
            $payment->store($order, true);

        } // create quote
        

        return to_route('clients.show', $client)->with('success', "Client has been created!");

        }

    }

    /**
     * Display the specified resource.
     */
    public function show(Clients $clients, $id, Request $request)
    {   

       
       
        $client = Clients::find($id);


    // controls the message banner
        $client['created'] = $request->query("client_created") ? true : false;
        $client['updated'] = $request->query("client_updated") ? true : false;

    // sets date format for display
        $client["createdAt"] = $client->created_at->format("F jS, Y, g:i a");
        $client["updatedAt"] = $client->updated_at->format("F jS, Y, g:i a");


    // Reverse payments for table display
        $payments = $client->payments->reverse()->values();
        
    // domains back to array format
        $client["domains"] = json_decode($client["domains"]);

    // pro_emails back to array format
        $client["pro_emails"] = json_decode($client['pro_emails']);
        $client["welcome_email_sent_count"] = json_decode($client["welcome_email_sent_count"]);

            
        return Inertia::render("Clients/Show", [
          'client' =>  $client,
          'payments' => $payments
        ], );

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Clients $clients, $id)
    {
        //
        $client = Clients::find($id);

        $client['domains'] = json_decode($client["domains"]);

        $client['pro_emails'] = json_decode($client["pro_emails"]);


        return Inertia::render("Clients/Edit", $client);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateClientsRequest $request, Clients $clients, $id, Payment $payments)
    {
        //

        $client = Clients::find($id);

        
// restore client if cancelled 
        if($client->status === 'cancelled') {
           
            $client->status = 'pending';
        
            // save client
           $client->save();


            return to_route("clients.show",$client)->with('success', 'Client has been restored');
        }
        

// Void Pending Payments for Cancel Client
        if( $request->status === 'cancelled' ) {

            foreach($client->payments as $payment) {

                if($payment->status === 'pending') {

                    $payment = Payment::find($payment->id);

                    $payment->status = 'void';

                    $payment->save();

                }

            }

        }//

// save client
        $client->update($request->all());

// Sets initial payment to pending if client is pending
        if($client->status === 'pending') {

            $payment = $payments->find($client->payments)->where('initial_payment', 1);

            $payment->status = 'pending';

            $payment->save();
        }

        return to_route("clients.show",$client)->with('success', 'Client has been updated!');

    }// end of update





    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Clients $clients, $id)
    {
        //

        $client = Clients::find($id);

        foreach( $client->payments as $payment ) {

            if($payment->status === "pending") {

                $payment->status = "void";

                $payment->save();

            }

        }       

        $client->delete();

       return to_route("clients.index")->with('success', "Client $client->name was deleted!");

    }
    
}



