<?php

use App\Http\Controllers\ClientsController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get("/", function () {
    return Inertia::render("Dashboard");
})->name('dashboard');

// Route::get("/intake", function () {
//     return Inertia::render("Intake");
// })->name('intake');

// Route::prefix('/clients')->group( function() {


    Route::resource('clients', ClientsController::class);


    // // Read =====/
    // Route::get("/", function () {
    //     return Inertia::render("Clients/Read");
    // })->name('clients');

    // // Create ====/ 
    // Route::get("/create", function () {
    //     return Inertia::render("Clients/Create");
    // })->name('clients.create');

    // // Edit ====/ 

// } );





Route::get("/emails", function () {
    return Inertia::render("Emails");
})->name('emails');
