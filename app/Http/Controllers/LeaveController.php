<?php

namespace App\Http\Controllers;

use App\Http\Services\LeaveService;
use App\Models\leave;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeaveController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.leave.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */

    public function  refetchEvents(Request $request)
    {
        $leaveService = new LeaveService(auth()->user());
        $leaveData = $leaveService->allLeave($request->all());
        return response()->json($leaveData);

    }
    public function store(Request $request)
    {
        $this->validate($request,[
            'start' => 'required',
            'end' => 'required',
            'type' => 'required',
        ]);
        $data = $request->all();
        $data['user_id'] = auth()->user()->id;
        $leaveService = new LeaveService(auth()->user());
        $leave = $leaveService->create($data);
       if($leave) {
           return response()->json([
               'success' =>true
           ]);
       }else{
           return response()->json([
                   'success' =>false
           ]);
       }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'start' => 'required',
            'end' => 'required',
            'type' => 'required',
        ]);

        $data = $request->all();

        $leave = Leave::find($id); // Assuming 'Leave' is your model
        if (!$leave) {
            return response()->json([
                'success' => false,
            ]);
        }

        $leave->fill($data);
        $leave->save();

        return response()->json([
            'success' => true,
        ]);
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
//        dd($id);
        $setting = leave::find($id);
        $setting->delete();
    }
}
