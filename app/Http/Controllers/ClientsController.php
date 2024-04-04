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


class ClientsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Clients $clients)
    {
        //
      
       
        
        $allClients = $clients->all()->reverse()->values();
        
        // dd($allClients);


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
                     
            
        // create request deposit
        $clientStore['deposit'] = Number::format(($request->quote / 2), precision: 2);

            
        $clientStore['quote'] = $clientStore['quote'] ? $clientStore['quote'] : '0.00';

        // create client
            $client = Clients::create($clientStore);


        // create an initial
        if($client->create_quote) {

            $order = [
                'client' => $client,
                'amount' => $client->deposit,
                'notes' => 'Initial deposit for services',
                'frequency' => 'one_time'
            ];

        // create initial payment  
            $payment->store($order, true);

        } // create quote
        
        

          return redirect()->route('clients.show', ['client'=> $client, 'client_created' => true]);

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
        
       

     // Create and array for domains   
        $client->domains = Str::of($client->domains)->split('/[\s,]+/');
        
            
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

        return Inertia::render("Clients/Edit", $client);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateClientsRequest $request, Clients $clients, $id)
    {
        //


        $client = Clients::find($id);

        $client->update($request->all());

        return to_route("clients.show", [
            $client->id,
            "client_updated" => true
        ]);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Clients $clients, $id)
    {
        //

        $client = Clients::find($id);

        $client->delete();

       return to_route("clients.index", [
        "clientDeleted" => true
       ]);

    }
    
}



