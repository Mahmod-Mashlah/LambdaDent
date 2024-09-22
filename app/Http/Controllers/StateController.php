<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\User;
use App\Models\State;
use Illuminate\Http\Request;
use App\Traits\HttpResponses;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\CaseSearchRequest;
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

    public function search(CaseSearchRequest $request)
    {
        $query = State::query();
        $clients_query = User::query();

        if ((!$request->has('client_name')) && (!$request->has('patient_name')) && (!$request->has('expected_delivery_date')) && (!$request->has('status')) && (!$request->has('confirm_delivery')) && (!$request->has('created_date'))) {
            return $this->error(" please select what are you looking for", "No Search Results Found", 404);
        }
        if ($request->has('client_name')) {

            $users = User::all();
            foreach ($users as $user) {
                $clients_query->where('first_name', 'like', '%' . $request->input('client_name') . '%')
                    ->orWhere('last_name', 'like', '%' . $request->input('client_name') . '%');
            }
        }
        if ($request->has('patient_name')) {
            $query->where('patient_name', 'like', '%' . $request->input('patient_name') . '%');
        }
        if ($request->has('expected_delivery_date')) {
            $query->where('expected_delivery_date', $request->input('expected_delivery_date'));
        }
        if ($request->has('status')) {
            $query->where('status',  $request->input('status'));
        }
        if ($request->has('confirm_delivery')) {
            $query->where('confirm_delivery', $request->input('confirm_delivery'));
        }
        if ($request->has('created_date')) {
            $query->where('created_at',  $request->input('created_date'));
        }
        $clients_ids_array = $clients_query->pluck("id")->toArray();
        // dd($clients_ids_array);
        $states_from_clients = State::whereIn("client_id", $clients_ids_array)->get();
        // dd($states_from_clients);

        $result = $query->get();
        $intersectedStatesResult = $result->intersect($states_from_clients);
        $intersectedStatesCount = $intersectedStatesResult->count();
        if ($intersectedStatesCount == 0 /*$intersectedStatesResult->empty()*/) {
            return $this->error(" please try another search", "No Search Results Found", 404);
        }
        return $this->success([
            // "clients" => $states_from_clients,
            // "results" => $result,
            "count_of_states_found" => $intersectedStatesCount,
            "intersectedStatesResult" => $intersectedStatesResult,
        ], 'Searching done', 200);
    }
    public function search_by_client_name(Request $request)
    {
        $clients_query = User::query();
        $users = User::all();
        foreach ($users as $user) {
            $clients_query->where('first_name', 'like', '%' . $request->input('client_name') . '%')
                ->orWhere('last_name', 'like', '%' . $request->input('client_name') . '%');
        }
        $clients_ids_array = $clients_query->pluck("id")->toArray();
        $states_from_clients = State::whereIn("client_id", $clients_ids_array)->get();
        $result_count = $states_from_clients->count();

        if ($result_count == 0 || $request->input('client_name') == null) {
            return $this->error(" please try another search input", "No Search Results Found", 404);
        }
        return $this->success([
            "count_of_states_found" => $result_count,
            "result" => $states_from_clients,
        ], 'Searching done', 200);
    }
    public function search_by_patient_name(Request $request)
    {
        $query = State::query();
        if ($request->has('patient_name')) {
            $query->where('patient_name', 'like', '%' . $request->input('patient_name') . '%');
        }
        $result = $query->get();
        $result_count = $result->count();

        if ($result_count == 0 || $request->input('patient_name') == null) {
            return $this->error(" please try another search input", "No Search Results Found", 404);
        }
        return $this->success([
            "count_of_states_found" => $result_count,
            "result" => $result,
        ], 'Searching done', 200);
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
            'status' => 0, //"pending"
            'confirm_delivery' => false,

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

        if ($files !== null) {

            foreach ($files as $file) {

                $filename =  $file->getClientOriginalName();

                $file_name_existed = File::where('name', $filename)->exists();
                if ($file_name_existed) {
                    $state->delete();
                    return $this->error("Rename the file " . $filename . " please & try again", "the File name is already taken", 404);
                }

                $image = File::create([
                    'name' => $filename,
                    'is_case_image' => false,
                    'case_id' => $state->id
                ]);

                $file->move(public_path("project-files"), $filename);
            }
        }

        $case_screenshot_image = $request->file('case_screenshot');

        $screenshot_image_name =  $case_screenshot_image->getClientOriginalName();

        // $file_name_existed = File::where('name', $screenshot_image_name)->exists();
        // if ($file_name_existed) {
        //     return $this->error("Rename the file" . $screenshot_image_name . "please", "the File name is already taken", 404);
        // }

        $screenshot_image = File::create([
            'name' => $screenshot_image_name,
            'is_case_image' => true,
            'case_id' => $state->id
        ]);

        $case_screenshot_image->move(public_path("project-files"), $screenshot_image_name);


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
    public function change_status(ChangeCaseStatusRequest $request)
    {
        $case_id = $request->case_id;
        $state = State::find($case_id);

        if ($state->status == 4) //"delivered"
        {
            return $this->error("you can't change 'delivered' status ", "Error", 404);
        } else {
            $state->status = (int) $request->new_status;
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

        if ($state->status == 3 /*"ready"*/ &&  $state->client_id == auth()->user()->id) {
            $state->status = 4; //"delivered"
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

        if ($state->status == 0/*"pending"*/ &&  $state->client_id == auth()->user()->id) {
            $state->delete();
            return $this->success([], "Case deleted successfully");
        }
        return $this->error(["you can't delete this case"], "unfortunately", 422);
    }
}
