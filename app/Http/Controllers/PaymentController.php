<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Http\Requests\StorePaymentRequest;
use App\Http\Requests\TwoDatesRequest;
use App\Http\Requests\UpdatePaymentRequest;
use App\Models\Bill;
use App\Models\Item;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    use HttpResponses;
    public function index()
    {
        $payments = Payment::orderBy('created_at', 'desc')->paginate(20);
        $count = $payments->count();
        // or: $items = Item::latest()->get();

        return $this->success([
            "payments_count" => $count,
            "item_payments" => $payments
        ], "Payments History ");
    }
    public function show_item_payments($item_id)
    {
        $item = Item::find($item_id);
        $item_payments = Payment::where("item_id", $item_id)
            ->orderBy('created_at', 'desc')->paginate(20);
        $count = $item_payments->count();
        // or: $items = Item::latest()->get();

        return $this->success([
            "item" => $item,
            "payments_count" => $count,
            "item_payments" => $item_payments
        ], "Payments History for the Item : " . $item->name);
    }

    public function store(StorePaymentRequest $request)
    {
        $payment = Payment::create([

            'item_id' => $request->item_id,
            'quantity' => $request->quantity,
            'unit_price' => $request->unit_price,
            'date' => $request->date,

        ]);

        return $this->success([
            'payment' => $payment,
        ], "payment added successfully");
    }
    public function calculate_income_outcome_gain()
    {

        $payments = Payment::select('unit_price', 'quantity')->get();

        $payments_value = 0; // Admin Payments

        foreach ($payments as $payment) {

            $payments_value += $payment->quantity * $payment->unit_price;
        }

        $bills_value = (int) Bill::sum('total_cost');  // Clients paid Bills

        return $this->success([
            'payments_value' => $payments_value,
            'bills_value' => $bills_value,
            'total_gain' => $bills_value - $payments_value,
        ], "total gain");
    }
    public function calculateGainBetween2Dates(TwoDatesRequest $request)
    {
        $payments = Payment::whereBetween('date',  [$request->date_from, $request->date_to])->get();

        $payments_value = 0; // Admin Payments

        foreach ($payments as $payment) {

            $payments_value += $payment->quantity * $payment->unit_price;
        }

        $bills = Bill::whereBetween('date_from',   [$request->date_from, $request->date_to])
            ->whereBetween('date_to',   [$request->date_from, $request->date_to])
            ->get();

        $bills_value = (int) $bills->sum('total_cost');  // Clients paid Bills
        $bills_codes = $bills->pluck("code_number");
        // dd($bills_codes);
        return $this->success([
            'payments_value' => $payments_value,
            'bills_value' => $bills_value,
            'total_gain' => $bills_value - $payments_value,
            'bills_codes' => $bills_codes,
        ], "total gain between $request->date_from and $request->date_to");
    }
}
