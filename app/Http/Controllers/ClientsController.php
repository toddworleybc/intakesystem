<?php

namespace App\Http\Controllers;

use App\Models\Clients;
use App\Http\Requests\StoreClientsRequest;
use App\Http\Requests\UpdateClientsRequest;
use Inertia\Inertia;
use Illuminate\Http\Request;


class ClientsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //

        return Inertia::render("Clients/Read");

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
    public function store(StoreClientsRequest $request)
    {
        //

        $client = Clients::create($request->validated());


        return to_route("clients.show", [
            $client->id,
            "client_created" => true
        ]);


    }

    /**
     * Display the specified resource.
     */
    public function show(Clients $clients, $id, Request $request)
    {   
       
        $client = Clients::find($id);

        
        $client['created'] = $request->query("client_created") ? true : false;


        $client["createdAt"] = $client->created_at->format("F jS, Y, g:i a");
        $client["updatedAt"] = $client->updated_at->format("F jS, Y, g:i a");
            
        return Inertia::render("Clients/Show", $client);

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Clients $clients)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateClientsRequest $request, Clients $clients)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Clients $clients)
    {
        //
    }
}
