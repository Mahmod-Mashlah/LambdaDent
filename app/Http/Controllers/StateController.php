<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\User;
use App\Models\State;
use Illuminate\Http\Request;
use App\Traits\HttpResponses;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreStateRequest;
use Illuminate\Support\Facades\Response;
use App\Http\Requests\UpdateStateRequest;
use App\Http\Requests\ChangeCaseStatusRequest;

class StateController extends Controller
{
    use HttpResponses;
    public function index()
    {
        $states = State::query()->orderBy('created_at', 'desc')->paginate(20);
        $states_count = DB::table('states')->count();

        return $this->success([
            'cases_count' => $states_count,
            'cases' => $states,
        ], "All cases");
    }

    public function show_client_cases($client_id)
    {
        $client = User::find($client_id);
        $states = State::Where('client_id', $client_id)->orderBy('created_at', 'desc')->with('images')->paginate(20);
        return $this->success([
            // 'images by files' => $images,
            'cases' => $states,
            // 'images by relations' => $state->images,
        ], "Cases for client : " . $client->first_name . " " . $client->first_name);
    }

    public function add(StoreStateRequest $request)
    {

        // $request->validated($request->all());

        $client = Auth::user();

        $state = State::create([
            'client_id' => $client->id,

            'patient_name' => $request->patient_name,
            'age' => $request->age,
            'gender' => $request->gender,
            'need_trial' => $request->need_trial,
            'repeat' => $request->repeat,
            'shade' => $request->shade,
            'expected_delivery_date' => $request->expected_delivery_date,
            'notes' => $request->notes,
            'status' => "pending",
            'confirm_delivery' => $request->confirm_delivery,

            'teeth_crown' => $request->teeth_crown,
            'teeth_pontic' => $request->teeth_pontic,
            'teeth_implant' => $request->teeth_implant,
            'teeth_veneer' => $request->teeth_veneer,
            'teeth_inlay' => $request->teeth_inlay,
            'teeth_denture' => $request->teeth_denture,

            'bridges_crown' => $request->bridges_crown,
            'bridges_pontic' => $request->bridges_pontic,
            'bridges_implant' => $request->bridges_implant,
            'bridges_veneer' => $request->bridges_veneer,
            'bridges_inlay' => $request->bridges_inlay,
            'bridges_denture' => $request->bridges_denture


        ]);

        $files = $request->file('images');
        // dd($files);
        foreach ($files as $file) {

            $filename =  $file->getClientOriginalName();

            // $file_name_existed = File::where('name', $filename)->exists();
            // if ($file_name_existed) {
            //     return $this->error("Rename the file" . $filename . "please", "the File name is already taken", 404);
            // }

            $image = File::create([
                'name' => $filename,
                'case_id' => $state->id
            ]);

            $file->move(public_path("project-files"), $filename);
        }
        // $images = File::Where('case_id', $state->id)->get();
        return $this->success([
            // 'images by files' => $images,
            'case' => $state->load('images'),
            // 'images by relations' => $state->images,
        ], "Case added successfully, waiting for admin approval");
    }
    public function downloadFile($file_id)
    {
        $file = File::find($file_id);
        $file_path = public_path('project-files/' . $file->name);
        if (file_exists($file_path)) {
            return Response::download($file_path, $file->name);
        }
        return $this->error("", "File not found", 404);
    }
    public function show_case_details($case_id)
    {
        $state = State::find($case_id);
        return $this->success([
            // 'images by files' => $images,
            'case' => $state->load('images'),
            // 'images by relations' => $state->images,
        ], "Case details");
    }

    public function show(State $state)
    {
        //
    }

    public function edit(State $state)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateStateRequest $request, State $state)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function change_status(ChangeCaseStatusRequest $request)
    {
        $case_id = $request->case_id;
        $state = State::find($case_id);

        if ($state->status == "delivered") {
            return $this->error("you can't change 'delivered' status ", "Error", 404);
        } else {
            $state->status = $request->new_status;
            $state->save();
            return $this->success([
                "case" => $state
            ], "Status changed successfully");
        }
    }
    public function confirm_delivery(Request $request)
    {
        $case_id = $request->case_id;
        $state = State::find($case_id);

        if ($state->status == "ready" &&  $state->client_id == auth()->user()->id) {
            $state->status = "delivered";
            $state->save();
            return $this->success([
                "case" => $state
            ], "Case confirmed successfully");
        }
        return $this->error(["you can't confirm this case"], "unfortunately", 422);
    }
    public function delete_request(Request $request)
    {
        $case_id = $request->case_id;
        $state = State::find($case_id);

        if ($state->status == "pending" &&  $state->client_id == auth()->user()->id) {
            $state->delete();
            return $this->success([], "Case deleted successfully");
        }
        return $this->error(["you can't delete this case"], "unfortunately", 422);
    }
    public function destroy(State $state)
    {
        //
    }
}
