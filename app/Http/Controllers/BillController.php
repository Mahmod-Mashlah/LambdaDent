<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\State;
use App\Models\BillCase;
use Illuminate\Support\Arr;
use App\Traits\HttpResponses;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\StoreBillRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class BillController extends Controller
{
    use HttpResponses;
    public function add_bill(StoreBillRequest $request)
    {

        $states_between_dates = State::whereBetween('created_at', [$request->date_from, $request->date_to])
            ->where("client_id", $request->client_id)->get();

        if ($states_between_dates->isEmpty() === true) {
            return $this->error(" There are no cases in this date , plaease change the date", "Error", 422);
        }

        $all_bill_cases_ids = BillCase::pluck("case_id")->toArray();

        foreach ($states_between_dates as $state) {
            if ($state->cost == 0) {
                // dd($state->id);
                return $this->error("case " . $state->id . " has no cost ", "Error", 422);
            }
            if (in_array($state->id, $all_bill_cases_ids)) // OR : Arr::exists($all_bill_cases_ids, $state->id )
            {
                return $this->error("case " . $state->id . " is exists in other bill , plaease change the date", "Error", 422);
            }
        }
        // dd($states_between_dates);

        // DB::transaction(function () use ($request, $states_between_dates) {

        $bill = Bill::create([

            'client_id' => $request->client_id,

            'date_from' => $request->date_from,
            'date_to' => $request->date_to,

        ]);
        $total_cost = 0;
        foreach ($states_between_dates as $state) {
            $bill_cases = BillCase::create([

                'bill_id' => $bill->id,
                'case_id' => $state->id,
                'case_cost' => $state->cost
            ]);
            $total_cost += $state->cost;
        }

        $bill->update([
            'code_number' => "0" . $request->client_id . "-0" . $bill->id,
            'total_cost' => $total_cost, // 😁😁😁😁😁😁😁😁😁😁😁😀😀
        ]);
        // $bill_cases = BillCase::where();
        // $bill_cases = BillCase::findMany();
        return $this->success([
            "bill" => $bill/*->load("bill_cases", "client")*/,
            "bill_cases" => $states_between_dates,
        ], "Bill added successfully");
        // }); // end of transaction

    }

    public function show_bill_details($bill_id)
    {
        $bill = Bill::find($bill_id);
        $bill_cases_ids = BillCase::where("bill_id", $bill_id)->pluck("case_id");
        $cases = State::findMany($bill_cases_ids);
        return $this->success([
            "bill" => $bill,
            "bill_cases" => $cases,
        ], "Bill " . $bill->code_number . " details");
    }
    public function show_client_bills($client_id)
    {
        $client = User::find($client_id);
        $bills = Bill::where("client_id", $client_id)
            ->orderBy('date_from', 'desc')->get();
        return $this->success([
            "client_bills" => $bills,
        ], "Bills for client : " . $client->first_name . " " . $client->last_name);
    }

    public function client_search_by_date($date)
    {

        $client_id = Auth::user()->id;

        $client_bills = Bill::where('client_id', $client_id)->get();

        $first_bill_date_from = Bill::where("client_id", $client_id)
            ->orderBy('date_from', 'asc')
            ->first()->date_from;

        $last_bill_date_to = Bill::where("client_id", $client_id)
            ->orderBy('date_to', 'desc')
            ->first()->date_to;

        foreach ($client_bills as $bill) {
            $date = Carbon::parse($date);
            $date_from = Carbon::parse($bill->date_from);
            $date_to = Carbon::parse($bill->date_to);

            if ($date->between($date_from, $date_to)) // instead ,you can use : $bill->date_from <= $date && $bill->date_to >= $date
                return $this->success([
                    "result" => $bill,
                ], "Result found");
        }

        // if ($bill_result->isEmpty() === true) {  // this 'if' is wrong ,I am just trying
        return $this->error("no bill found, plaease enter a date between " . $first_bill_date_from . " and " . $last_bill_date_to, "Error", 404);
        // }
    }
}
