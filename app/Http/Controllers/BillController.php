<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\State;
use App\Models\BillCase;
use Illuminate\Support\Arr;
use App\Traits\HttpResponses;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\StoreBillRequest;

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
}
