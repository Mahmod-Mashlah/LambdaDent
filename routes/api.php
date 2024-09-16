<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController /*as ApiAuthController */;
use App\Http\Controllers\StateController;
use App\Http\Controllers\UserController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

// Route::prefix()-> group(['middleware'=>['auth:sanctum']],function () {} //to implement prefix

// public Routes _____________________________________________________________________________

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// protected Routes (With Sanctum Auth) ______________________________________________________

Route::group(['middleware' => ['auth:sanctum']], function () {

    // Years السنوات الدراسية
    //Route::resource('/years', ApiYearController::class)/*->only(['index', 'show'])*/;
    //Route::get('/seasons/seasons_by_year_id/{year_id}', [ApiSeasonController::class, 'index']);

    Route::prefix('clients')->group(function () {

        Route::get('/show-accepted-clients', [UserController::class, 'show_accepted_clients']);
        Route::get('/show-clients-requests', [UserController::class, 'show_clients_requests']);
        Route::get('/show-client-details/{client_id}', [UserController::class, 'show_client_details']);
        Route::post('/accept-client', [UserController::class, 'accept_client']);
        Route::post('/decline-client', [UserController::class, 'decline_client']);
    });
    // Cases

    Route::prefix('cases')->group(function () {


        Route::get('/show-all-cases', [StateController::class, 'index']);  // admin do this
        Route::get('/show-client-cases', [StateController::class, 'show_client_details']);  // admin and client do this
        Route::post('/add', [StateController::class, 'add']); // admin and client do this 😎
        Route::get('/show-case-details/{case_id}', [StateController::class, 'show_client_details']);  // admin and client do this
        Route::post('/request-cancellation', [StateController::class, 'delete']); // client do this
        Route::post('/confirm-delivery', [StateController::class, 'delete']); // client do this
        Route::post('/change-status', [StateController::class, 'delete']); // admin do this

        Route::get('/download-case-image/{file_id}', [StateController::class, 'downloadFile']); // admin and client do this

        // Search

    });



    // Logout
});
