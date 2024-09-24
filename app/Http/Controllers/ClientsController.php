<?php

namespace App\Http\Controllers;


use App\Models\Clients;
use App\Models\Payment;
use Inertia\Inertia;
use App\Http\Requests\ClientRequest;
use App\Http\Requests\ClientsUpdateRequest;



class ClientsController extends Controller
{


    /**
     * Display a listing of the resource.
     */
    public function index(Clients $clients)
    {
        //
      
       
         $allClients = $clients->orderBy('name', 'asc')->get()->all();



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
   

    public function store(ClientRequest $request)
    {
        //

        // vars
            $newClient = new Clients();
            $client = $request->all();
            $name = $request->name;
        
            
        // create client
            $createdClient = $newClient->create($client);

    // set message and status
         $createdClientStatus = $createdClient ? 
        [
            'status' => 'success',
            'type' => 'safe',
            'message' => "Successfully created client $name!"
        ] :
        [
            'status' => 'error',
            'type' => 'danger',
            'message' => "Failed to create client $name!"
        ];     

        // return to route
         return to_route("clients.show", $createdClient)->with('message', $createdClientStatus);

    }

    
    /**
     * Show single resource.
     */

    public function show(Clients $clients, $id)

    {   
        
        // dd(now());

        $client = $clients->find($id);
        $payments = array_reverse($client->payments->all());

        $dateTest = now('UTC')->format($client->created_at);
        $created_at = $client->created_at;
        $updated_at = $client->updated_at;
        $payments_created_at = [];

        foreach($payments as $payment) {
            $payments_created_at[] = [
                'id' => $payment->id,
                'created_date' => $payment->created_at
            ];
        }

        return inertia('Clients/Show', [
            'client' => $client,
            'payments' => $payments,
            'created_at' => $created_at,
            'updated_at' => $updated_at,
            'payments_created_at' => $payments_created_at
        ]);

       
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Clients $clients, $id)
    {
        //
        $client = $clients->find($id);


        return Inertia::render("Clients/Edit", $client);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ClientsUpdateRequest $request, $id)
    {
        //

        $client = Clients::find($id);
        $name = $client->name;

        
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
            $updatedClient = $client->update($request->all());

            // set message and status
            $clientUpdatedStatus = $updatedClient ? 
            [
                'status' => 'success',
                'type' => 'safe',
                'message' => "Successfully updated client $name!"
            ] :
            [
                'status' => 'error',
                'type' => 'danger',
                'message' => "Failed to update client $name!"
            ];     

        return to_route("clients.show",$client)->with('message', $clientUpdatedStatus);

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



