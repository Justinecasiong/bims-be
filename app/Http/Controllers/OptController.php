<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Logs;
use App\Models\OptRecord;

class OptController extends Controller
{
    public function optRecords()
    {
        return response()->json(OptRecord::paginate(10), 200);
    }

    public function newOptRecord(Request $request)
    {
        $users = User::where('remember_token', $request->remember_token)->first();
        Logs::create([
            'user_id' => $users->id,
            'user' => $users->name,
            'action' => 'Added a opt record.'
        ]);
        return response()->json(OptRecord::create($request->except(['remember_token', 'search'])), 200);
    }
    
    public function updateOptRecord(Request $request, $id)
    {
        $users = User::where('remember_token', $request->remember_token)->first();
        Logs::create([
            'user_id' => $users->id,
            'user' => $users->name,
            'action' => 'Updated a opt record.'
        ]);
        $optRecord = OptRecord::find($id);
        $optRecord->update($request->except('remember_token'));
        return response()->json($optRecord, 200);
    }

    public function deleteOptRecord(Request $request, $id)
    {
        $users = User::where('remember_token', $request->remember_token)->first();
        Logs::create([
            'user_id' => $users->id,
            'user' => $users->name,
            'action' => 'Deleted a opt record.'
        ]);
        return response()->json(OptRecord::destroy($id), 200);
    }
}