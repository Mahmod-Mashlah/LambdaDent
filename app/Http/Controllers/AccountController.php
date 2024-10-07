<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Http\Requests\StoreAccountRequest;
use App\Http\Requests\UpdateAccountRequest;
use App\Models\User;
use App\Traits\HttpResponses;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{
    use HttpResponses;
    public function show_account_history($client_id)
    {
        $account_history =
            Account::where('client_id', $client_id)
            // ->whereNotNull('bill_id')
            ->orderBy('created_at', 'desc')
            ->get();

        return $this->success([
            "account_history" => $account_history,

        ], "Account History");
    }

    /**
     * Show the form for creating a new resource.
     */
    public function increase_account(StoreAccountRequest $request)
    {
        $user = Auth::user();
        // dd($user->type != 'admin');
        if ($user->type != 'admin') {
            return $this->error([], "Unauthenticated", 401);
        }
        $previous_client_account_value =
            Account::where('client_id', $request->client_id)
            ->orderBy('created_at', 'desc')->first()->current_account;

        $account = Account::create([

            'client_id' => $request->client_id,
            'bill_id' => null,

            'type' => "إضافة رصيد",
            'note' => $request->note,
            'signed_value' => $request->value,
            'current_account' => $previous_client_account_value + $request->value

        ]);
        return $this->success([
            "account" => $account/*->load("bill_cases", "client")*/,

        ], "Account value added successfully");
    }
}
