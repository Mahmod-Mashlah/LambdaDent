<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    use HttpResponses;
    public function show_accepted_clients()
    {
        $registered_clients = User::Where("register_accepted", true)
            ->where('type', 'client')->get();

        if (Auth::user()->type == "admin") {
            return $this->success([
                "registered_clients" => $registered_clients,
            ], message: "Accepted Clients");
        } else {
            return $this->error([], "Unauthenticated", 401);
        }
    }

    public function show_clients_requests()
    {
        $clients_requests = User::Where("register_accepted", false)
            ->where('type', 'client')->get();

        if (Auth::user()->type == "admin") {
            return $this->success([
                "clients_requests" => $clients_requests,
            ], message: "Clients Requests");
        } else {
            return $this->error([], "Unauthenticated", 401);
        }
    }

    public function show_client_details($client_id)
    {
        $client = User::find($client_id);

        if (Auth::user()->type == "admin") {
            return $this->success([
                "client_details" => $client,
            ], message: "Clients Requests");
        } else {
            return $this->error([], "Unauthenticated", 401);
        }
    }
    public function accept_client(Request $request)
    {
        $client_id =  $request->client_id;
        $client = User::find($client_id);
        if (Auth::User()->type == "admin") {

            $client->register_accepted = 1; // 1 == true
            $client->save();
            // dd($client);

            return $this->success([
                "client" => $client,
            ], message: "Client Request Accepted Successfully");
        } else {
            return $this->error([], "Unauthenticated", 401);
        }
    }

    public function decline_client(Request $request)
    {
        $client_id =  $request->client_id;
        $client = User::find($client_id);
        if (Auth::User()->type == "admin") {
            if ($client->register_accepted == false) {

                $client->delete();

                return $this->success([],  "Client Request Deleted Successfully");
            }
            return $this->error([],  "Error,this client is accepted, you can't delete him", 422);
        } else {
            return $this->error([], "Unauthenticated", 401);
        }
    }
}
